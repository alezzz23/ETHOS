<?php

namespace App\Observers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\UserFunctionalArea;
use App\Jobs\SendSatisfactionSurveyJob;
use App\Notifications\NewProjectAssignmentNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class ProjectObserver
{
    /**
     * Status that triggers consultant assignment alert.
     */
    private const TRIGGER_STATUS = 'assignment';

    public function updated(Project $project): void
    {
        if (!$project->wasChanged('status')) {
            return;
        }

        if ($project->status === 'completed') {
            SendSatisfactionSurveyJob::dispatch($project)->delay(now()->addHours(1));
        }

        if ($project->status !== self::TRIGGER_STATUS) {
            return;
        }

        $this->notifyConsultants($project);
    }

    // ─── Helpers ───────────────────────────────────────────────────

    private function notifyConsultants(Project $project): void
    {
        // Find functional areas relevant to this project
        // For now we use project type as a proxy; once proposals link to services
        // we'll use service functional_areas directly.
        $functionalAreas = $this->inferAreasFromProject($project);

        // Find consultants with matching functional areas
        $consultants = User::whereHas('roles', fn ($q) => $q->where('name', 'consultor'))
            ->when($functionalAreas, function ($q) use ($functionalAreas) {
                $q->whereHas('functionalAreas', function ($q2) use ($functionalAreas) {
                    $q2->whereIn('functional_area', $functionalAreas);
                });
            })
            ->get();

        // If no match, send to all consultants
        if ($consultants->isEmpty()) {
            $consultants = User::whereHas('roles', fn ($q) => $q->where('name', 'consultor'))->get();
        }

        $dueDate = Carbon::now()->addWeekdays(3);

        foreach ($consultants as $consultant) {
            // Create task
            Task::create([
                'project_id'  => $project->id,
                'assigned_to' => $consultant->id,
                'type'        => 'proposal_upload',
                'title'       => 'Subir propuesta de servicio',
                'description' => "El proyecto \"{$project->title}\" requiere propuesta. Vence en 3 días hábiles.",
                'due_date'    => $dueDate,
                'status'      => 'pending',
            ]);

            // Send notification
            $consultant->notify(new NewProjectAssignmentNotification($project));
        }
    }

    private function inferAreasFromProject(Project $project): array
    {
        // Map project types to functional areas
        return match ($project->type) {
            'consultoria'     => ['RRHH', 'Operaciones', 'Finanzas'],
            'infraestructura' => ['TI', 'Operaciones'],
            'desarrollo_web'  => ['TI', 'Comercial'],
            default           => [],
        };
    }
}
