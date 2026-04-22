<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_page_includes_password_confirmation_field(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $admin = User::where('email', 'admin@ethos.com')->firstOrFail();

        $response = $this->actingAs($admin)->get(route('users.index'));

        $response->assertOk();
        $response->assertSee('name="password_confirmation"', false);
    }

    public function test_super_admin_can_create_a_user_via_json(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $admin = User::where('email', 'admin@ethos.com')->firstOrFail();
        $email = 'nuevo.'.uniqid().'.admin@gmail.com';

        $response = $this->actingAs($admin)->postJson(route('users.store'), [
            'name' => 'Usuario Nuevo',
            'email' => $email,
            'password' => 'Abcd1234!xyz',
            'password_confirmation' => 'Abcd1234!xyz',
            'role' => 'consultor',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Usuario creado exitosamente')
            ->assertJsonPath('user.email', $email)
            ->assertJsonPath('user.role', 'consultor');

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'name' => 'Usuario Nuevo',
        ]);
    }
}