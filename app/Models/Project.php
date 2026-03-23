<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'title',
        'description',
        'status',
        'starts_at',
        'ends_at',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
