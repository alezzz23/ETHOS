<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminChatLog;
use App\Models\AiModelPricing;
use App\Models\ChatFeedback;
use App\Models\RestrictedTopic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Fase 3.16 — Dashboard de observabilidad del chatbot (solo rol 'owner').
 */
class AiUsageController extends Controller
{
    public function index()
    {
        return view('admin.ai-usage.index', [
            'summary' => $this->summary(),
        ]);
    }

    /**
     * Datos agregados para ApexCharts.
     */
    public function data(Request $request): JsonResponse
    {
        $days = max(1, min(90, (int) $request->query('days', 30)));
        $from = now()->subDays($days)->startOfDay();

        return response()->json([
            'summary'         => $this->summary($from),
            'tokens_by_day'   => $this->tokensByDay($from),
            'top_users'       => $this->topUsers($from),
            'top_models'      => $this->topModels($from),
            'feedback_ratio'  => $this->feedbackRatio($from),
            'restricted_hits' => $this->restrictedHits($from),
            'cost_estimate'   => $this->costEstimate($from),
        ]);
    }

    private function summary(?Carbon $from = null): array
    {
        $from ??= now()->subDays(30)->startOfDay();

        $totalTokens = (int) AdminChatLog::where('role', 'assistant')
            ->where('created_at', '>=', $from)->sum('tokens_used');

        return [
            'range_from'     => $from->toDateString(),
            'total_messages' => AdminChatLog::where('created_at', '>=', $from)->count(),
            'total_tokens'   => $totalTokens,
            'unique_users'   => AdminChatLog::where('created_at', '>=', $from)->distinct('user_id')->count('user_id'),
            'conversations'  => AdminChatLog::where('created_at', '>=', $from)->distinct('conversation_id')->count('conversation_id'),
        ];
    }

    private function tokensByDay(Carbon $from): array
    {
        return AdminChatLog::where('role', 'assistant')
            ->where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as day, SUM(tokens_used) as tokens, COUNT(*) as msgs')
            ->groupBy('day')->orderBy('day')
            ->get()
            ->map(fn ($r) => [
                'day'    => (string) $r->day,
                'tokens' => (int) $r->tokens,
                'msgs'   => (int) $r->msgs,
            ])->all();
    }

    private function topUsers(Carbon $from): array
    {
        return AdminChatLog::where('role', 'assistant')
            ->where('created_at', '>=', $from)
            ->selectRaw('user_id, SUM(tokens_used) as tokens, COUNT(*) as msgs')
            ->groupBy('user_id')->orderByDesc('tokens')->limit(10)
            ->with('user:id,name')
            ->get()
            ->map(fn ($r) => [
                'user_id' => $r->user_id,
                'name'    => $r->user?->name ?? ('Usuario ' . $r->user_id),
                'tokens'  => (int) $r->tokens,
                'msgs'    => (int) $r->msgs,
            ])->all();
    }

    private function topModels(Carbon $from): array
    {
        return AdminChatLog::where('role', 'assistant')
            ->where('created_at', '>=', $from)
            ->selectRaw('model, SUM(tokens_used) as tokens, COUNT(*) as msgs')
            ->groupBy('model')->orderByDesc('tokens')
            ->get()
            ->map(fn ($r) => [
                'model'  => $r->model ?: 'desconocido',
                'tokens' => (int) $r->tokens,
                'msgs'   => (int) $r->msgs,
            ])->all();
    }

    private function feedbackRatio(Carbon $from): array
    {
        $rows = ChatFeedback::where('created_at', '>=', $from)
            ->selectRaw('rating, COUNT(*) as c')
            ->groupBy('rating')->pluck('c', 'rating')->all();

        return [
            'helpful'     => (int) ($rows['helpful']     ?? 0),
            'not_helpful' => (int) ($rows['not_helpful'] ?? 0),
        ];
    }

    private function restrictedHits(Carbon $from): array
    {
        // Sin tabla dedicada de eventos: aproximamos leyendo del log.
        // Buscamos logs user_message que coincidan con keywords de RestrictedTopic.
        return DB::table('admin_chat_logs')
            ->where('role', 'user')
            ->where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as hits')
            ->where(function ($w) {
                $w->where('content', 'like', '%restring%')
                  ->orWhere('content', 'like', '%confidencial%');
            })
            ->groupBy('day')->orderBy('day')
            ->get()->map(fn ($r) => ['day' => $r->day, 'hits' => (int) $r->hits])->all();
    }

    private function costEstimate(Carbon $from): float
    {
        $rows = AdminChatLog::where('role', 'assistant')
            ->where('created_at', '>=', $from)
            ->selectRaw('model, SUM(tokens_used) as tokens')
            ->groupBy('model')->get();

        $pricing = AiModelPricing::where('is_active', true)->get()->keyBy('model');

        $cost = 0.0;
        foreach ($rows as $r) {
            $p = $pricing->get((string) $r->model);
            if (! $p) continue;
            // Asumimos split 60/40 prompt/completion si no tenemos detalle.
            $tokens = (int) $r->tokens;
            $cost += ($tokens * 0.6 / 1000) * (float) $p->prompt_cost_per_1k
                   + ($tokens * 0.4 / 1000) * (float) $p->completion_cost_per_1k;
        }
        return round($cost, 4);
    }
}
