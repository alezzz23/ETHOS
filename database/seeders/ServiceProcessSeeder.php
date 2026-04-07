<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceProcess;
use App\Models\ProcessMethod;
use Illuminate\Database\Seeder;

class ServiceProcessSeeder extends Seeder
{
    /**
     * Seed processes and methods for all core services.
     *
     * standard_hours = hours per target-person for that method/process.
     * The calculator multiplies these by client size's default_target_persons.
     */
    public function run(): void
    {
        // Map: short_name fragment => [processes => [ name => [method => hours] ]]
        $definitions = [
            // 1 — Auditoría Fiscal
            'Auditoría Fiscal' => [
                'levantamiento'  => ['documental' => 1.5, 'encuesta' => 0.5],
                'diagnostico'    => ['documental' => 2.0, 'entrevista' => 1.0],
                'implementacion' => ['entrevista'  => 1.5],
                'seguimiento'    => ['entrevista'  => 0.5],
            ],
            // 2 — Diseño de Procesos Corporativos
            'Diseño de Procesos Corporativos' => [
                'levantamiento'  => ['entrevista' => 1.5, 'observacion' => 1.0],
                'diagnostico'    => ['documental' => 1.0, 'entrevista'  => 1.0],
                'propuesta'      => ['entrevista' => 0.5],
                'implementacion' => ['observacion' => 1.5, 'entrevista' => 1.0],
                'seguimiento'    => ['encuesta'   => 0.5],
            ],
            // 3 — Manuales Operativos y de Funciones
            'Manuales Operativos y de Funciones' => [
                'levantamiento'  => ['entrevista'  => 2.0, 'observacion' => 1.0],
                'diagnostico'    => ['documental'  => 1.0],
                'propuesta'      => ['documental'  => 0.5],
                'implementacion' => ['entrevista'  => 1.0],
                'seguimiento'    => ['encuesta'    => 0.5],
            ],
            // 4 — Estructuración Organizacional
            'Estructuración Organizacional' => [
                'levantamiento'  => ['entrevista'  => 1.5, 'encuesta'   => 1.0],
                'diagnostico'    => ['documental'  => 1.5, 'entrevista' => 0.5],
                'propuesta'      => ['entrevista'  => 0.5],
                'implementacion' => ['entrevista'  => 1.0, 'observacion' => 0.5],
                'seguimiento'    => ['encuesta'    => 0.5],
            ],
            // 5 — Sistema de Control Contable
            'Sistema de Control Contable' => [
                'levantamiento'  => ['documental'  => 2.0, 'entrevista' => 1.0],
                'diagnostico'    => ['documental'  => 2.0],
                'implementacion' => ['entrevista'  => 1.5, 'documental' => 1.0],
                'seguimiento'    => ['entrevista'  => 0.5],
            ],
            // 6 — Protocolos Internos Empresariales
            'Protocolos Internos Empresariales' => [
                'levantamiento'  => ['entrevista'  => 1.5, 'documental' => 0.5],
                'diagnostico'    => ['documental'  => 1.0],
                'propuesta'      => ['entrevista'  => 0.5],
                'implementacion' => ['entrevista'  => 1.0, 'encuesta'  => 0.5],
                'seguimiento'    => ['encuesta'    => 0.5],
            ],
        ];

        foreach ($definitions as $name => $processes) {
            $service = Service::where('short_name', $name)->first();
            if (! $service) {
                $this->command->warn("Service not found: {$name}");
                continue;
            }

            // Remove existing processes for idempotence
            $service->processes()->delete();

            $order = 1;
            foreach ($processes as $processName => $methods) {
                $process = ServiceProcess::create([
                    'service_id' => $service->id,
                    'name'       => $processName,
                    'order'      => $order++,
                ]);

                foreach ($methods as $method => $hours) {
                    ProcessMethod::create([
                        'service_process_id' => $process->id,
                        'method'             => $method,
                        'standard_hours'     => $hours,
                    ]);
                }
            }

            $totalMethods = collect($processes)->sum(fn($m) => count($m));
            $this->command->line("  ✓ {$name}: " . count($processes) . " procesos, {$totalMethods} métodos");
        }

        $this->command->info('ServiceProcessSeeder completed.');
    }
}
