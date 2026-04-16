<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiModelPricing extends Model
{
    protected $table = 'ai_model_pricing';

    protected $fillable = [
        'model',
        'provider',
        'prompt_cost_per_1k',
        'completion_cost_per_1k',
        'currency',
        'is_active',
    ];

    protected $casts = [
        'prompt_cost_per_1k'     => 'float',
        'completion_cost_per_1k' => 'float',
        'is_active'              => 'boolean',
    ];

    /**
     * Estima el costo de una interacción (USD por defecto).
     * Si no hay pricing para el modelo, retorna 0.
     */
    public static function estimate(string $model, int $promptTokens, int $completionTokens): float
    {
        $row = static::query()->where('model', $model)->where('is_active', true)->first();
        if (! $row) return 0.0;
        return round(
            ($promptTokens     / 1000) * $row->prompt_cost_per_1k +
            ($completionTokens / 1000) * $row->completion_cost_per_1k,
            6
        );
    }
}
