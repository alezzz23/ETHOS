<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminChatLog extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'role',
        'content',
        'model',
        'tokens_used',
        'response_ms',
        'ip_address',
        'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
