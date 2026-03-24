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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            // Básico
            $table->string('name');
            $table->string('industry')->nullable();

            // Contacto
            $table->string('primary_contact_name')->nullable();
            $table->string('primary_contact_email')->nullable();
            $table->string('secondary_contact_name')->nullable();
            $table->string('secondary_contact_email')->nullable();
            $table->string('phone')->nullable();

            // Ubicación detallada
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('municipality')->nullable();
            $table->string('city')->nullable();
            $table->string('parish')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Negocio
            $table->string('type')->nullable();   // empresa, particular, gobierno
            $table->string('size')->nullable();   // pequeño, mediano, grande
            $table->string('source')->nullable(); // referido, web, etc
            $table->string('status')->default('lead'); // lead, prospecto, cliente, inactivo
            $table->decimal('estimated_value', 12, 2)->nullable();

            // Notas
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};