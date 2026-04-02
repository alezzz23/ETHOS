<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restricted_topics', function (Blueprint $table) {
            $table->id();
            $table->string('topic');
            $table->json('keywords');           // ["precio exacto","tarifa","costo total",...]
            $table->text('response_message');   // what chatbot replies when topic matched
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restricted_topics');
    }
};
