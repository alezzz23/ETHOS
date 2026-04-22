<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seeders = [
            RoleAndPermissionSeeder::class,
            ServiceSeeder::class,
            ClientSizeConfigSeeder::class,
            ServiceProcessSeeder::class,
        ];

        // Demo/sample CRM data should not be loaded in production.
        if (! app()->environment('production')) {
            $seeders[] = ClientProjectSeeder::class;
        }

        $this->call($seeders);
    }
}
