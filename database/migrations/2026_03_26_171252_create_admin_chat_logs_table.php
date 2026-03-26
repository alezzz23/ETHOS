<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_chat_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('session_id', 100)->index();
            $table->enum('role', ['user', 'assistant', 'system']);
            $table->text('content');
            $table->string('model', 120)->nullable();
            $table->unsignedSmallInteger('tokens_used')->nullable();
            $table->unsignedSmallInteger('response_ms')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 300)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_chat_logs');
    }
};
