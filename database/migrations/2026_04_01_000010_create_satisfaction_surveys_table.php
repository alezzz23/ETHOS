<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('satisfaction_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('token', 80)->unique();
            $table->enum('status', ['pending', 'completed', 'expired'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('satisfaction_survey_id')->constrained('satisfaction_surveys')->cascadeOnDelete();
            // NPS: 0-10
            $table->unsignedTinyInteger('nps_score')->nullable();
            // CES: 1-7 (Customer Effort Score)
            $table->unsignedTinyInteger('ces_score')->nullable();
            // CSAT: 1-5 (Customer Satisfaction)
            $table->unsignedTinyInteger('csat_score')->nullable();
            // Open feedback
            $table->text('what_went_well')->nullable();
            $table->text('what_could_improve')->nullable();
            $table->text('additional_comments')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_responses');
        Schema::dropIfExists('satisfaction_surveys');
    }
};
