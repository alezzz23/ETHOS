<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
        $response->assertSeeText('Proyectos Totales');
        $response->assertSeeText('2');
        $response->assertSeeText('1');
        $response->assertSeeText('Proyectos Recientes');
    }

    public function test_assistant_route_returns_reply_with_default_nvidia_base_url()
    {
        config()->set('services.ai_assistant.api_key', 'test-key');
        config()->set('services.ai_assistant.base_url', '');
        config()->set('services.ai_assistant.model', 'minimaxai/minimax-m2.7');
        config()->set('services.ai_assistant.temperature', 1);
        config()->set('services.ai_assistant.top_p', 0.95);
        config()->set('services.ai_assistant.max_tokens', 8192);

        Http::fake([
            'https://integrate.api.nvidia.com/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Respuesta de prueba',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->postJson('/assistant/chat', [
            'message' => 'Hola',
            'history' => [],
        ]);

        $response->assertOk();
        $response->assertJson([
            'reply' => 'Respuesta de prueba',
        ]);

        Http::assertSent(function (HttpRequest $request): bool {
            $payload = $request->data();

            return $request->url() === 'https://integrate.api.nvidia.com/v1/chat/completions'
                && data_get($payload, 'model') === 'minimaxai/minimax-m2.7'
                && data_get($payload, 'temperature') === 1
                && data_get($payload, 'top_p') === 0.95
                && data_get($payload, 'max_tokens') === 8192;
        });
    }

    public function test_assistant_route_handles_missing_api_key()
    {
        config()->set('services.ai_assistant.api_key', '');
        config()->set('services.ai_assistant.base_url', '');

        $response = $this->postJson('/assistant/chat', [
            'message' => 'Necesito soporte',
            'history' => [],
        ]);

        $response->assertStatus(503);
    }

    public function test_landing_contains_clear_chat_controls_and_integration_hooks()
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('id="assistant-clear"', false);
        $response->assertSee('Limpiar chat');
        $response->assertSee('id="assistant-confirm"', false);
        $response->assertSee('id="assistant-confirm-clear"', false);
        $response->assertSee('id="assistant-cancel-clear"', false);
        $response->assertSee('setAssistantClearButtonState', false);
        $response->assertSee('clearAssistantHistory', false);
    }

    public function test_assistant_clear_endpoint_clears_session_history_and_logs_action()
    {
        Log::spy();

        $response = $this->withSession([
            'assistant_history' => [
                ['role' => 'user', 'content' => 'Hola'],
                ['role' => 'assistant', 'content' => '¡Hola!'],
            ],
        ])->postJson('/assistant/chat/clear', [
            'cleared_count' => 2,
        ]);

        $response->assertOk();
        $response->assertJson([
            'cleared' => true,
        ]);
        $response->assertSessionMissing('assistant_history');

        Log::shouldHaveReceived('info')
            ->once()
            ->withArgs(function (string $message, array $context): bool {
                return $message === 'assistant_chat_cleared'
                    && ($context['cleared_count'] ?? null) === 2
                    && isset($context['session_id'])
                    && isset($context['ip']);
            });
    }
}
