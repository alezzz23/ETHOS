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

        // 1. Create permissions
        $permissions = [
            'admin.access',
            'users.manage',
            'clients.view',
            'clients.create',
            'clients.edit',
            'clients.delete',
            'projects.view',
            'projects.create',
            'projects.edit',
            'projects.assign',
            'projects.delete',
            'tasks.manage',
            'tasks.execute',
            // Services (Module 1)
            'services.view',
            'services.create',
            'services.edit',
            'services.deactivate',
            // Proposals (Module 4)
            'proposals.view',
            'proposals.create',
            'proposals.edit',
            'proposals.approve',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Create roles and assign specific permissions

        // SUPER ADMIN
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $roleSuperAdmin->givePermissionTo(Permission::all());

        // MARKETING (Captación)
        $roleMarketing = Role::firstOrCreate(['name' => 'marketing']);
        $roleMarketing->givePermissionTo([
            'admin.access',
            'clients.view',
            'clients.create',
            'clients.edit',
            'projects.view',
            'projects.create',
            'services.view',
        ]);

        // CONSULTOR
        $roleConsultor = Role::firstOrCreate(['name' => 'consultor']);
        $roleConsultor->givePermissionTo([
            'admin.access',
            'clients.view',
            'projects.view',
            'projects.create',
            'projects.edit',
            'projects.assign',
            'services.view',
            'services.edit',
            'proposals.view',
            'proposals.create',
            'proposals.edit',
        ]);

        // LÍDER DE PROYECTO
        $roleLider = Role::firstOrCreate(['name' => 'lider_proyecto']);
        $roleLider->givePermissionTo([
            'admin.access',
            'clients.view',
            'projects.view',
            'projects.edit',
            'tasks.manage',
            'tasks.execute',
            'proposals.view',
            'proposals.approve',
        ]);

        // EQUIPO DE LEVANTAMIENTO (Operativo)
        $roleEquipo = Role::firstOrCreate(['name' => 'equipo_levantamiento']);
        $roleEquipo->givePermissionTo([
            'admin.access',
            'projects.view', // Solo ver los suyos, se controlará a nivel de Query/Policy
            'tasks.execute',
        ]);

        // 3. Create test users for the demo
        $testUsers = [
            [
                'name' => 'Miguel (Super Admin)',
                'email' => 'admin@ethos.com',
                'role' => 'super_admin'
            ],
            [
                'name' => 'Ana (Marketing)',
                'email' => 'marketing@ethos.com',
                'role' => 'marketing'
            ],
            [
                'name' => 'Carlos (Consultor)',
                'email' => 'consultor@ethos.com',
                'role' => 'consultor'
            ],
            [
                'name' => 'Laura (Líder Proyecto)',
                'email' => 'lider@ethos.com',
                'role' => 'lider_proyecto'
            ],
            [
                'name' => 'Pedro (Equipo)',
                'email' => 'equipo@ethos.com',
                'role' => 'equipo_levantamiento'
            ],
        ];

        foreach ($testUsers as $testUser) {
            $user = User::firstOrCreate(
                ['email' => $testUser['email']],
                [
                    'name' => $testUser['name'],
                    'password' => Hash::make('password') // default password for testing
                ]
            );

            // Resync role just in case
            $user->syncRoles([$testUser['role']]);
        }
    }
}
