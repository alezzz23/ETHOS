<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ClientSizeConfig extends Model
{
    public const CACHE_KEY = 'client_size_configs:all';
    public const CACHE_TTL = 3600;

    protected $fillable = [
        'size_key',
        'label',
        'min_employees',
        'max_employees',
        'default_target_persons',
    ];

    protected $casts = [
        'min_employees'          => 'integer',
        'max_employees'          => 'integer',
        'default_target_persons' => 'integer',
    ];

    protected static function booted(): void
    {
        $flush = fn () => Cache::forget(self::CACHE_KEY);
        static::saved($flush);
        static::deleted($flush);
    }

    /**
     * Cached full list ordered by min_employees.
     */
    public static function allCached(): Collection
    {
        return Cache::remember(
            self::CACHE_KEY,
            self::CACHE_TTL,
            fn () => static::orderBy('min_employees')->get()
        );
    }
}

