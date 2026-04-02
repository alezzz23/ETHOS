<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\KnowledgeBaseEntry;
use App\Models\RestrictedTopic;

class LandingAssistantController extends Controller
{
    private function buildSystemPrompt(string $userQuery): string
    {
        $base = 'Eres el asistente virtual de ETHOS. Responde en español claro, breve y profesional. Resuelve preguntas frecuentes sobre servicios, precios, horarios, compra y soporte técnico. Si falta información exacta, dilo con transparencia y sugiere contacto humano.';

        $entries = KnowledgeBaseEntry::search($userQuery, 3);
        if ($entries->isNotEmpty()) {
            $base .= "\n\nINFORMACIÓN DE ETHOS RELEVANTE:\n";
            foreach ($entries as $entry) {
                $base .= "--- {$entry->title} ---\n";
                $base .= ($entry->embedding_summary ?? substr($entry->content, 0, 300)) . "\n\n";
            }
        }

        return $base;
    }
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1200'],
            'history' => ['nullable', 'array', 'max:20'],
            'history.*.role' => ['required_with:history', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:1200'],
        ]);

        $apiKey = (string) config('services.ai_assistant.api_key');
        $configuredBaseUrl = trim((string) config('services.ai_assistant.base_url'));
        $baseUrl = $configuredBaseUrl !== '' ? rtrim($configuredBaseUrl, '/') : 'https://openrouter.ai/api/v1';
        $model = (string) config('services.ai_assistant.model', 'nvidia/nemotron-3-super-120b-a12b:free');
        $timeout = (int) config('services.ai_assistant.timeout', 30);

        if ($apiKey === '') {
            return response()->json([
                'message' => 'Asistente no disponible temporalmente.',
            ], 503);
        }

        // ── Restricted topics check ────────────────────────────────
        $userMessage = trim($validated['message']);
        $blocked = RestrictedTopic::active()->get()
            ->first(fn (RestrictedTopic $t): bool => $t->matches($userMessage));

        if ($blocked) {
            return response()->json(['reply' => $blocked->response_message]);
        }

        $history = collect($validated['history'] ?? [])
            ->map(function (array $item): array {
                return [
                    'role' => $item['role'],
                    'content' => trim($item['content']),
                ];
            })
            ->filter(fn (array $item): bool => $item['content'] !== '')
            ->take(-10)
            ->values()
            ->all();

        $messages = array_merge(
            [[
                'role' => 'system',
                'content' => $this->buildSystemPrompt($userMessage),
            ]],
            $history,
            [[
                'role' => 'user',
                'content' => $userMessage,
            ]]
        );

        $response = Http::timeout($timeout)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name', 'ETHOS'),
            ])
            ->post($baseUrl . '/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.4,
                'max_tokens' => 500,
            ]);

        if (! $response->successful()) {
            return response()->json([
                'message' => 'No se pudo obtener respuesta del asistente.',
            ], 503);
        }

        $reply = trim((string) data_get($response->json(), 'choices.0.message.content', ''));

        if ($reply === '') {
            return response()->json([
                'message' => 'Respuesta vacía del proveedor IA.',
            ], 502);
        }

        $sessionHistory = array_slice(array_merge(
            $history,
            [[
                'role' => 'user',
                'content' => $userMessage,
            ]],
            [[
                'role' => 'assistant',
                'content' => $reply,
            ]]
        ), -20);

        $request->session()->put('assistant_history', $sessionHistory);

        return response()->json([
            'reply' => $reply,
        ]);
    }

    public function clearHistory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cleared_count' => ['nullable', 'integer', 'min:0'],
        ]);

        $clearedCount = (int) ($validated['cleared_count'] ?? 0);
        $sessionId = $request->session()->getId();
        $request->session()->forget('assistant_history');

        Log::info('assistant_chat_cleared', [
            'session_id' => $sessionId,
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 250),
            'cleared_count' => $clearedCount,
        ]);

        return response()->json([
            'cleared' => true,
        ]);
    }
}
