<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectChecklist extends Model
{
    protected $fillable = [
        'project_id',
        'proposal_id',
        'service_id',
        'created_by',
        'title',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    // ─── Relations ────────────────────────────────────────────────

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ChecklistItem::class, 'project_checklist_id')->orderBy('order');
    }

    // ─── Accessors ────────────────────────────────────────────────

    public function getCompletionPercentAttribute(): int
    {
        $total = $this->items()->count();

        if ($total === 0) {
            return 0;
        }

        $done = $this->items()->where('is_completed', true)->count();

        return (int) round(($done / $total) * 100);
    }
}
