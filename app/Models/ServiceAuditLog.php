<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'service_id',
        'action',
        'snapshot',
        'changes',
        'changed_by',
        'created_at',
    ];

    protected $casts = [
        'snapshot'   => 'array',
        'changes'    => 'array',
        'created_at' => 'datetime',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
