<?php

namespace App\Services\Chat\Tools;

use App\Models\Project;
use App\Models\User;

class GetProjectStatusTool extends Tool
{
    public function name(): string { return 'get_project_status'; }

    public function description(): string
    {
        return 'Obtiene el estado actual, fase, % de avance y checklist pendiente de un proyecto por su ID. '
             . 'Úsalo cuando el usuario pregunte por un proyecto específico (ej. "¿cómo va el proyecto 42?").';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'project_id' => ['type' => 'integer', 'description' => 'ID numérico del proyecto'],
            ],
            'required' => ['project_id'],
        ];
    }

    public function execute(array $args, User $user): array
    {
        $id = (int) ($args['project_id'] ?? 0);
        if ($id <= 0) return ['error' => 'project_id inválido'];

        $p = Project::query()
            ->with(['client:id,name', 'assignedTo:id,name'])
            ->find($id, ['id', 'client_id', 'assigned_to', 'title', 'status', 'progress',
                         'priority_level', 'starts_at', 'ends_at', 'estimated_budget', 'final_budget']);

        if (! $p) return ['error' => "Proyecto {$id} no encontrado."];

        $pendingTasks = $p->tasks()->where('status', '!=', 'completed')->count();

        return [
            'id'              => $p->id,
            'title'           => $p->title,
            'status'          => $p->status,
            'progress'        => (float) $p->progress,
            'priority'        => $p->priority_level,
            'client'          => $p->client?->name,
            'assigned_to'     => $p->assignedTo?->name,
            'starts_at'       => $p->starts_at?->toDateString(),
            'ends_at'         => $p->ends_at?->toDateString(),
            'budget_estimated'=> (float) $p->estimated_budget,
            'budget_final'    => (float) $p->final_budget,
            'pending_tasks'   => $pendingTasks,
        ];
    }
}
