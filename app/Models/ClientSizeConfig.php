<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientSizeConfig extends Model
{
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
}
