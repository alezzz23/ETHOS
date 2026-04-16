<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 160)->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->unsignedInteger('message_count')->default(0);
            $table->unsignedInteger('total_tokens')->default(0);
            $table->boolean('archived')->default(false);
            $table->boolean('pinned')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'archived', 'last_message_at'], 'chat_conv_user_archived_last_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_conversations');
    }
};
