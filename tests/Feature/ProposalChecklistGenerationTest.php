<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ProcessMethod;
use App\Models\Project;
use App\Models\ProjectChecklist;
use App\Models\Proposal;
use App\Models\Service;
use App\Models\ServiceProcess;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProposalChecklistGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_approving_a_proposal_generates_only_one_checklist(): void
    {
        $user = User::query()->where('email', 'admin@ethos.com')->firstOrFail();
        $user->forceFill(['email_verified_at' => now()])->save();

        $client = Client::query()->create([
            'name' => 'Cliente QA Checklist',
        ]);

        $service = Service::query()->create([
            'short_name'   => 'Servicio QA Checklist',
            'description'  => 'Servicio para prueba de checklist sin duplicados.',
            'status'       => 'active',
            'created_by'   => $user->id,
            'updated_by'   => $user->id,
        ]);

        $levantamiento = ServiceProcess::query()->create([
            'service_id' => $service->id,
            'name'       => 'levantamiento',
            'order'      => 1,
        ]);

        ProcessMethod::query()->create([
            'service_process_id' => $levantamiento->id,
            'method'             => 'entrevista',
            'standard_hours'     => 8,
        ]);

        $diagnostico = ServiceProcess::query()->create([
            'service_id' => $service->id,
            'name'       => 'diagnostico',
            'order'      => 2,
        ]);

        ProcessMethod::query()->create([
            'service_process_id' => $diagnostico->id,
            'method'             => 'documental',
            'standard_hours'     => 6,
        ]);

        $project = Project::query()->create([
            'client_id'      => $client->id,
            'service_id'     => $service->id,
            'title'          => 'Proyecto QA Checklist',
            'status'         => Project::STATUS_EN_ANALISIS,
            'captured_by'    => $user->id,
            'assigned_to'    => $user->id,
            'estimated_hours'=> 14,
            'hourly_rate'    => 45,
        ]);

        $proposal = Proposal::query()->create([
            'project_id'      => $project->id,
            'service_id'      => $service->id,
            'created_by'      => $user->id,
            'client_size'     => 'mediana',
            'hourly_rate'     => 45,
            'margin_percent'  => 25,
            'target_persons'  => 4,
            'total_hours'     => 14,
            'adjusted_hours'  => 14,
            'price_min'       => 700,
            'price_max'       => 787.5,
            'status'          => 'sent',
            'sent_at'         => now(),
        ]);

        $response = $this->actingAs($user)->patch(route('proposals.approve', $proposal));

        $response->assertOk();
        $response->assertJsonPath('message', 'Propuesta aprobada. Lista de levantamiento generada.');

        $this->assertSame(Project::STATUS_APROBADO, $project->fresh()->status);
        $this->assertSame(1, ProjectChecklist::query()->where('project_id', $project->id)->count());

        $checklist = ProjectChecklist::query()
            ->where('project_id', $project->id)
            ->where('proposal_id', $proposal->id)
            ->firstOrFail();

        $this->assertSame(2, $checklist->items()->count());
    }
}