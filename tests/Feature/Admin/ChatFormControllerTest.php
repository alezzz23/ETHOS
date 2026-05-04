<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Client;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ChatFormControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function adminUser(): User
    {
        /** @var User $u */
        $u = User::query()->where('email', 'admin@ethos.com')->firstOrFail();
        $u->forceFill(['email_verified_at' => now()])->save();
        return $u;
    }

    public function test_user_form_schema_returns_prefilled_fields(): void
    {
        $res = $this->actingAs($this->adminUser())
            ->postJson(route('admin.chat.forms.schema'), [
                'entity' => 'user',
                'defaults' => [
                    'name' => 'Ana Pérez',
                    'email' => 'ana@ethos.com',
                ],
            ]);

        $res->assertOk()
            ->assertJsonPath('form.entity', 'user')
            ->assertJsonPath('form.fields.0.name', 'name')
            ->assertJsonPath('form.fields.0.value', 'Ana Pérez')
            ->assertJsonPath('form.fields.1.value', 'ana@ethos.com');
    }

    public function test_project_form_schema_includes_client_options(): void
    {
        $client = Client::create(['name' => 'Acme Corp']);

        $res = $this->actingAs($this->adminUser())
            ->postJson(route('admin.chat.forms.schema'), [
                'entity' => 'project',
                'defaults' => [
                    'title' => 'Proyecto piloto',
                ],
            ]);

        $res->assertOk()
            ->assertJsonPath('form.entity', 'project')
            ->assertJsonPath('form.fields.1.value', 'Proyecto piloto')
            ->assertJsonFragment([
                'value' => (string) $client->id,
                'label' => 'Acme Corp',
            ]);
    }
}