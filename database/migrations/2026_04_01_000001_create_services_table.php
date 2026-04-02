<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            $table->string('short_name');
            $table->text('description');
            $table->json('functional_areas')->nullable();  // ["RRHH","Finanzas","Logística",...]
            $table->json('client_types')->nullable();      // ["micro","pequeña","mediana","gran_empresa"]

            $table->string('status')->default('active');   // active | inactive
            $table->unsignedSmallInteger('version')->default(1);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
