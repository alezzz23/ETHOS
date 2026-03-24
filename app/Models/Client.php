<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        // Básico
        'name',
        'industry',

        // Contacto
        'primary_contact_name',
        'primary_contact_email',
        'secondary_contact_name',
        'secondary_contact_email',
        'phone',

        // Ubicación
        'country',
        'state',
        'municipality',
        'city',
        'parish',
        'address',
        'latitude',
        'longitude',

        // Negocio
        'type',
        'size',
        'source',
        'status',
        'estimated_value',

        // Notas
        'notes',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'estimated_value' => 'float',
    ];

    // ─── Relaciones ───────────────────────────────────────────────

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}