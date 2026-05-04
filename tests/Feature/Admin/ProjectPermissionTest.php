<?php

namespace Tests\Feature\Admin;

use App\Models\Client;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_user_can_create_a_project(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $marketingUser = User::query()->where('email', 'marketing@ethos.com')->firstOrFail();
        $client = Client::query()->create([
            'name' => 'Cliente Marketing Test',
        ]);

        $response = $this->actingAs($marketingUser)->postJson(route('projects.store'), [
            'client_id' => $client->id,
            'title' => 'Proyecto capturado por marketing',
            'description' => 'Captura inicial desde marketing',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Proyecto capturado exitosamente. Se notificó a las consultoras.')
            ->assertJsonPath('project.title', 'Proyecto capturado por marketing')
            ->assertJsonPath('project.client_id', $client->id);

        $this->assertDatabaseHas('projects', [
            'client_id' => $client->id,
            'title' => 'Proyecto capturado por marketing',
            'status' => 'capturado',
            'captured_by' => $marketingUser->id,
        ]);
    }
}