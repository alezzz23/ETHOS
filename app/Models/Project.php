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
}