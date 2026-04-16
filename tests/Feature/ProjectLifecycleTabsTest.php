<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\DomCrawler\Crawler;
use Tests\TestCase;

final class ProjectLifecycleTabsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_captured_projects_keep_phase_two_available_for_analysis(): void
    {
        $user = User::query()->where('email', 'admin@ethos.com')->firstOrFail();
        $user->forceFill(['email_verified_at' => now()])->save();

        $client = Client::query()->create([
            'name' => 'Cliente QA Tabs',
        ]);

        $project = Project::query()->create([
            'client_id' => $client->id,
            'title' => 'Proyecto QA Tabs',
            'status' => Project::STATUS_CAPTURADO,
            'captured_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('projects.show', $project));

        $response->assertOk();
        $response->assertSee('Fase 2 · Análisis');
        $response->assertSee('Enviar a Análisis');

        $crawler = new Crawler($response->getContent());
        $phaseTwoTab = $crawler->filter('#tab-f2');

        $this->assertCount(1, $phaseTwoTab);
        $this->assertStringNotContainsString('disabled', (string) $phaseTwoTab->attr('class'));
        $this->assertCount(1, $crawler->filter('#formAnalyze'));
    }

    public function test_projects_in_analysis_keep_phase_three_available_for_approval(): void
    {
        $user = User::query()->where('email', 'admin@ethos.com')->firstOrFail();
        $user->forceFill(['email_verified_at' => now()])->save();

        $client = Client::query()->create([
            'name' => 'Cliente QA Approval',
        ]);

        $project = Project::query()->create([
            'client_id' => $client->id,
            'title' => 'Proyecto QA Approval',
            'status' => Project::STATUS_EN_ANALISIS,
            'captured_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('projects.show', $project));

        $response->assertOk();
        $response->assertSee('Fase 3 · Aprobación');
        $response->assertSee('Aprobar Proyecto');

        $crawler = new Crawler($response->getContent());
        $phaseThreeTab = $crawler->filter('#tab-f3');

        $this->assertCount(1, $phaseThreeTab);
        $this->assertStringNotContainsString('disabled', (string) $phaseThreeTab->attr('class'));
        $this->assertCount(1, $crawler->filter('#formApprove'));
    }
}