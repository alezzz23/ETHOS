<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatConversation extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'user_id',
        'title',
        'last_message_at',
        'message_count',
        'total_tokens',
        'archived',
        'pinned',
    ];

    protected $casts = [
        'archived'        => 'boolean',
        'pinned'          => 'boolean',
        'message_count'   => 'integer',
        'total_tokens'    => 'integer',
        'last_message_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AdminChatLog::class, 'conversation_id', 'id');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeActive($query)
    {
        return $query->where('archived', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderByDesc('pinned')->orderByDesc('last_message_at');
    }
}
