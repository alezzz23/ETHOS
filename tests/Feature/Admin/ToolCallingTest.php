<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use App\Services\Chat\Tools\GetMetricsTool;
use App\Services\Chat\Tools\GetProjectStatusTool;
use App\Services\Chat\Tools\SearchClientsTool;
use App\Services\Chat\Tools\ToolRegistry;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ToolCallingTest extends TestCase
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

    public function test_tool_registry_resolves_all_tools(): void
    {
        $r = app(ToolRegistry::class);
        $names = array_keys($r->all());

        $this->assertContains('get_project_status', $names);
        $this->assertContains('list_pending_tasks', $names);
        $this->assertContains('search_clients', $names);
        $this->assertContains('get_proposal', $names);
        $this->assertContains('get_metrics', $names);
    }

    public function test_get_project_status_returns_data(): void
    {
        $client = Client::create(['name' => 'Acme']);
        $project = Project::create([
            'client_id' => $client->id,
            'title'     => 'Proyecto 42',
            'status'    => Project::STATUS_CAPTURADO,
            'progress'  => 25,
        ]);

        $tool = new GetProjectStatusTool();
        $out  = $tool->execute(['project_id' => $project->id], $this->adminUser());

        $this->assertSame($project->id, $out['id']);
        $this->assertSame('Proyecto 42', $out['title']);
        $this->assertSame(25.0, $out['progress']);
    }

    public function test_search_clients_returns_matches(): void
    {
        Client::create(['name' => 'Acme Corp', 'primary_contact_email' => 'a@acme.com']);
        Client::create(['name' => 'Zeta Ltd',  'primary_contact_email' => 'z@zeta.com']);

        $tool = new SearchClientsTool();
        $out  = $tool->execute(['query' => 'acme'], $this->adminUser());

        $this->assertSame(1, $out['count']);
        $this->assertSame('Acme Corp', $out['clients'][0]['name']);
    }

    public function test_get_metrics_general(): void
    {
        $out = (new GetMetricsTool())->execute(['scope' => 'general'], $this->adminUser());

        $this->assertArrayHasKey('users', $out);
        $this->assertArrayHasKey('clients', $out);
        $this->assertArrayHasKey('projects', $out);
        $this->assertArrayHasKey('tasks', $out);
    }

    public function test_registry_dispatch_rejects_unknown_tool(): void
    {
        $out = app(ToolRegistry::class)->dispatch('nonexistent', [], $this->adminUser());
        $this->assertArrayHasKey('error', $out);
    }
}
