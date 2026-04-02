<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'short_name',
        'description',
        'functional_areas',
        'client_types',
        'status',
        'version',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'functional_areas' => 'array',
        'client_types'     => 'array',
        'version'          => 'integer',
    ];

    // ─── Relationships ─────────────────────────────────────────────

    public function documents(): HasMany
    {
        return $this->hasMany(ServiceDocument::class)->orderBy('order');
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(ServiceRequirement::class)->orderBy('order');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(ServiceAuditLog::class)->latest();
    }

    public function processes(): HasMany
    {
        return $this->hasMany(ServiceProcess::class)->orderBy('order');
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ─── Scopes ────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // ─── Accessors ─────────────────────────────────────────────────

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getFunctionalAreasLabelAttribute(): string
    {
        return implode(', ', $this->functional_areas ?? []);
    }

    public function getClientTypesLabelAttribute(): string
    {
        return implode(', ', $this->client_types ?? []);
    }
}
