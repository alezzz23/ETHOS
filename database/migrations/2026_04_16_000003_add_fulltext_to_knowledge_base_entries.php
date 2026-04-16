<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fase 3.13 — Índice FULLTEXT sobre knowledge_base_entries para búsqueda RAG rápida.
 * Requiere MySQL 5.6+ / InnoDB.
 */
return new class extends Migration {
    public function up(): void
    {
        // Ignoramos si ya existe (MySQL no tiene IF NOT EXISTS para FULLTEXT).
        try {
            DB::statement("ALTER TABLE knowledge_base_entries
                ADD FULLTEXT INDEX kb_entries_fulltext_idx (title, content, embedding_summary)");
        } catch (\Throwable $e) {
            if (! str_contains($e->getMessage(), 'Duplicate key') && ! str_contains($e->getMessage(), 'already exists')) {
                throw $e;
            }
        }
    }

    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE knowledge_base_entries DROP INDEX kb_entries_fulltext_idx");
        } catch (\Throwable $e) { /* noop */ }
    }
};
