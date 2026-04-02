<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFunctionalArea extends Model
{
    protected $fillable = ['user_id', 'functional_area'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
