<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SatisfactionSurvey extends Model
{
    protected $fillable = [
        'project_id',
        'client_id',
        'token',
        'status',
        'sent_at',
        'completed_at',
        'expires_at',
    ];

    protected $casts = [
        'sent_at'      => 'datetime',
        'completed_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    // ─── Relations ────────────────────────────────────────────────

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function response(): HasOne
    {
        return $this->hasOne(SurveyResponse::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public function isValid(): bool
    {
        if ($this->status === 'completed') {
            return false; // already answered
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return $this->status === 'pending';
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
