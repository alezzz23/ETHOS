<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ClientPortalToken extends Model
{
    protected $fillable = [
        'project_id',
        'client_id',
        'token',
        'expires_at',
        'last_accessed_at',
        'is_active',
    ];

    protected $casts = [
        'expires_at'       => 'datetime',
        'last_accessed_at' => 'datetime',
        'is_active'        => 'boolean',
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

    // ─── Helpers ──────────────────────────────────────────────────

    public static function generate(Project $project, ?\DateTimeInterface $expiresAt = null): self
    {
        return static::create([
            'project_id' => $project->id,
            'client_id'  => $project->client_id,
            'token'      => Str::random(64),
            'expires_at' => $expiresAt,
            'is_active'  => true,
        ]);
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function markAccessed(): void
    {
        $this->update(['last_accessed_at' => now()]);
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopeValid($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }
}
