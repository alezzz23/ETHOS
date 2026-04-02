<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceProcess extends Model
{
    protected $fillable = ['service_id', 'name', 'order'];

    protected $casts = ['order' => 'integer'];

    public const NAMES = [
        'levantamiento'  => 'Levantamiento',
        'diagnostico'    => 'Diagnóstico',
        'propuesta'      => 'Propuesta',
        'implementacion' => 'Implementación',
        'seguimiento'    => 'Seguimiento',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function methods(): HasMany
    {
        return $this->hasMany(ProcessMethod::class, 'service_process_id');
    }

    public function getNameLabelAttribute(): string
    {
        return self::NAMES[$this->name] ?? $this->name;
    }
}
