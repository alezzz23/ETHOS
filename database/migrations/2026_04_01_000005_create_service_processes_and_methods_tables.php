<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Processes that compose a service
        Schema::create('service_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->enum('name', [
                'levantamiento',
                'diagnostico',
                'propuesta',
                'implementacion',
                'seguimiento',
            ]);
            $table->unsignedTinyInteger('order')->default(0);
            $table->timestamps();
        });

        // Research methods per process and their standard hours
        Schema::create('process_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_process_id')->constrained('service_processes')->cascadeOnDelete();
            $table->enum('method', ['encuesta', 'entrevista', 'observacion', 'documental']);
            $table->decimal('standard_hours', 6, 2)->default(1.00); // hours per person-unit
            $table->timestamps();
        });

        // Default target persons per client size
        Schema::create('client_size_configs', function (Blueprint $table) {
            $table->id();
            $table->string('size_key')->unique();   // micro, pequeña, mediana, gran_empresa
            $table->string('label');
            $table->unsignedSmallInteger('min_employees')->default(1);
            $table->unsignedSmallInteger('max_employees')->default(9999);
            $table->unsignedSmallInteger('default_target_persons')->default(10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('process_methods');
        Schema::dropIfExists('service_processes');
        Schema::dropIfExists('client_size_configs');
    }
};
