<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_base_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category');           // 'faq' | 'process' | 'case_study' | 'definition'
            $table->string('title');
            $table->text('content');
            $table->text('embedding_summary')->nullable(); // Short summary for RAG retrieval
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('chat_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_chat_log_id')->nullable()->constrained('admin_chat_logs')->nullOnDelete();
            $table->enum('context', ['landing', 'dashboard']);
            $table->enum('rating', ['helpful', 'not_helpful']);
            $table->text('user_message')->nullable();
            $table->text('assistant_message')->nullable();
            $table->text('improvement_note')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_feedback');
        Schema::dropIfExists('knowledge_base_entries');
    }
};
