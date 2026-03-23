<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'clients.view',
            'clients.create',
            'clients.update',
            'projects.view',
            'projects.create',
            'projects.update',
            'admin.access',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $roles = [
            'presidente_vendedor',
            'socio',
            'consultor_estrategico',
            'disenador_procesos',
            'documentador_procesos',
            'lider_proyecto',
            'especialista_implementacion',
            'experto_sistemas',
            'marketing_diseno',
        ];

        foreach ($roles as $role) {
            $roleInstance = Role::firstOrCreate(['name' => $role]);
            // For MVP give all permissions to all roles or just basic ones?
            // "Permisos mínimos por MVP" all roles get these for now
            $roleInstance->givePermissionTo($permissions);
        }

        // Create Users from ETHOS_logica_procesos.md
        $team = [
            // Miguel — Presidente y Vendedor Estratégico
            ['name' => 'Miguel', 'email' => 'miguel@ethos.com', 'role' => 'presidente_vendedor'],
            
            // Documentadores de Procesos
            ['name' => 'Leomar', 'email' => 'leomar@ethos.com', 'role' => 'documentador_procesos'],
            ['name' => 'Gabriela', 'email' => 'gabriela@ethos.com', 'role' => 'documentador_procesos'],
            ['name' => 'Doren', 'email' => 'doren@ethos.com', 'role' => 'documentador_procesos'],
            ['name' => 'Luis', 'email' => 'luis@ethos.com', 'role' => 'documentador_procesos'],
            
            // Marketing y Diseño Gráfico
            ['name' => 'Verónica', 'email' => 'veronica@ethos.com', 'role' => 'marketing_diseno'],
            ['name' => 'Alejandra', 'email' => 'alejandra@ethos.com', 'role' => 'marketing_diseno'],
        ];

        foreach ($team as $member) {
            $user = User::firstOrCreate([
                'email' => $member['email']
            ], [
                'name' => $member['name'],
                'password' => Hash::make('password') // default password
            ]);

            $user->assignRole($member['role']);
        }
    }
}
