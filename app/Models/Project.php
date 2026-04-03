<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    // ─── Ciclo de vida oficial ─────────────────────────────────────
    // capturado → en_analisis → aprobado → en_ejecucion → cerrado
    public const STATUS_CAPTURADO   = 'capturado';
    public const STATUS_EN_ANALISIS = 'en_analisis';
    public const STATUS_APROBADO    = 'aprobado';
    public const STATUS_EN_EJECUCION = 'en_ejecucion';
    public const STATUS_CERRADO     = 'cerrado';

    public const STATUSES = [
        self::STATUS_CAPTURADO,
        self::STATUS_EN_ANALISIS,
        self::STATUS_APROBADO,
        self::STATUS_EN_EJECUCION,
        self::STATUS_CERRADO,
    ];

    // Campos que se bloquean para edición por el solicitante tras capturar
    public const LOCKED_FIELDS = [
        'title', 'description', 'type', 'subtype',
        'urgency', 'complexity', 'starts_at', 'estimated_budget',
    ];

    protected $fillable = [
        // Relaciones
        'client_id',
        'service_id',

        // Básico (bloqueables post-captura)
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

        // Fase 2: análisis
        'estimated_hours',
        'hourly_rate',

        // Fase 4: ejecución
        'actual_hours',
        'deviation_percent',

        // Priorización
        'priority_score',
        'priority_level',

        // Responsables
        'captured_by',
        'assigned_to',
        'validated_by',
        'leader_id',

        // Seguimiento
        'progress',
        'starts_at',
        'ends_at',
        'finished_at',

        // Ciclo de vida
        'locked_fields_at',
        'approved_at',
        'execution_started_at',
        'closed_at',
    ];

    protected $casts = [
        'starts_at'            => 'date',
        'ends_at'              => 'date',
        'finished_at'          => 'date',
        'locked_fields_at'     => 'datetime',
        'approved_at'          => 'datetime',
        'execution_started_at' => 'datetime',
        'closed_at'            => 'datetime',
        'estimated_budget'     => 'float',
        'final_budget'         => 'float',
        'estimated_hours'      => 'float',
        'hourly_rate'          => 'float',
        'actual_hours'         => 'float',
        'deviation_percent'    => 'float',
        'priority_score'       => 'float',
        'progress'             => 'integer',
    ];

    // ─── Relaciones ───────────────────────────────────────────────

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
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

    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(ProjectChecklist::class);
    }

    public function progressEntries(): HasMany
    {
        return $this->hasMany(ProjectProgressEntry::class);
    }

    // ─── Business logic helpers ───────────────────────────────────

    /**
     * Whether the given user can edit the locked fields.
     */
    public function userCanEditLockedFields(\App\Models\User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('consultor');
    }

    /**
     * Transition to en_analisis and lock fields.
     */
    public function captureAndLock(): void
    {
        $this->locked_fields_at = now();
        $this->status = self::STATUS_CAPTURADO;
        $this->save();
    }

    /**
     * Calculate and persist the weighted priority score.
     * score = urgency_pts + complexity_pts + client_size_pts   (lower = higher priority)
     */
    public function recalculatePriorityScore(): void
    {
        $urgencyPts = match ($this->urgency) {
            'alta'  => 1,
            'media' => 3,
            'baja'  => 5,
            default => 5,
        };
        $complexityPts = match ($this->complexity) {
            'alta'  => 1,
            'media' => 2,
            'baja'  => 3,
            default => 3,
        };
        // Client employee count as proxy for size
        $employees = $this->client?->employees ?? 0;
        $sizePts = match (true) {
            $employees >= 201 => 1,
            $employees >= 51  => 2,
            $employees >= 11  => 3,
            default           => 4,
        };

        $score = $urgencyPts + $complexityPts + $sizePts;
        $this->priority_score = $score;
        $this->priority_level = $score <= 4 ? 'alta' : ($score <= 7 ? 'media' : 'baja');
        $this->saveQuietly();
    }

    /**
     * Recalculate deviation_percent from actual vs estimated hours.
     */
    public function recalculateDeviation(): void
    {
        if ($this->estimated_hours && $this->estimated_hours > 0) {
            $this->deviation_percent = round(
                (($this->actual_hours ?? 0) / $this->estimated_hours - 1) * 100,
                2
            );
            $this->saveQuietly();
        }
    }

    /**
     * Recalculate progress as weighted average of progress entries.
     */
    public function recalculateProgress(): void
    {
        $entries = $this->progressEntries;
        if ($entries->isEmpty()) {
            return;
        }
        $totalWeight = $entries->sum('weight');
        if ($totalWeight <= 0) {
            return;
        }
        $weightedSum = $entries->sum(fn ($e) => $e->progress_pct * $e->weight);
        $this->progress = (int) round($weightedSum / $totalWeight);
        $this->saveQuietly();
    }

    // ─── Accessors ───────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_CAPTURADO    => 'Capturado',
            self::STATUS_EN_ANALISIS  => 'En Análisis',
            self::STATUS_APROBADO     => 'Aprobado',
            self::STATUS_EN_EJECUCION => 'En Ejecución',
            self::STATUS_CERRADO      => 'Cerrado',
            default                   => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_CAPTURADO    => 'secondary',
            self::STATUS_EN_ANALISIS  => 'info',
            self::STATUS_APROBADO     => 'warning',
            self::STATUS_EN_EJECUCION => 'primary',
            self::STATUS_CERRADO      => 'success',
            default                   => 'secondary',
        };
    }

    public function getIsLockedAttribute(): bool
    {
        return $this->locked_fields_at !== null;
    }

    public function getComplexityLabelAttribute(): string
    {
        return match ($this->complexity) {
            'baja'  => 'Baja',
            'media' => 'Media',
            'alta'  => 'Alta',
            default => 'Sin definir',
        };
    }

    public function getUrgencyLabelAttribute(): string
    {
        return match ($this->urgency) {
            'baja'  => 'Baja',
            'media' => 'Media',
            'alta'  => 'Alta',
            default => 'Sin definir',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        $score = $this->priority_score;
        if ($score === null) {
            return 'Sin prioridad';
        }
        if ($score <= 3) {
            return 'Urgente';
        }
        if ($score <= 6) {
            return 'Alta';
        }
        if ($score <= 8) {
            return 'Media';
        }
        return 'Baja';
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
        return 'Sin diferencia';
    }

    public function getDeviationLabelAttribute(): string
    {
        if ($this->deviation_percent === null) {
            return 'N/A';
        }
        $pct = round($this->deviation_percent, 1);
        $sign = $pct >= 0 ? '+' : '';
        return "{$sign}{$pct}%";
    }
}
