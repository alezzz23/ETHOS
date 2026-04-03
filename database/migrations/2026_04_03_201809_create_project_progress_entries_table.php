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
        Schema::create('project_progress_entries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('checklist_item_id')->nullable()->constrained('checklist_items')->nullOnDelete();
            $table->unsignedBigInteger('recorded_by');
            $table->foreign('recorded_by')->references('id')->on('users')->cascadeOnDelete();

            // Método de investigación al que pertenece este avance
            $table->string('method')->nullable(); // encuesta, entrevista, observacion, documental
            $table->string('phase')->nullable();  // levantamiento, diagnostico, propuesta, etc.
            $table->decimal('weight', 5, 2)->default(1.00); // ponderación del método

            // Horas planificadas vs reales para esta entrada
            $table->decimal('planned_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->default(0);

            // Porcentaje de avance reportado (0-100)
            $table->unsignedTinyInteger('progress_pct')->default(0);

            $table->text('notes')->nullable();
            $table->date('date_worked');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_progress_entries');
    }
};
