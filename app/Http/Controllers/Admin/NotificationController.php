<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'filter' => ['nullable', 'string', 'in:all,unread'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        $filter = $validated['filter'] ?? 'all';
        $limit = $validated['limit'] ?? 12;

        $user = $request->user();

        $query = $user->notifications()->latest();
        if ($filter === 'unread') {
            $query->whereNull('read_at');
        }

        $items = $query->limit($limit)->get()->map(fn (DatabaseNotification $n) => $this->map($n));

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'items' => $items,
        ]);
    }

    public function markRead(Request $request, string $id)
    {
        $user = $request->user();
        /** @var DatabaseNotification|null $n */
        $n = $user->notifications()->where('id', $id)->first();
        if (!$n) {
            return response()->json(['message' => 'Notificación no encontrada'], 404);
        }

        $n->markAsRead();

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'notification' => $this->map($n->fresh()),
        ]);
    }

    public function markAllRead(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();

        return response()->json([
            'unread_count' => 0,
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        /** @var DatabaseNotification|null $n */
        $n = $user->notifications()->where('id', $id)->first();
        if (!$n) {
            return response()->json(['message' => 'Notificación no encontrada'], 404);
        }

        $n->delete();

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    public function stream(Request $request): StreamedResponse
    {
        $user = $request->user();
        $lastId = (string) $request->query('last_id', '');

        return response()->stream(function () use ($user, $lastId) {
            @ini_set('zlib.output_compression', 0);
            @ini_set('output_buffering', 'off');
            @ini_set('implicit_flush', 1);

            $start = time();
            $timeoutSeconds = 25;
            $pollMs = 1200;

            while ((time() - $start) < $timeoutSeconds) {
                $unreadCount = $user->unreadNotifications()->count();

                $latest = $user->notifications()->latest()->first();
                if ($latest && (string) $latest->id !== $lastId) {
                    $payload = [
                        'type' => 'update',
                        'last_id' => (string) $latest->id,
                        'unread_count' => $unreadCount,
                    ];
                    echo "event: notif\n";
                    echo 'data: ' . json_encode($payload) . "\n\n";
                    flush();
                    return;
                }

                echo "event: ping\n";
                echo 'data: ' . json_encode(['type' => 'ping', 'unread_count' => $unreadCount]) . "\n\n";
                flush();

                usleep($pollMs * 1000);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    private function map(DatabaseNotification $n): array
    {
        $data = is_array($n->data) ? $n->data : [];

        return [
            'id' => (string) $n->id,
            'read_at' => $n->read_at?->toIso8601String(),
            'created_at' => $n->created_at?->toIso8601String(),
            'title' => (string) ($data['title'] ?? 'Notificación'),
            'message' => (string) ($data['message'] ?? ''),
            'level' => (string) ($data['level'] ?? 'info'),
            'action_url' => $data['action_url'] ?? null,
            'action_label' => $data['action_label'] ?? null,
        ];
    }
}
