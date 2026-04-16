<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Chat\ChatRequest;
use App\Http\Requests\Admin\Chat\FeedbackRequest;
use App\Models\AdminChatLog;
use App\Models\ChatConversation;
use App\Models\ChatFeedback;
use App\Services\Chat\AdminChatService;
use App\Services\Chat\LlmClient;
use App\Services\Chat\Tools\ToolRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardChatController extends Controller
{
    public function __construct(
        private AdminChatService $chat,
        private LlmClient $llm,
        private ToolRegistry $tools,
    ) {}

    /**
     * Endpoint JSON no-streaming (fallback).
     *
     * @OA\Post(
     *     path="/admin/chat",
     *     tags={"AdminChat"},
     *     summary="Envía un mensaje al asistente IA (respuesta completa, no-streaming)",
     *     security={{"sessionAuth":{}, "csrfHeader":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"message"},
     *         @OA\Property(property="message", type="string", maxLength=2000),
     *         @OA\Property(property="conversation_id", type="string", format="uuid", nullable=true),
     *         @OA\Property(property="history", type="array", @OA\Items(type="object",
     *             @OA\Property(property="role", type="string", enum={"user","assistant"}),
     *             @OA\Property(property="content", type="string")))
     *     )),
     *     @OA\Response(response=200, description="Respuesta generada", @OA\JsonContent(
     *         @OA\Property(property="reply", type="string"),
     *         @OA\Property(property="tokens_used", type="integer"),
     *         @OA\Property(property="response_ms", type="integer"),
     *         @OA\Property(property="model", type="string"),
     *         @OA\Property(property="conversation_id", type="string", format="uuid"),
     *         @OA\Property(property="admin_chat_log_id", type="integer")
     *     )),
     *     @OA\Response(response=422, description="Validación"),
     *     @OA\Response(response=429, description="Cuota excedida (budget o throttle)"),
     *     @OA\Response(response=503, description="Proveedor IA no disponible")
     * )
     */
    public function chat(ChatRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user      = $request->user();

        if ($blocked = $this->chat->checkRestricted($validated['message'])) {
            return response()->json(['reply' => $blocked->response_message, 'blocked' => true]);
        }

        $conversation = $this->chat->ensureConversation($user, $validated['conversation_id'] ?? null, $validated['message']);
        $messages     = $this->chat->buildMessages($validated['message'], $validated['history'] ?? []);

        $toolSchemas = (bool) config('chatbot.tools.enabled', true) ? $this->tools->schemas() : [];
        $maxHops     = (int)  config('chatbot.tools.max_hops', 3);
        $totalTokens = 0;
        $totalMs     = 0;
        $lastModel   = (string) config('chatbot.llm.models.primary');
        $result      = null;

        for ($hop = 0; $hop <= $maxHops; $hop++) {
            $result = $this->llm->complete($messages, null, $toolSchemas);
            if (! $result['ok']) {
                return response()->json(['message' => 'No se pudo obtener respuesta del asistente.'], $result['status'] ?? 503);
            }
            $totalTokens += (int) ($result['tokens'] ?? 0);
            $totalMs     += (int) ($result['ms']     ?? 0);
            $lastModel    = $result['model'] ?? $lastModel;

            $toolCalls = (array) ($result['tool_calls'] ?? []);
            if (empty($toolCalls) || $hop === $maxHops) {
                break;
            }

            // Añadir assistant tool_calls + resultados al array de mensajes.
            $messages[] = [
                'role'       => 'assistant',
                'content'    => $result['reply'] ?: null,
                'tool_calls' => $toolCalls,
            ];

            foreach ($toolCalls as $tc) {
                $name   = (string) data_get($tc, 'function.name', '');
                $argsJs = (string) data_get($tc, 'function.arguments', '{}');
                $args   = json_decode($argsJs, true) ?: [];
                $out    = $this->tools->dispatch($name, $args, $user);

                $messages[] = [
                    'role'         => 'tool',
                    'tool_call_id' => (string) ($tc['id'] ?? ''),
                    'name'         => $name,
                    'content'      => json_encode($out, JSON_UNESCAPED_UNICODE),
                ];
            }
        }

        if (trim((string) $result['reply']) === '') {
            return response()->json(['message' => 'Respuesta vacía del proveedor IA.'], 502);
        }

        $persisted = $this->chat->persistInteraction(
            $user,
            $conversation,
            $validated['message'],
            $result['reply'],
            $lastModel,
            $totalTokens,
            $totalMs,
            $request->session()->getId(),
            $request->ip(),
            $request->userAgent(),
        );

        return response()->json([
            'reply'             => $result['reply'],
            'tokens_used'       => $totalTokens,
            'response_ms'       => $totalMs,
            'model'             => $lastModel,
            'conversation_id'   => $conversation->id,
            'admin_chat_log_id' => $persisted['assistant']->id,
        ]);
    }

    /**
     * SSE streaming.
     *
     * @OA\Post(
     *     path="/admin/chat/stream",
     *     tags={"AdminChat"},
     *     summary="Envía mensaje y recibe respuesta vía Server-Sent Events",
     *     description="Content-Type de respuesta: text/event-stream. Eventos: meta, delta, tool_call, tool_result, done, error.",
     *     security={{"sessionAuth":{}, "csrfHeader":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"message"},
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(property="conversation_id", type="string", nullable=true),
     *         @OA\Property(property="history", type="array", @OA\Items(type="object"))
     *     )),
     *     @OA\Response(response=200, description="Flujo SSE"),
     *     @OA\Response(response=429, description="Cuota excedida")
     * )
     */
    public function stream(ChatRequest $request): StreamedResponse|JsonResponse
    {
        $validated = $request->validated();
        $user      = $request->user();

        if ($blocked = $this->chat->checkRestricted($validated['message'])) {
            // Aun así devolvemos JSON: el front lo maneja igual.
            return response()->json(['reply' => $blocked->response_message, 'blocked' => true]);
        }

        $conversation = $this->chat->ensureConversation($user, $validated['conversation_id'] ?? null, $validated['message']);
        $messages     = $this->chat->buildMessages($validated['message'], $validated['history'] ?? []);
        $model        = (string) config('chatbot.llm.models.primary');

        $sessionId = $request->session()->getId();
        $ip        = $request->ip();
        $ua        = $request->userAgent();

        $userMessage = $validated['message'];

        $response = new StreamedResponse(function () use (
            $messages, $model, $conversation, $user, $userMessage, $sessionId, $ip, $ua
        ) {
            @ini_set('output_buffering', '0');
            @ini_set('zlib.output_compression', '0');
            while (ob_get_level() > 0) { ob_end_flush(); }

            $emit = function (array $data): void {
                echo 'data: ' . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n\n";
                if (function_exists('ob_flush')) { @ob_flush(); }
                flush();
            };

            $emit([
                'type'            => 'meta',
                'conversation_id' => $conversation->id,
                'model'           => $model,
            ]);

            $toolSchemas = (bool) config('chatbot.tools.enabled', true) ? $this->tools->schemas() : [];
            $maxHops     = (int)  config('chatbot.tools.max_hops', 3);
            $totalTokens = 0;
            $totalMs     = 0;
            $finalReply  = '';
            $result      = null;

            for ($hop = 0; $hop <= $maxHops; $hop++) {
                $result = $this->llm->stream(
                    $messages,
                    onDelta: function (string $delta) use ($emit) {
                        $emit(['type' => 'delta', 'content' => $delta]);
                    },
                    onUsage: null,
                    onError: function (string $msg) use ($emit) {
                        $emit(['type' => 'error', 'message' => $msg]);
                    },
                    shouldAbort: fn () => connection_aborted() === 1,
                    model: $model,
                    tools: $toolSchemas,
                );

                if (! $result['ok']) { return; }

                $totalTokens += (int) ($result['tokens'] ?? 0);
                $totalMs     += (int) ($result['ms']     ?? 0);
                $finalReply   = $result['reply'] ?: $finalReply;

                $toolCalls = (array) ($result['tool_calls'] ?? []);
                if (empty($toolCalls) || $hop === $maxHops) { break; }

                // Notificar al cliente de tool_calls ejecutándose.
                foreach ($toolCalls as $tc) {
                    $emit([
                        'type' => 'tool_call',
                        'name' => (string) data_get($tc, 'function.name', ''),
                    ]);
                }

                $messages[] = [
                    'role'       => 'assistant',
                    'content'    => $result['reply'] ?: null,
                    'tool_calls' => $toolCalls,
                ];

                foreach ($toolCalls as $tc) {
                    $name   = (string) data_get($tc, 'function.name', '');
                    $argsJs = (string) data_get($tc, 'function.arguments', '{}');
                    $args   = json_decode($argsJs, true) ?: [];
                    $out    = $this->tools->dispatch($name, $args, $user);

                    $messages[] = [
                        'role'         => 'tool',
                        'tool_call_id' => (string) ($tc['id'] ?? ''),
                        'name'         => $name,
                        'content'      => json_encode($out, JSON_UNESCAPED_UNICODE),
                    ];

                    $emit([
                        'type' => 'tool_result',
                        'name' => $name,
                    ]);
                }
            }

            if (trim($finalReply) === '') {
                $emit(['type' => 'error', 'message' => 'Respuesta vacía del proveedor IA.']);
                return;
            }

            $persisted = $this->chat->persistInteraction(
                $user,
                $conversation,
                $userMessage,
                $finalReply,
                $result['model'] ?? $model,
                $totalTokens,
                $totalMs,
                $sessionId,
                $ip,
                $ua,
            );

            $emit([
                'type'              => 'done',
                'admin_chat_log_id' => $persisted['assistant']->id,
                'conversation_id'   => $conversation->id,
                'tokens_used'       => $totalTokens,
                'response_ms'       => $totalMs,
            ]);
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }

    /**
     * @OA\Post(
     *     path="/admin/chat/feedback",
     *     tags={"AdminChat"},
     *     summary="Registra 👍/👎 de una respuesta",
     *     security={{"sessionAuth":{}, "csrfHeader":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"rating"},
     *         @OA\Property(property="rating", type="string", enum={"helpful","not_helpful"}),
     *         @OA\Property(property="admin_chat_log_id", type="integer", nullable=true),
     *         @OA\Property(property="context", type="string"),
     *         @OA\Property(property="user_message", type="string"),
     *         @OA\Property(property="assistant_message", type="string"),
     *         @OA\Property(property="improvement_note", type="string")
     *     )),
     *     @OA\Response(response=200, description="Feedback registrado"),
     *     @OA\Response(response=422, description="Validación")
     * )
     */
    public function feedback(FeedbackRequest $request): JsonResponse
    {
        $data = $request->validated();

        ChatFeedback::create([
            'admin_chat_log_id' => $data['admin_chat_log_id'] ?? null,
            'context'           => $data['context'] ?? 'dashboard',
            'rating'            => $data['rating'],
            'user_message'      => $data['user_message']      ?? null,
            'assistant_message' => $data['assistant_message'] ?? null,
            'improvement_note'  => $data['improvement_note']  ?? null,
            'session_id'        => $request->session()->getId(),
        ]);

        return response()->json(['message' => 'Feedback registrado.']);
    }

    /**
     * @OA\Post(
     *     path="/admin/chat/clear",
     *     tags={"AdminChat"},
     *     summary="Limpia historial de chat de la sesión actual",
     *     security={{"sessionAuth":{}, "csrfHeader":{}}},
     *     @OA\Response(response=200, description="Historial limpiado")
     * )
     */
    public function clearHistory(Request $request): JsonResponse
    {
        $request->session()->forget(['admin_chat_history', 'admin_chat_conversation_id']);

        Log::info('admin_chatbot_history_cleared', [
            'user_id'    => $request->user()?->id,
            'session_id' => $request->session()->getId(),
            'ip'         => $request->ip(),
        ]);

        return response()->json(['cleared' => true]);
    }

    /**
     * @OA\Get(
     *     path="/admin/chat/audit",
     *     tags={"AdminChat"},
     *     summary="Últimos 200 eventos del chat con auditoría (admin)",
     *     security={{"sessionAuth":{}}},
     *     @OA\Response(response=200, description="Listado de logs")
     * )
     */
    public function auditLog(Request $request): JsonResponse
    {
        $logs = AdminChatLog::with('user:id,name,email')
            ->orderByDesc('created_at')
            ->limit(200)
            ->get(['id', 'user_id', 'session_id', 'conversation_id', 'role', 'content', 'model', 'tokens_used', 'response_ms', 'ip_address', 'created_at']);

        return response()->json(['logs' => $logs]);
    }
}
