<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'client_id'        => Client::factory(),
            'title'            => fake()->sentence(4),
            'description'      => fake()->paragraph(),
            'status'           => Project::STATUS_CAPTURADO,
            'type'             => fake()->randomElement(['desarrollo_web', 'infraestructura', 'consultoria']),
            'urgency'          => fake()->randomElement(['baja', 'media', 'alta']),
            'complexity'       => fake()->randomElement(['baja', 'media', 'alta']),
            'estimated_budget' => fake()->randomFloat(2, 500, 50000),
            'currency'         => 'USD',
        ];
    }

    public function inAnalysis(): static
    {
        return $this->state(fn () => ['status' => Project::STATUS_EN_ANALISIS]);
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status'      => Project::STATUS_APROBADO,
            'approved_at' => now(),
        ]);
    }
}
