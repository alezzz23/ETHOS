<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatFeedback extends Model
{
    protected $fillable = [
        'admin_chat_log_id',
        'context',
        'rating',
        'user_message',
        'assistant_message',
        'improvement_note',
        'session_id',
    ];
}
