<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistItem extends Model
{
    protected $fillable = [
        'project_checklist_id',
        'assigned_to',
        'title',
        'description',
        'phase',
        'order',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'order'        => 'integer',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    // ─── Relations ────────────────────────────────────────────────

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(ProjectChecklist::class, 'project_checklist_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // ─── Accessors ────────────────────────────────────────────────

    public function getPhaseLabelAttribute(): string
    {
        return match ($this->phase) {
            'levantamiento'  => 'Levantamiento',
            'diagnostico'    => 'Diagnóstico',
            'propuesta'      => 'Propuesta',
            'implementacion' => 'Implementación',
            'seguimiento'    => 'Seguimiento',
            default          => ucfirst($this->phase),
        };
    }
}
