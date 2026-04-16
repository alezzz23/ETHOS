<?php

namespace App\Services\Chat;

use App\Models\AdminChatLog;
use App\Models\ChatConversation;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Orquestador del chat admin.
 *
 * Responsabilidades:
 *  - Preparar payload (mensajes) para el LLM.
 *  - Asegurar una ChatConversation asociada.
 *  - Persistir AdminChatLog (user + assistant) y actualizar contadores.
 *  - Generar un título automático a partir del primer mensaje.
 */
class AdminChatService
{
    public function __construct(
        private SystemPromptBuilder $prompt,
        private RestrictedTopicGuard $guard,
    ) {}

    /**
     * Construye los mensajes completos listos para enviar al LLM.
     *
     * @param array<int,array{role:string,content:string}> $history
     */
    public function buildMessages(string $userMessage, array $history = []): array
    {
        $window = (int) config('chatbot.limits.history_window', 20);

        $history = collect($history)
            ->map(fn (array $m): array => [
                'role'    => $m['role'],
                'content' => trim($m['content']),
            ])
            ->filter(fn (array $m): bool => $m['content'] !== '')
            ->take(-$window)
            ->values()
            ->all();

        return array_merge(
            [['role' => 'system', 'content' => $this->prompt->build($userMessage)]],
            $history,
            [['role' => 'user', 'content' => $userMessage]],
        );
    }

    public function checkRestricted(string $message): ?\App\Models\RestrictedTopic
    {
        return $this->guard->check($message);
    }

    /**
     * Asegura que exista una ChatConversation para el usuario.
     * Si no hay conversationId o no existe/pertenece a otro user, crea una nueva.
     */
    public function ensureConversation(User $user, ?string $conversationId, string $firstUserMessage): ChatConversation
    {
        if ($conversationId) {
            $conv = ChatConversation::query()
                ->where('id', $conversationId)
                ->where('user_id', $user->id)
                ->first();
            if ($conv) return $conv;
        }

        return ChatConversation::create([
            'id'              => (string) Str::uuid(),
            'user_id'         => $user->id,
            'title'           => $this->autoTitle($firstUserMessage),
            'last_message_at' => now(),
            'message_count'   => 0,
            'total_tokens'    => 0,
        ]);
    }

    /**
     * Persiste user+assistant logs y actualiza la conversación.
     *
     * @return array{user:AdminChatLog, assistant:AdminChatLog, conversation:ChatConversation}
     */
    public function persistInteraction(
        User $user,
        ChatConversation $conversation,
        string $userMessage,
        string $reply,
        string $model,
        int $tokens,
        int $responseMs,
        string $sessionId,
        ?string $ip,
        ?string $userAgent,
    ): array {
        $ua = substr((string) $userAgent, 0, 300);

        $userLog = AdminChatLog::create([
            'user_id'         => $user->id,
            'session_id'      => $sessionId,
            'conversation_id' => $conversation->id,
            'role'            => 'user',
            'content'         => $userMessage,
            'model'           => $model,
            'ip_address'      => $ip,
            'user_agent'      => $ua,
        ]);

        $assistantLog = AdminChatLog::create([
            'user_id'         => $user->id,
            'session_id'      => $sessionId,
            'conversation_id' => $conversation->id,
            'role'            => 'assistant',
            'content'         => $reply,
            'model'           => $model,
            'tokens_used'     => $tokens,
            'response_ms'     => $responseMs,
            'ip_address'      => $ip,
            'user_agent'      => $ua,
        ]);

        $conversation->forceFill([
            'last_message_at' => now(),
            'message_count'   => $conversation->message_count + 2,
            'total_tokens'    => $conversation->total_tokens + $tokens,
        ])->save();

        Log::info('admin_chatbot_interaction', [
            'user_id'         => $user->id,
            'conversation_id' => $conversation->id,
            'model'           => $model,
            'tokens'          => $tokens,
            'response_ms'     => $responseMs,
        ]);

        return ['user' => $userLog, 'assistant' => $assistantLog, 'conversation' => $conversation->fresh()];
    }

    private function autoTitle(string $message): string
    {
        $t = trim(preg_replace('/\s+/u', ' ', $message));
        return Str::limit($t, 60, '…');
    }
}
