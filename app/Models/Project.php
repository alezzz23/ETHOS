<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        // Relación
        'client_id',

        // Básico
        'title',
        'description',
        'status',

        // Clasificación
        'type',
        'subtype',
        'complexity',
        'urgency',

        // Negocio
        'estimated_budget',
        'final_budget',
        'currency',

        // Priorización
        'priority_score',
        'priority_level',

        // Responsables
        'captured_by',
        'assigned_to',
        'validated_by',

        // Seguimiento
        'progress',
        'starts_at',
        'ends_at',
        'finished_at',
    ];

    protected $casts = [
        // Fechas
        'starts_at'   => 'date',
        'ends_at'     => 'date',
        'finished_at' => 'date',

        // Numéricos
        'estimated_budget' => 'float',
        'final_budget'     => 'float',
        'priority_score'   => 'float',

        // Otros
        'progress' => 'integer',
    ];

    // ─── Relaciones ───────────────────────────────────────────────

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function capturedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'captured_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    // ─── Accessors ───────────────────────────────────────────────

    public function getBudgetDifferenceAttribute(): ?float
    {
        if ($this->estimated_budget === null || $this->final_budget === null) {
            return null;
        }
        return $this->final_budget - $this->estimated_budget;
    }

    public function getBudgetDifferenceLabelAttribute(): string
    {
        $diff = $this->budget_difference;
        if ($diff === null) {
            return 'N/A';
        }
        $formatted = number_format(abs($diff), 2);
        $currency = $this->currency ?? 'USD';
        if ($diff > 0) {
            return "+{$currency} {$formatted} (sobrecosto)";
        }
        if ($diff < 0) {
            return "-{$currency} {$formatted} (ahorro)";
        }
        return "Sin diferencia";
    }

    public function getComplexityLabelAttribute(): string
    {
        return match ($this->complexity) {
            'baja' => 'Baja',
            'media' => 'Media',
            'alta' => 'Alta',
            default => 'Sin definir',
        };
    }

    public function getUrgencyLabelAttribute(): string
    {
        return match ($this->urgency) {
            'baja' => 'Baja',
            'media' => 'Media',
            'alta' => 'Alta',
            default => 'Sin definir',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        $score = $this->priority_score;
        if ($score === null) {
            return 'Sin prioridad';
        }
        if ($score <= 1) {
            return 'Urgente (1)';
        }
        if ($score <= 3) {
            return "Alta ({$score})";
        }
        if ($score <= 5) {
            return "Media ({$score})";
        }
        return "Baja ({$score})";
    }

    public function getProgressPercentAttribute(): int
    {
        return max(0, min(100, (int) ($this->progress ?? 0)));
    }

    public function getEstimatedBudgetLabelAttribute(): string
    {
        if ($this->estimated_budget === null) {
            return 'Sin definir';
        }
        $currency = $this->currency ?? 'USD';
        return "{$currency} " . number_format($this->estimated_budget, 2);
    }

    public function getFinalBudgetLabelAttribute(): string
    {
        if ($this->final_budget === null) {
            return 'Sin definir';
        }
        $currency = $this->currency ?? 'USD';
        return "{$currency} " . number_format($this->final_budget, 2);
    }
}