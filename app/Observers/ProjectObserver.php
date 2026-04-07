<?php

namespace App\Observers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Jobs\SendSatisfactionSurveyJob;
use App\Notifications\NewProjectAssignmentNotification;
use App\Notifications\ProjectApprovedNotification;
use Carbon\Carbon;

class ProjectObserver
{
    /** Deviation threshold in percent that triggers an alert */
    private const DEVIATION_THRESHOLD = 20.0;

    // ─── Hooks ────────────────────────────────────────────────────

    /**
     * First time a project is saved: lock fields and notify consultants.
     */
    public function created(Project $project): void
    {
        // Lock fields immediately
        $project->locked_fields_at = now();
        $project->saveQuietly();

        $this->notifyConsultants($project);
    }

    /**
     * Handle status transitions and deviation checks on update.
     */
    public function updated(Project $project): void
    {
        $this->handleStatusTransition($project);
        $this->checkDeviation($project);
    }

    // ─── Transition handlers ──────────────────────────────────────

    private function handleStatusTransition(Project $project): void
    {
        if (!$project->wasChanged('status')) {
            return;
        }

        match ($project->status) {
            Project::STATUS_EN_ANALISIS  => $this->onEnAnalisis($project),
            Project::STATUS_APROBADO     => $this->onAprobado($project),
            Project::STATUS_EN_EJECUCION => $this->onEnEjecucion($project),
            Project::STATUS_CERRADO      => $this->onCerrado($project),
            default                      => null,
        };
    }

    private function onEnAnalisis(Project $project): void
    {
        // Re-notify consultants if the project moves back to analysis
        // (only fire if it was previously capturado)
        if ($project->getOriginal('status') === Project::STATUS_CAPTURADO) {
            $this->notifyConsultants($project);
        }
    }

    private function onAprobado(Project $project): void
    {
        $project->approved_at = now();
        $project->recalculatePriorityScore();
        $project->saveQuietly();

        // Notify leader
        if ($project->leader_id) {
            $leader = User::find($project->leader_id);
            $leader?->notify(new ProjectApprovedNotification($project));
        }

        // Generate base tasks if checklist doesn't exist yet
        if ($project->checklists()->doesntExist() && $project->proposals()->where('status', 'approved')->exists()) {
            $approvedProposal = $project->proposals()->where('status', 'approved')->latest()->first();
            if ($approvedProposal) {
                app(\App\Services\ChecklistGeneratorService::class)->generateFromProposal($approvedProposal);
            }
        }
    }

    private function onEnEjecucion(Project $project): void
    {
        $project->execution_started_at = now();
        $project->saveQuietly();
    }

    private function onCerrado(Project $project): void
    {
        $project->closed_at = now();
        $project->saveQuietly();

        SendSatisfactionSurveyJob::dispatch($project)->delay(now()->addHour());
    }

    // ─── Deviation logic ──────────────────────────────────────────

    private function checkDeviation(Project $project): void
    {
        if (!$project->wasChanged('actual_hours')) {
            return;
        }

        $project->recalculateDeviation();

        $deviation = abs($project->deviation_percent ?? 0);
        if ($deviation < self::DEVIATION_THRESHOLD) {
            return;
        }

        // Notify consultora (assigned_to) and leader
        $notifiable = collect();
        if ($project->assigned_to) {
            $notifiable->push(User::find($project->assigned_to));
        }
        if ($project->leader_id && $project->leader_id !== $project->assigned_to) {
            $notifiable->push(User::find($project->leader_id));
        }

        $notification = new \App\Notifications\ProjectDeviationAlertNotification(
            $project,
            round($project->deviation_percent ?? 0, 1)
        );

        foreach ($notifiable->filter() as $user) {
            $user->notify($notification);
        }
    }

    // ─── Helpers ──────────────────────────────────────────────────

    private function notifyConsultants(Project $project): void
    {
        $functionalAreas = $this->inferAreasFromProject($project);

        $consultants = User::whereHas('roles', fn ($q) => $q->where('name', 'consultor'))
            ->when($functionalAreas, function ($q) use ($functionalAreas) {
                $q->whereHas('functionalAreas', fn ($q2) => $q2->whereIn('functional_area', $functionalAreas));
            })
            ->get();

        if ($consultants->isEmpty()) {
            $consultants = User::whereHas('roles', fn ($q) => $q->where('name', 'consultor'))->get();
        }

        $dueDate = Carbon::now()->addWeekdays(3);

        foreach ($consultants as $consultant) {
            Task::create([
                'project_id'  => $project->id,
                'assigned_to' => $consultant->id,
                'type'        => 'proposal_upload',
                'title'       => 'Analizar viabilidad y cotizar servicio',
                'description' => "El proyecto \"{$project->title}\" fue capturado y requiere análisis. Vence en 3 días hábiles.",
                'due_date'    => $dueDate,
                'status'      => 'pending',
            ]);

            try {
                $consultant->notify(new NewProjectAssignmentNotification($project));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning(
                    "Could not send project assignment notification to consultant {$consultant->id}: " . $e->getMessage()
                );
            }
        }
    }

    private function inferAreasFromProject(Project $project): array
    {
        // If service is already selected, use its functional_areas
        if ($project->service?->functional_areas) {
            $areas = $project->service->functional_areas;
            if (is_string($areas)) {
                $areas = json_decode($areas, true) ?? [];
            }
            return (array) $areas;
        }

        return match ($project->type) {
            'consultoria'     => ['RRHH', 'Operaciones', 'Finanzas'],
            'infraestructura' => ['TI', 'Operaciones'],
            'desarrollo_web'  => ['TI', 'Comercial'],
            default           => [],
        };
    }
}
