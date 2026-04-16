<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminChatLog;
use App\Models\ChatConversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChatConversationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $conversations = ChatConversation::query()
            ->forUser($request->user()->id)
            ->active()
            ->ordered()
            ->limit(60)
            ->get(['id', 'title', 'last_message_at', 'message_count', 'total_tokens', 'pinned', 'archived']);

        return response()->json(['conversations' => $conversations]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $conv = ChatConversation::query()
            ->forUser($request->user()->id)
            ->findOrFail($id);

        $messages = AdminChatLog::query()
            ->forConversation($id)
            ->orderBy('created_at')
            ->get(['id', 'role', 'content', 'model', 'tokens_used', 'response_ms', 'created_at']);

        return response()->json([
            'conversation' => $conv,
            'messages'     => $messages,
        ]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $conv = ChatConversation::query()
            ->forUser($request->user()->id)
            ->findOrFail($id);

        AdminChatLog::where('conversation_id', $id)->delete();
        $conv->delete();

        return response()->json(['deleted' => true]);
    }

    public function rename(Request $request, string $id): JsonResponse
    {
        $conv = ChatConversation::query()
            ->forUser($request->user()->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
        ]);

        $conv->update(['title' => $validated['title']]);

        return response()->json(['conversation' => $conv]);
    }

    public function export(Request $request, string $id): Response
    {
        $conv = ChatConversation::query()
            ->forUser($request->user()->id)
            ->findOrFail($id);

        $messages = AdminChatLog::query()
            ->forConversation($id)
            ->orderBy('created_at')
            ->get(['role', 'content', 'created_at']);

        $md  = "# " . ($conv->title ?? 'Conversación') . "\n\n";
        $md .= "_Exportado: " . now()->format('d/m/Y H:i') . "_\n\n---\n\n";
        foreach ($messages as $m) {
            $who = $m->role === 'user' ? '👤 **Tú**' : '🤖 **ETHOS AI**';
            $md .= $who . ' _(' . $m->created_at->format('d/m/Y H:i') . ")_\n\n";
            $md .= $m->content . "\n\n---\n\n";
        }

        $slug = \Illuminate\Support\Str::slug($conv->title ?? 'conversacion', '_');
        $filename = "ethos_chat_{$slug}_" . now()->format('Ymd_His') . ".md";

        return response($md, 200, [
            'Content-Type'        => 'text/markdown; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
