<?php

namespace App\Services;

use App\Models\ClientSizeConfig;
use App\Models\Service;

class HourCalculatorService
{
    /**
     * Calculate man-hours and suggested price for a service given client size.
     *
     * @param  Service  $service
     * @param  string   $clientSize   micro|pequeña|mediana|gran_empresa
     * @param  float    $hourlyRate   price per man-hour
     * @param  float    $marginPct    desired margin in percentage (0-100)
     * @param  int|null $overridePersons  override default target persons
     * @return array{
     *   total_hours: float,
     *   target_persons: int,
     *   breakdown: array,
     *   suggested_price_min: float,
     *   suggested_price_max: float,
     *   hourly_rate: float,
     *   margin_pct: float,
     * }
     */
    public function calculate(
        Service $service,
        string $clientSize,
        float $hourlyRate = 25.0,
        float $marginPct = 20.0,
        ?int $overridePersons = null
    ): array {
        $sizeConfig = ClientSizeConfig::where('size_key', $clientSize)->first();
        $targetPersons = $overridePersons ?? ($sizeConfig?->default_target_persons ?? 10);

        $service->loadMissing(['processes.methods']);

        $totalHours = 0.0;
        $breakdown  = [];

        foreach ($service->processes as $process) {
            $processHours = 0.0;
            $methods      = [];

            foreach ($process->methods as $method) {
                $hours         = $method->standard_hours * $targetPersons;
                $processHours += $hours;
                $methods[]     = [
                    'method'         => $method->method,
                    'method_label'   => $method->method_label,
                    'standard_hours' => $method->standard_hours,
                    'persons'        => $targetPersons,
                    'subtotal_hours' => round($hours, 2),
                ];
            }

            $totalHours += $processHours;
            $breakdown[] = [
                'process'       => $process->name,
                'process_label' => $process->name_label,
                'hours'         => round($processHours, 2),
                'methods'       => $methods,
            ];
        }

        $basePrice    = $totalHours * $hourlyRate;
        $priceMin     = round($basePrice * (1 + ($marginPct - 5) / 100), 2);
        $priceMax     = round($basePrice * (1 + ($marginPct + 5) / 100), 2);

        return [
            'total_hours'        => round($totalHours, 2),
            'target_persons'     => $targetPersons,
            'breakdown'          => $breakdown,
            'price_min' => $priceMin,
            'price_max' => $priceMax,
            'hourly_rate'        => $hourlyRate,
            'margin_pct'         => $marginPct,
        ];
    }
}
