<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            // Relación
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();

            // Básico
            $table->string('title');
            $table->text('description')->nullable();

            // Estado del flujo
            $table->string('status')->default('captured'); 
            // captured, classified, validated, prioritized, assigned, in_progress, closed

            // Clasificación
            $table->string('type')->nullable();        // desarrollo_web, infraestructura, etc
            $table->string('subtype')->nullable();     // landing, app, sistema
            $table->string('complexity')->nullable();  // baja, media, alta
            $table->string('urgency')->nullable();     // baja, media, alta

            // Negocio
            $table->decimal('estimated_budget', 12, 2)->nullable();
            $table->decimal('final_budget', 12, 2)->nullable();
            $table->string('currency', 10)->default('USD');

            // Priorización
            $table->decimal('priority_score', 5, 2)->nullable();
            $table->string('priority_level')->nullable(); // baja, media, alta

            // Responsables
            $table->foreignId('captured_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();

            // Seguimiento
            $table->unsignedTinyInteger('progress')->default(0); // 0 - 100
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->date('finished_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};