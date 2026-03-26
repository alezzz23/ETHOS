<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminChatLog;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DashboardChatController extends Controller
{
    // ── Render stats context for the system prompt ──────────────────
    private function buildSystemPrompt(): string
    {
        $now = now()->timezone(config('app.timezone', 'America/Caracas'));

        $stats = [
            'total_usuarios'  => User::count(),
            'total_clientes'  => class_exists(Client::class) ? Client::count() : 0,
            'total_proyectos' => class_exists(Project::class) ? Project::count() : 0,
        ];

        return <<<PROMPT
Eres ETHOS AI, el asistente inteligente exclusivo del panel de administración de ETHOS.

CONTEXTO ACTUAL DEL SISTEMA ({$now->format('d/m/Y H:i')}):
- Usuarios registrados: {$stats['total_usuarios']}
- Clientes registrados: {$stats['total_clientes']}
- Proyectos registrados: {$stats['total_proyectos']}

ROL Y CAPACIDADES:
- Ayudas a los administradores con análisis de datos, gestión de usuarios, proyectos y clientes.
- Interpretas y explicas estadísticas y métricas del sistema.
- Orientas sobre configuración y operaciones del panel.
- Redactas borradores de comunicaciones o reportes.
- Sugieres acciones concretas y pasos de solución a problemas operativos.
- Puedes responder en español o inglés según el idioma del usuario.

LIMITACIONES HONESTAS:
- No tienes acceso directo a leer o modificar la base de datos en tiempo real, solo estadísticas generales.
- Si necesitas hacer algo que requiere acceso real, indica claramente el camino a seguir.

ESTILO:
- Respuestas claras, estructuradas y profesionales.
- Usa listas y formato Markdown para mayor claridad cuando sea apropiado.
- Sé conciso pero completo.
PROMPT;
    }

    // ── Chat endpoint ────────────────────────────────────────────────
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'history' => ['nullable', 'array', 'max:30'],
            'history.*.role'    => ['required_with:history', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:2000'],
        ]);

        /** @var \App\Models\User $user */
        $user    = $request->user();
        $apiKey  = (string) config('services.ai_dashboard.api_key', '');
        $baseUrl = rtrim((string) config('services.ai_dashboard.base_url', 'https://openrouter.ai/api/v1'), '/');
        $model   = (string) config('services.ai_dashboard.model', 'nvidia/llama-nemotron-super-49b-v1:free');
        $timeout = (int) config('services.ai_dashboard.timeout', 30);

        if ($apiKey === '') {
            return response()->json(['message' => 'Asistente no disponible temporalmente.'], 503);
        }

        $history = collect($validated['history'] ?? [])
            ->map(fn (array $m): array => [
                'role'    => $m['role'],
                'content' => trim($m['content']),
            ])
            ->filter(fn (array $m): bool => $m['content'] !== '')
            ->take(-20)
            ->values()
            ->all();

        $messages = array_merge(
            [['role' => 'system', 'content' => $this->buildSystemPrompt()]],
            $history,
            [['role' => 'user', 'content' => trim($validated['message'])]]
        );

        $startMs = (int) round(microtime(true) * 1000);

        $response = Http::timeout($timeout)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
                'HTTP-Referer'  => config('app.url'),
                'X-Title'       => config('app.name', 'ETHOS Admin'),
            ])
            ->post($baseUrl . '/chat/completions', [
                'model'       => $model,
                'messages'    => $messages,
                'temperature' => 0.55,
                'max_tokens'  => 1200,
            ]);

        $responseMs = (int) round(microtime(true) * 1000) - $startMs;

        if (!$response->successful()) {
            Log::warning('admin_chatbot_api_error', [
                'user_id' => $user->id,
                'status'  => $response->status(),
                'body'    => substr($response->body(), 0, 500),
            ]);
            return response()->json(['message' => 'No se pudo obtener respuesta del asistente.'], 503);
        }

        $reply  = trim((string) data_get($response->json(), 'choices.0.message.content', ''));
        $tokens = (int) data_get($response->json(), 'usage.total_tokens', 0);

        if ($reply === '') {
            return response()->json(['message' => 'Respuesta vacía del proveedor IA.'], 502);
        }

        // ── Audit log ─────────────────────────────────────────────
        $sessionId  = $request->session()->getId();
        $ip         = $request->ip();
        $ua         = substr((string) $request->userAgent(), 0, 300);

        AdminChatLog::create([
            'user_id'     => $user->id,
            'session_id'  => $sessionId,
            'role'        => 'user',
            'content'     => trim($validated['message']),
            'model'       => $model,
            'ip_address'  => $ip,
            'user_agent'  => $ua,
        ]);
        AdminChatLog::create([
            'user_id'     => $user->id,
            'session_id'  => $sessionId,
            'role'        => 'assistant',
            'content'     => $reply,
            'model'       => $model,
            'tokens_used' => $tokens,
            'response_ms' => $responseMs,
            'ip_address'  => $ip,
            'user_agent'  => $ua,
        ]);

        Log::info('admin_chatbot_interaction', [
            'user_id'     => $user->id,
            'model'       => $model,
            'tokens'      => $tokens,
            'response_ms' => $responseMs,
        ]);

        // ── Update session history ────────────────────────────────
        $sessionHistory = array_slice(array_merge(
            $history,
            [['role' => 'user',      'content' => trim($validated['message'])]],
            [['role' => 'assistant', 'content' => $reply]]
        ), -30);

        $request->session()->put('admin_chat_history', $sessionHistory);

        return response()->json([
            'reply'       => $reply,
            'tokens_used' => $tokens,
            'response_ms' => $responseMs,
            'model'       => $model,
        ]);
    }

    // ── Clear history ────────────────────────────────────────────────
    public function clearHistory(Request $request): JsonResponse
    {
        $request->session()->forget('admin_chat_history');

        Log::info('admin_chatbot_history_cleared', [
            'user_id'    => $request->user()?->id,
            'session_id' => $request->session()->getId(),
            'ip'         => $request->ip(),
        ]);

        return response()->json(['cleared' => true]);
    }

    // ── Fetch audit logs (admin only) ────────────────────────────────
    public function auditLog(Request $request): JsonResponse
    {
        $logs = AdminChatLog::with('user:id,name,email')
            ->orderByDesc('created_at')
            ->limit(200)
            ->get(['id', 'user_id', 'session_id', 'role', 'content', 'model', 'tokens_used', 'response_ms', 'ip_address', 'created_at']);

        return response()->json(['logs' => $logs]);
    }
}
