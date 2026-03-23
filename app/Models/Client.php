<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'industry',
        'primary_contact_name',
        'primary_contact_email',
        'notes',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
