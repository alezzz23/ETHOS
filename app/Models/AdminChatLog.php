<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminChatLog extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'conversation_id',
        'role',
        'content',
        'model',
        'tokens_used',
        'response_ms',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'tokens_used' => 'integer',
        'response_ms' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(ChatFeedback::class);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForConversation($query, string $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }
}
