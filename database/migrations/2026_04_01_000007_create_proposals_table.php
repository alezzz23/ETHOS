<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            // Hour calculation inputs
            $table->string('client_size');              // micro | pequeña | mediana | gran_empresa
            $table->decimal('hourly_rate', 8, 2)->default(25.00);
            $table->decimal('margin_percent', 5, 2)->default(20.00);
            $table->unsignedInteger('target_persons')->nullable();

            // Hour calculation outputs (stored for immutability)
            $table->decimal('total_hours', 8, 2)->default(0);
            $table->decimal('adjusted_hours', 8, 2)->default(0);
            $table->text('adjustment_reason')->nullable();
            $table->decimal('price_min', 10, 2)->default(0);
            $table->decimal('price_max', 10, 2)->default(0);

            // Payment plan
            $table->json('payment_milestones')->nullable(); // [{label, percentage, amount, due_days}]

            // Status lifecycle
            $table->enum('status', ['draft', 'sent', 'approved', 'rejected', 'expired'])
                ->default('draft');
            $table->text('rejection_reason')->nullable();

            // PDF
            $table->string('pdf_path')->nullable();

            // Timestamps
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
