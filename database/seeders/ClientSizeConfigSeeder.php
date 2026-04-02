<?php

namespace Database\Seeders;

use App\Models\ClientSizeConfig;
use Illuminate\Database\Seeder;

class ClientSizeConfigSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            [
                'size_key'               => 'micro',
                'label'                  => 'Microempresa',
                'min_employees'          => 1,
                'max_employees'          => 10,
                'default_target_persons' => 5,
            ],
            [
                'size_key'               => 'pequeña',
                'label'                  => 'Pequeña empresa',
                'min_employees'          => 11,
                'max_employees'          => 50,
                'default_target_persons' => 15,
            ],
            [
                'size_key'               => 'mediana',
                'label'                  => 'Mediana empresa',
                'min_employees'          => 51,
                'max_employees'          => 200,
                'default_target_persons' => 40,
            ],
            [
                'size_key'               => 'gran_empresa',
                'label'                  => 'Gran empresa',
                'min_employees'          => 201,
                'max_employees'          => 65535,
                'default_target_persons' => 80,
            ],
        ];

        foreach ($configs as $config) {
            ClientSizeConfig::updateOrCreate(
                ['size_key' => $config['size_key']],
                $config
            );
        }
    }
}
