<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Normalise project lifecycle fields.
     *
     * NEW STATUS CYCLE:
     *   capturado → en_analisis → aprobado → en_ejecucion → cerrado
     *
     * Adds:
     *   - service_id           FK → services  (Fase 2)
     *   - leader_id            FK → users     (Fase 2: líder de equipo)
     *   - estimated_hours      Horas-hombre estimadas (Fase 2)
     *   - hourly_rate          Tarifa/hora consultora (Fase 2)
     *   - actual_hours         Horas reales consumidas (Fase 4)
     *   - deviation_percent    Desvío real vs planificado % (Fase 4)
     *   - locked_fields_at     Timestamp bloqueo de campos clave (Fase 1)
     *   - approved_at          Timestamp aprobación (Fase 3)
     *   - execution_started_at Timestamp inicio ejecución (Fase 4)
     *   - closed_at            Timestamp cierre real (Fase 4)
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Fase 2: análisis de consultora
            $table->unsignedBigInteger('service_id')->nullable()->after('client_id');
            $table->foreign('service_id')->references('id')->on('services')->nullOnDelete();

            $table->unsignedBigInteger('leader_id')->nullable()->after('assigned_to');
            $table->foreign('leader_id')->references('id')->on('users')->nullOnDelete();

            $table->decimal('estimated_hours', 8, 2)->nullable()->after('final_budget');
            $table->decimal('hourly_rate', 10, 2)->nullable()->after('estimated_hours');

            // Fase 4: ejecución
            $table->decimal('actual_hours', 8, 2)->nullable()->after('hourly_rate');
            $table->decimal('deviation_percent', 6, 2)->nullable()->after('actual_hours');

            // Control de ciclo de vida
            $table->timestamp('locked_fields_at')->nullable()->after('finished_at');
            $table->timestamp('approved_at')->nullable()->after('locked_fields_at');
            $table->timestamp('execution_started_at')->nullable()->after('approved_at');
            $table->timestamp('closed_at')->nullable()->after('execution_started_at');
        });

        // Normalise old status values → new lifecycle statuses
        $map = [
            'captured'                   => 'capturado',
            'classified'                 => 'en_analisis',
            'validated'                  => 'en_analisis',
            'prioritized'                => 'en_analisis',
            'assigned'                   => 'aprobado',
            'assignment'                 => 'en_analisis',
            'in_progress'                => 'en_ejecucion',
            'closed'                     => 'cerrado',
            'clasificacion_pendiente'    => 'en_analisis',
            'priorizado'                 => 'aprobado',
            'asignacion_lider_pendiente' => 'aprobado',
            'en_diagnostico'             => 'en_ejecucion',
            'en_diseno'                  => 'en_ejecucion',
            'en_implementacion'          => 'en_ejecucion',
            'en_seguimiento'             => 'en_ejecucion',
        ];

        foreach ($map as $old => $new) {
            DB::table('projects')->where('status', $old)->update(['status' => $new]);
        }
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropForeign(['leader_id']);
            $table->dropColumn([
                'service_id', 'leader_id',
                'estimated_hours', 'hourly_rate',
                'actual_hours', 'deviation_percent',
                'locked_fields_at', 'approved_at',
                'execution_started_at', 'closed_at',
            ]);
        });
    }
};
