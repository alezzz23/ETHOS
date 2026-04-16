<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds performance indexes on hot columns and soft-delete support for
 * business-critical tables so records can be archived safely.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->softDeletes();
            $table->index('status', 'projects_status_idx');
            $table->index('priority_level', 'projects_priority_level_idx');
            $table->index(['client_id', 'status'], 'projects_client_status_idx');
            $table->index('assigned_to', 'projects_assigned_to_idx');
            $table->index('ends_at', 'projects_ends_at_idx');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->softDeletes();
            $table->index('status', 'clients_status_idx');
            $table->index('industry', 'clients_industry_idx');
        });

        Schema::table('proposals', function (Blueprint $table) {
            $table->softDeletes();
            $table->index('status', 'proposals_status_idx');
            $table->index(['project_id', 'status'], 'proposals_project_status_idx');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->index('status', 'tasks_status_idx');
            $table->index('assigned_to', 'tasks_assigned_to_idx');
            $table->index(['project_id', 'status'], 'tasks_project_status_idx');
            $table->index('due_date', 'tasks_due_date_idx');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex('projects_status_idx');
            $table->dropIndex('projects_priority_level_idx');
            $table->dropIndex('projects_client_status_idx');
            $table->dropIndex('projects_assigned_to_idx');
            $table->dropIndex('projects_ends_at_idx');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex('clients_status_idx');
            $table->dropIndex('clients_industry_idx');
        });

        Schema::table('proposals', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex('proposals_status_idx');
            $table->dropIndex('proposals_project_status_idx');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('tasks_status_idx');
            $table->dropIndex('tasks_assigned_to_idx');
            $table->dropIndex('tasks_project_status_idx');
            $table->dropIndex('tasks_due_date_idx');
        });
    }
};
