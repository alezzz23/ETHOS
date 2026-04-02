<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestrictedTopic extends Model
{
    protected $fillable = [
        'topic',
        'keywords',
        'response_message',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'keywords'  => 'array',
        'is_active' => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if the given text triggers this topic.
     */
    public function matches(string $text): bool
    {
        $text = mb_strtolower($text);
        foreach ($this->keywords ?? [] as $keyword) {
            if (str_contains($text, mb_strtolower($keyword))) {
                return true;
            }
        }
        return false;
    }
}
