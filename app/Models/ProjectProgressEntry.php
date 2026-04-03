<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectProgressEntry extends Model
{
    protected $fillable = [
        'project_id',
        'checklist_item_id',
        'recorded_by',
        'method',
        'phase',
        'weight',
        'planned_hours',
        'actual_hours',
        'progress_pct',
        'notes',
        'date_worked',
    ];

    protected $casts = [
        'date_worked'   => 'date',
        'actual_hours'  => 'float',
        'planned_hours' => 'float',
        'weight'        => 'float',
        'progress_pct'  => 'integer',
    ];

    // ─── Relations ────────────────────────────────────────────────

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function checklistItem(): BelongsTo
    {
        return $this->belongsTo(ChecklistItem::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // ─── Accessors ────────────────────────────────────────────────

    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'encuesta'     => 'Encuesta',
            'entrevista'   => 'Entrevista',
            'observacion'  => 'Observación',
            'documental'   => 'Documental',
            default        => ucfirst($this->method ?? ''),
        };
    }

    public function getPhaseLabelAttribute(): string
    {
        return match ($this->phase) {
            'levantamiento'  => 'Levantamiento',
            'diagnostico'    => 'Diagnóstico',
            'propuesta'      => 'Propuesta',
            'implementacion' => 'Implementación',
            'seguimiento'    => 'Seguimiento',
            default          => ucfirst($this->phase ?? ''),
        };
    }
}
