<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyResponse extends Model
{
    protected $fillable = [
        'satisfaction_survey_id',
        'nps_score',
        'ces_score',
        'csat_score',
        'what_went_well',
        'what_could_improve',
        'additional_comments',
        'ip_address',
    ];

    protected $casts = [
        'nps_score'  => 'integer',
        'ces_score'  => 'integer',
        'csat_score' => 'integer',
    ];

    // ─── Relations ────────────────────────────────────────────────

    public function survey(): BelongsTo
    {
        return $this->belongsTo(SatisfactionSurvey::class, 'satisfaction_survey_id');
    }

    // ─── Accessors ────────────────────────────────────────────────

    public function getNpsLabelAttribute(): string
    {
        return match (true) {
            $this->nps_score >= 9 => 'Promotor',
            $this->nps_score >= 7 => 'Pasivo',
            default               => 'Detractor',
        };
    }
}
