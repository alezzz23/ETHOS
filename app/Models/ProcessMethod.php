<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessMethod extends Model
{
    protected $fillable = ['service_process_id', 'method', 'standard_hours'];

    protected $casts = ['standard_hours' => 'float'];

    public const METHODS = [
        'encuesta'    => 'Encuesta',
        'entrevista'  => 'Entrevista',
        'observacion' => 'Observación',
        'documental'  => 'Documental',
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(ServiceProcess::class, 'service_process_id');
    }

    public function getMethodLabelAttribute(): string
    {
        return self::METHODS[$this->method] ?? $this->method;
    }
}
