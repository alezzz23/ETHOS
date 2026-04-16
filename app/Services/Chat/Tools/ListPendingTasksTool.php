<?php

namespace App\Services\Chat\Tools;

use App\Models\Task;
use App\Models\User;

class ListPendingTasksTool extends Tool
{
    public function name(): string { return 'list_pending_tasks'; }

    public function description(): string
    {
        return 'Lista tareas pendientes filtradas opcionalmente por usuario asignado y/o proyecto. '
             . 'Por defecto retorna las 10 más próximas a vencer.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'user_id'    => ['type' => 'integer', 'description' => 'ID del usuario asignado (opcional)'],
                'project_id' => ['type' => 'integer', 'description' => 'ID del proyecto (opcional)'],
                'limit'      => ['type' => 'integer', 'description' => 'Máximo de resultados (por defecto 10)', 'default' => 10],
            ],
        ];
    }

    public function execute(array $args, User $user): array
    {
        $q = Task::query()->where('status', '!=', 'completed');
        if (! empty($args['user_id']))    $q->where('assigned_to', (int) $args['user_id']);
        if (! empty($args['project_id'])) $q->where('project_id',  (int) $args['project_id']);

        $tasks = $q->orderByRaw('due_date IS NULL, due_date ASC')
                   ->limit(min(50, (int) ($args['limit'] ?? 10)))
                   ->get(['id', 'project_id', 'assigned_to', 'title', 'status', 'due_date']);

        return [
            'count' => $tasks->count(),
            'tasks' => $tasks->map(fn ($t) => [
                'id'          => $t->id,
                'project_id'  => $t->project_id,
                'assigned_to' => $t->assigned_to,
                'title'       => $t->title,
                'status'      => $t->status,
                'due_date'    => $t->due_date?->toDateString(),
                'overdue'     => $t->is_overdue,
            ])->values()->all(),
        ];
    }
}
