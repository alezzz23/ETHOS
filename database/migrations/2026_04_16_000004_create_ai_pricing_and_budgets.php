<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_model_pricing', function (Blueprint $table) {
            $table->id();
            $table->string('model', 120)->unique();
            $table->string('provider', 60)->default('openrouter');
            $table->decimal('prompt_cost_per_1k',      10, 6)->default(0);
            $table->decimal('completion_cost_per_1k',  10, 6)->default(0);
            $table->string('currency', 8)->default('USD');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('user_ai_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('daily_token_cap')->nullable();
            $table->unsignedInteger('monthly_token_cap')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_ai_budgets');
        Schema::dropIfExists('ai_model_pricing');
    }
};
