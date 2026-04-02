<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeBaseEntry extends Model
{
    protected $fillable = [
        'service_id',
        'category',
        'title',
        'content',
        'embedding_summary',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ─── Relations ────────────────────────────────────────────────

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForContext($query, string $context)
    {
        return $query->where('category', $context);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Simple keyword relevance scoring for RAG retrieval.
     * Returns entries sorted by how many query terms they match.
     */
    public static function search(string $query, int $limit = 5): \Illuminate\Support\Collection
    {
        $terms = array_filter(array_map('trim', explode(' ', strtolower($query))));

        return static::active()
            ->get()
            ->map(function (self $entry) use ($terms) {
                $haystack = strtolower($entry->title . ' ' . ($entry->embedding_summary ?? $entry->content));
                $score    = 0;
                foreach ($terms as $term) {
                    if (strlen($term) > 3 && str_contains($haystack, $term)) {
                        $score++;
                    }
                }
                return ['entry' => $entry, 'score' => $score];
            })
            ->filter(fn ($item) => $item['score'] > 0)
            ->sortByDesc('score')
            ->take($limit)
            ->pluck('entry');
    }
}
