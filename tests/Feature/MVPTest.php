<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;

class MVPTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Setup initial roles and permissions needed for the test
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
    }

    public function test_guest_cannot_access_admin()
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');

        $response = $this->get('/admin/clients');
        $response->assertRedirect('/login');
    }

    public function test_user_with_permission_can_view_clients_list()
    {
        // El seeder ya creó a Miguel
        $user = User::where('email', 'miguel@ethos.com')->first();

        $response = $this->actingAs($user)->get('/admin/clients');
        $response->assertStatus(200);
        $response->assertViewIs('admin.clients.index');
    }

    public function test_project_creation_validates_and_persists()
    {
        $user = User::where('email', 'miguel@ethos.com')->first();

        $client = Client::create([
            'name' => 'Ethos Client Test',
        ]);

        $projectData = [
            'client_id' => $client->id,
            'title' => 'Nuevo Proyecto de Prueba',
            'status' => 'capturado',
        ];

        $response = $this->actingAs($user)->post('/admin/projects', $projectData);
        $response->assertRedirect('/admin/projects');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('projects', [
            'title' => 'Nuevo Proyecto de Prueba',
            'status' => 'capturado',
            'client_id' => $client->id,
        ]);
    }

    public function test_dashboard_uses_real_project_data()
    {
        $user = User::where('email', 'miguel@ethos.com')->first();

        $client = Client::create([
            'name' => 'Cliente Dashboard',
        ]);

        Project::create([
            'client_id' => $client->id,
            'title' => 'Proyecto Activo',
            'status' => 'en_diseno',
            'ends_at' => now()->addDays(10)->toDateString(),
        ]);

        Project::create([
            'client_id' => $client->id,
            'title' => 'Proyecto Cerrado',
            'status' => 'cerrado',
            'ends_at' => now()->addDays(45)->toDateString(),
        ]);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertViewIs('admin.dashboard');
        $response->assertSeeText('Clientes');
        $response->assertSeeText('Proyectos totales');
        $response->assertSeeText('2');
        $response->assertSeeText('1');
        $response->assertSeeText('Proyectos recientes');
    }
}
