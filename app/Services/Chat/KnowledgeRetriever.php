<?php

namespace App\Services\Chat;

use App\Models\KnowledgeBaseEntry;
use Illuminate\Support\Collection;

class KnowledgeRetriever
{
    public function retrieve(string $query): Collection
    {
        if (! (bool) config('chatbot.privacy.allow_external_kb', true)) {
            return collect();
        }

        return KnowledgeBaseEntry::search(
            $query,
            (int) config('chatbot.rag.top_k', 4)
        );
    }
}
