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
     * Búsqueda RAG con MySQL FULLTEXT (MATCH...AGAINST) con fallback a LIKE
     * si FULLTEXT no está disponible o la query es demasiado corta.
     *
     * @return \Illuminate\Support\Collection<int, self>
     */
    public static function search(string $query, int $limit = 5): \Illuminate\Support\Collection
    {
        $query = trim($query);
        if ($query === '') return collect();

        // FULLTEXT requiere tokens ≥ 3-4 chars (innodb_ft_min_token_size) y
        // rinde mal con queries muy cortas. Para queries largas usamos MATCH.
        if (mb_strlen($query) >= 4) {
            try {
                $raw = static::active()
                    ->selectRaw(
                        '*, MATCH(title, content, embedding_summary) AGAINST (? IN NATURAL LANGUAGE MODE) AS score',
                        [$query]
                    )
                    ->whereRaw(
                        'MATCH(title, content, embedding_summary) AGAINST (? IN NATURAL LANGUAGE MODE)',
                        [$query]
                    )
                    ->orderByDesc('score')
                    ->limit($limit)
                    ->get();

                if ($raw->isNotEmpty()) return $raw;
            } catch (\Throwable $e) {
                // FULLTEXT index no presente (dev/sqlite): fall through al LIKE.
                \Illuminate\Support\Facades\Log::debug('kb_fulltext_fallback', ['error' => $e->getMessage()]);
            }
        }

        // Fallback LIKE por términos (score = nº de términos que matchean).
        $terms = array_filter(array_map('trim', preg_split('/\s+/u', mb_strtolower($query)) ?: []));
        if (! $terms) return collect();

        return static::active()
            ->get()
            ->map(function (self $entry) use ($terms) {
                $hay = mb_strtolower($entry->title . ' ' . ($entry->embedding_summary ?? $entry->content));
                $score = 0;
                foreach ($terms as $t) {
                    if (mb_strlen($t) > 3 && str_contains($hay, $t)) $score++;
                }
                return ['entry' => $entry, 'score' => $score];
            })
            ->filter(fn ($i) => $i['score'] > 0)
            ->sortByDesc('score')
            ->take($limit)
            ->pluck('entry');
    }
}
