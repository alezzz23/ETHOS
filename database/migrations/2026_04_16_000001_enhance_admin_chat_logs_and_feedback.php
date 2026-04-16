<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── admin_chat_logs: tokens_used → UNSIGNED INT (evita overflow > 65535) ─
        DB::statement('ALTER TABLE admin_chat_logs MODIFY tokens_used INT UNSIGNED NOT NULL DEFAULT 0');

        Schema::table('admin_chat_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('admin_chat_logs', 'conversation_id')) {
                $table->uuid('conversation_id')->nullable()->after('session_id');
                $table->index('conversation_id', 'admin_chat_logs_conversation_id_idx');
            }

            $table->index(['user_id', 'created_at'], 'admin_chat_logs_user_created_idx');
        });

        // ── chat_feedback: índice compuesto para reportes ─────────────
        Schema::table('chat_feedback', function (Blueprint $table) {
            $table->index(['context', 'rating'], 'chat_feedback_context_rating_idx');
        });
    }

    public function down(): void
    {
        Schema::table('admin_chat_logs', function (Blueprint $table) {
            $table->dropIndex('admin_chat_logs_user_created_idx');

            if (Schema::hasColumn('admin_chat_logs', 'conversation_id')) {
                $table->dropIndex('admin_chat_logs_conversation_id_idx');
                $table->dropColumn('conversation_id');
            }
        });

        DB::statement('ALTER TABLE admin_chat_logs MODIFY tokens_used SMALLINT UNSIGNED NOT NULL DEFAULT 0');

        Schema::table('chat_feedback', function (Blueprint $table) {
            $table->dropIndex('chat_feedback_context_rating_idx');
        });
    }
};
