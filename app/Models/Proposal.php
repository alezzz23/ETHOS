<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proposal extends Model
{
    protected $fillable = [
        'project_id',
        'service_id',
        'created_by',
        'approved_by',
        'client_size',
        'hourly_rate',
        'margin_percent',
        'target_persons',
        'total_hours',
        'adjusted_hours',
        'adjustment_reason',
        'price_min',
        'price_max',
        'payment_milestones',
        'status',
        'rejection_reason',
        'pdf_path',
        'sent_at',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'hourly_rate'        => 'float',
        'margin_percent'     => 'float',
        'target_persons'     => 'integer',
        'total_hours'        => 'float',
        'adjusted_hours'     => 'float',
        'price_min'          => 'float',
        'price_max'          => 'float',
        'payment_milestones' => 'array',
        'sent_at'            => 'datetime',
        'approved_at'        => 'datetime',
        'rejected_at'        => 'datetime',
    ];

    // ─── Relations ────────────────────────────────────────────────

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(ProjectChecklist::class);
    }

    // ─── Accessors ────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft'    => 'Borrador',
            'sent'     => 'Enviada',
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada',
            'expired'  => 'Expirada',
            default    => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft'    => 'secondary',
            'sent'     => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            'expired'  => 'warning',
            default    => 'secondary',
        };
    }
}
