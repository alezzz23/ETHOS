<?php

namespace Tests\Unit\Services;

use App\Models\ClientSizeConfig;
use App\Models\ProcessMethod;
use App\Models\Service;
use App\Models\ServiceProcess;
use App\Services\HourCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HourCalculatorServiceTest extends TestCase
{
    use RefreshDatabase;

    private HourCalculatorService $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = app(HourCalculatorService::class);
    }

    public function test_calculates_total_hours_using_standard_hours_times_persons(): void
    {
        ClientSizeConfig::create([
            'size_key' => 'mediana',
            'label' => 'Mediana',
            'min_employees' => 50,
            'max_employees' => 250,
            'default_target_persons' => 20,
        ]);

        $service = Service::create([
            'short_name' => 'Auditoría',
            'description' => 'Test',
            'status' => 'active',
        ]);

        $process = ServiceProcess::create([
            'service_id' => $service->id,
            'name' => 'diagnostico',
            'order' => 1,
        ]);

        ProcessMethod::create([
            'service_process_id' => $process->id,
            'method' => 'entrevista',
            'standard_hours' => 2.0,
        ]);

        $result = $this->calculator->calculate($service, 'mediana');

        // 2 horas * 20 personas = 40
        $this->assertEquals(40.0, $result['total_hours']);
        $this->assertEquals(20, $result['target_persons']);
    }

    public function test_override_persons_takes_precedence_over_size_default(): void
    {
        ClientSizeConfig::create([
            'size_key' => 'pequeña',
            'label' => 'Pequeña',
            'min_employees' => 10,
            'max_employees' => 49,
            'default_target_persons' => 10,
        ]);

        $service = Service::create(['short_name' => 'X', 'description' => 'x', 'status' => 'active']);
        $process = ServiceProcess::create(['service_id' => $service->id, 'name' => 'diagnostico', 'order' => 1]);
        ProcessMethod::create([
            'service_process_id' => $process->id,
            'method' => 'encuesta',
            'standard_hours' => 1.0,
        ]);

        $result = $this->calculator->calculate($service, 'pequeña', overridePersons: 200);

        $this->assertEquals(200, $result['target_persons']);
        $this->assertEquals(200.0, $result['total_hours']);
    }

    public function test_price_range_respects_margin_percent(): void
    {
        ClientSizeConfig::create([
            'size_key' => 'micro', 'label' => 'Micro',
            'min_employees' => 1, 'max_employees' => 9,
            'default_target_persons' => 5,
        ]);
        $service = Service::create(['short_name' => 'X', 'description' => 'x', 'status' => 'active']);
        $process = ServiceProcess::create(['service_id' => $service->id, 'name' => 'propuesta', 'order' => 1]);
        ProcessMethod::create([
            'service_process_id' => $process->id,
            'method' => 'documental',
            'standard_hours' => 4.0,
        ]);

        // 4h * 5p = 20h * $25 = $500 base
        $result = $this->calculator->calculate($service, 'micro', hourlyRate: 25.0, marginPct: 20.0);

        // min = 500 * 1.15 = 575, max = 500 * 1.25 = 625
        $this->assertEquals(575.0, $result['price_min']);
        $this->assertEquals(625.0, $result['price_max']);
    }

    public function test_unknown_size_falls_back_to_default_ten_persons(): void
    {
        $service = Service::create(['short_name' => 'X', 'description' => 'x', 'status' => 'active']);
        $process = ServiceProcess::create(['service_id' => $service->id, 'name' => 'levantamiento', 'order' => 1]);
        ProcessMethod::create([
            'service_process_id' => $process->id,
            'method' => 'observacion',
            'standard_hours' => 1.5,
        ]);

        $result = $this->calculator->calculate($service, 'inexistente');

        $this->assertEquals(10, $result['target_persons']);
        $this->assertEquals(15.0, $result['total_hours']);
    }
}
