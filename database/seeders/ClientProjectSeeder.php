<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;

class ClientProjectSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::pluck('id')->toArray();

        $services = [
            'auditoria_fiscal' => 'Auditoría interna y fiscal',
            'procesos_corporativos' => 'Diseño de procesos corporativos',
            'manuales_operativos' => 'Manuales operativos y de funciones',
            'estructura_organizacional' => 'Estructuración organizacional',
            'control_contable' => 'Sistemas de control contable',
            'protocolos_internos' => 'Protocolos internos empresariales',
        ];

        $clientsData = [
            [
                'name' => 'Grupo Empresarial Andino',
                'industry' => 'Consultoría',
                'city' => 'Caracas',
                'state' => 'Distrito Capital',
                'country' => 'Venezuela',
                'type' => 'empresa',
            ],
            [
                'name' => 'Constructora Horizonte C.A.',
                'industry' => 'Construcción',
                'city' => 'Valencia',
                'state' => 'Carabobo',
                'country' => 'Venezuela',
                'type' => 'empresa',
            ],
            [
                'name' => 'Inversiones Delta Group',
                'industry' => 'Finanzas',
                'city' => 'Maracaibo',
                'state' => 'Zulia',
                'country' => 'Venezuela',
                'type' => 'empresa',
            ],
            [
                'name' => 'Logística Integral 360',
                'industry' => 'Logística',
                'city' => 'Barquisimeto',
                'state' => 'Lara',
                'country' => 'Venezuela',
                'type' => 'empresa',
            ],
            [
                'name' => 'Clínica Santa María',
                'industry' => 'Salud',
                'city' => 'Caracas',
                'state' => 'Miranda',
                'country' => 'Venezuela',
                'type' => 'empresa',
            ],

            // 🔥 nuevos
            [
                'name' => 'Tecnología Avanzada C.A.',
                'industry' => 'Tecnología',
                'city' => 'Caracas',
                'state' => 'Distrito Capital',
                'country' => 'Venezuela',
                'type' => 'empresa',
            ],
            [
                'name' => 'Agroexportadora Los Llanos',
                'industry' => 'Agricultura',
                'city' => 'Acarigua',
                'state' => 'Portuguesa',
                'country' => 'Venezuela',
                'type' => 'empresa',
            ],
            [
                'name' => 'Servicios Integrales Orinoco',
                'industry' => 'Servicios',
                'city' => 'Ciudad Bolívar',
                'state' => 'Bolívar',
                'country' => 'Venezuela',
                'type' => 'empresa',
            ],
            [
                'name' => 'Distribuidora Central del Caribe',
                'industry' => 'Comercio',
                'city' => 'Puerto La Cruz',
                'state' => 'Anzoátegui',
                'country' => 'Venezuela',
                'type' => 'empresa',
            ],
            [
                'name' => 'Industrias Metalúrgicas Lara',
                'industry' => 'Manufactura',
                'city' => 'Barquisimeto',
                'state' => 'Lara',
                'country' => 'Venezuela',
                'type' => 'empresa',
            ],
            [
                'name' => 'Grupo Educativo Futuro',
                'industry' => 'Educación',
                'city' => 'Mérida',
                'state' => 'Mérida',
                'country' => 'Venezuela',
                'type' => 'empresa',
            ],
            [
                'name' => 'Turismo y Aventura Andes',
                'industry' => 'Turismo',
                'city' => 'San Cristóbal',
                'state' => 'Táchira',
                'country' => 'Venezuela',
                'type' => 'empresa',
            ],
            [
                'name' => 'Farmacéutica BioSalud',
                'industry' => 'Salud',
                'city' => 'Maracay',
                'state' => 'Aragua',
                'country' => 'Venezuela',
                'type' => 'empresa',
            ],
            [
                'name' => 'Constructora Nueva Era',
                'industry' => 'Construcción',
                'city' => 'Valencia',
                'state' => 'Carabobo',
                'country' => 'Venezuela',
                'type' => 'empresa',
            ],
            [
                'name' => 'Soluciones Digitales Globales',
                'industry' => 'Tecnología',
                'city' => 'Caracas',
                'state' => 'Miranda',
                'country' => 'Venezuela',
                'type' => 'empresa',
            ],
        ];

        foreach ($clientsData as $clientData) {

            $client = Client::create([
                'name' => $clientData['name'],
                'industry' => $clientData['industry'],

                // Contacto
                'primary_contact_name' => fake()->name(),
                'primary_contact_email' => fake()->safeEmail(),
                'secondary_contact_name' => fake()->name(),
                'secondary_contact_email' => fake()->safeEmail(),
                'phone' => fake()->phoneNumber(),

                // Ubicación completa
                'country' => $clientData['country'],
                'state' => $clientData['state'],
                'municipality' => fake()->city(),
                'city' => $clientData['city'],
                'parish' => fake()->streetName(),
                'address' => fake()->address(),

                // Coordenadas (importante para mapa)
                'latitude' => fake()->latitude(-10, 10),
                'longitude' => fake()->longitude(-70, -60),

                // Negocio
                'type' => $clientData['type'],
                'size' => collect(['pequeño', 'mediano', 'grande'])->random(),
                'source' => collect(['referido', 'web', 'instagram', 'linkedin'])->random(),
                'status' => collect(['lead', 'prospecto', 'cliente'])->random(),
                'estimated_value' => rand(1000, 20000),

                'notes' => fake()->sentence(),
            ]);

            // Crear entre 2 y 4 proyectos por cliente
            for ($i = 0; $i < rand(2, 4); $i++) {

                $typeKey = array_rand($services);

                Project::create([
                    'client_id' => $client->id,

                    'title' => $services[$typeKey],
                    'description' => fake()->paragraph(),

                    'status' => collect([
                        'captured',
                        'classified',
                        'validated',
                        'prioritized',
                        'assigned',
                        'in_progress'
                    ])->random(),

                    // Clasificación
                    'type' => $typeKey,
                    'subtype' => null,
                    'complexity' => collect(['baja', 'media', 'alta'])->random(),
                    'urgency' => collect(['baja', 'media', 'alta'])->random(),

                    // Negocio
                    'estimated_budget' => rand(500, 5000),
                    'final_budget' => rand(800, 8000),
                    'currency' => 'USD',

                    // Priorización
                    'priority_score' => rand(10, 100) / 10,
                    'priority_level' => collect(['baja', 'media', 'alta'])->random(),

                    // Responsables
                    'captured_by' => $users[array_rand($users)] ?? null,
                    'assigned_to' => $users[array_rand($users)] ?? null,
                    'validated_by' => $users[array_rand($users)] ?? null,

                    // Seguimiento
                    'progress' => rand(0, 100),
                    'starts_at' => now()->subDays(rand(1, 30)),
                    'ends_at' => now()->addDays(rand(10, 60)),
                    'finished_at' => rand(0, 1) ? now() : null,
                ]);
            }
        }
    }
}