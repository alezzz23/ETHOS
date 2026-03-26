<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->date('birth_date')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('birth_date');
            $table->string('position')->nullable()->after('avatar');
            $table->text('bio')->nullable()->after('position');
            // Notification preferences (stored as JSON-like booleans)
            $table->boolean('notif_email')->default(true)->after('bio');
            $table->boolean('notif_browser')->default(true)->after('notif_email');
            $table->boolean('notif_project_updates')->default(true)->after('notif_browser');
            $table->boolean('notif_client_activity')->default(false)->after('notif_project_updates');
            // Privacy
            $table->boolean('privacy_show_email')->default(false)->after('notif_client_activity');
            $table->boolean('privacy_show_phone')->default(false)->after('privacy_show_email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'birth_date', 'avatar', 'position', 'bio',
                'notif_email', 'notif_browser', 'notif_project_updates', 'notif_client_activity',
                'privacy_show_email', 'privacy_show_phone',
            ]);
        });
    }
};
