<?php

namespace App\Services;

use App\Models\ChecklistItem;
use App\Models\Proposal;
use App\Models\ProjectChecklist;
use App\Models\Service;
use App\Models\ServiceProcess;

class ChecklistGeneratorService
{
    /**
     * Generate a levantamiento checklist for a project based on the approved proposal.
     * Returns the created ProjectChecklist.
     */
    public function generateFromProposal(Proposal $proposal): ProjectChecklist
    {
        $proposal->loadMissing(['service.processes.methods', 'service.requirements', 'project']);

        $service = $proposal->service;
        $project = $proposal->project;

        $checklist = ProjectChecklist::firstOrCreate(
            [
                'project_id'  => $project->id,
                'proposal_id' => $proposal->id,
            ],
            [
                'service_id'  => $service->id,
                'created_by'  => $proposal->created_by,
                'title'       => "Lista de levantamiento — {$service->short_name}",
                'status'      => 'active',
            ]
        );

        $checklist->fill([
            'service_id' => $service->id,
            'created_by' => $proposal->created_by,
            'title'      => "Lista de levantamiento — {$service->short_name}",
            'status'     => 'active',
        ]);

        if ($checklist->isDirty()) {
            $checklist->save();
        }

        if ($checklist->items()->exists()) {
            return $checklist->load('items');
        }

        $this->buildChecklistItems($checklist, $service);

        return $checklist->fresh('items');
    }

    private function buildChecklistItems(ProjectChecklist $checklist, Service $service): void
    {
        // Build items from service processes+methods
        $processes = $service->processes()->with('methods')->orderBy('order')->get();
        $order     = 1;

        foreach ($processes as $process) {
            foreach ($process->methods as $method) {
                ChecklistItem::create([
                    'project_checklist_id' => $checklist->id,
                    'title'                => "{$process->name_label}: {$method->method_label}",
                    'description'          => "Método {$method->method_label} para fase {$process->name_label}. {$method->standard_hours}h estimadas.",
                    'phase'                => $process->name,
                    'order'                => $order++,
                    'is_completed'         => false,
                ]);
            }
        }

        // If no processes defined yet, add a default item per service requirement
        if ($processes->isEmpty()) {
            foreach ($service->requirements()->orderBy('order')->get() as $req) {
                ChecklistItem::create([
                    'project_checklist_id' => $checklist->id,
                    'title'                => $req->description,
                    'phase'                => 'levantamiento',
                    'order'                => $order++,
                    'is_completed'         => false,
                ]);
            }
        }

    }
}
