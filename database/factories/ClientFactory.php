<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'name'                  => fake()->company(),
            'industry'              => fake()->randomElement(['tech', 'retail', 'salud', 'educacion', 'otros']),
            'primary_contact_name'  => fake()->name(),
            'primary_contact_email' => fake()->unique()->safeEmail(),
            'phone'                 => fake()->phoneNumber(),
            'country'               => 'VE',
            'type'                  => fake()->randomElement(['empresa', 'particular']),
            'size'                  => fake()->randomElement(['micro', 'pequeña', 'mediana', 'gran_empresa']),
            'status'                => 'active',
        ];
    }
}
