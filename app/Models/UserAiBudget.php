<?php

namespace App\Models;

use App\Models\AdminChatLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAiBudget extends Model
{
    protected $fillable = [
        'user_id',
        'daily_token_cap',
        'monthly_token_cap',
        'is_active',
    ];

    protected $casts = [
        'daily_token_cap'   => 'integer',
        'monthly_token_cap' => 'integer',
        'is_active'         => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array{daily:int, monthly:int}
     */
    public static function usage(int $userId): array
    {
        $today = AdminChatLog::query()
            ->where('user_id', $userId)
            ->where('role', 'assistant')
            ->whereDate('created_at', today())
            ->sum('tokens_used');

        $month = AdminChatLog::query()
            ->where('user_id', $userId)
            ->where('role', 'assistant')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('tokens_used');

        return ['daily' => (int) $today, 'monthly' => (int) $month];
    }

    /**
     * Retorna los límites efectivos (propios o defaults desde config).
     *
     * @return array{daily:int, monthly:int}
     */
    public static function effectiveCaps(int $userId): array
    {
        $row = static::query()->where('user_id', $userId)->where('is_active', true)->first();

        return [
            'daily'   => (int) ($row?->daily_token_cap   ?? config('chatbot.budget.default_daily',   50000)),
            'monthly' => (int) ($row?->monthly_token_cap ?? config('chatbot.budget.default_monthly', 1_000_000)),
        ];
    }
}
