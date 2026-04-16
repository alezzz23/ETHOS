<?php

namespace App\Services\Chat\Tools;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class GetMetricsTool extends Tool
{
    public function name(): string { return 'get_metrics'; }

    public function description(): string
    {
        return 'Obtiene métricas agregadas del sistema según un alcance (scope). '
             . 'Scopes válidos: "general", "projects", "clients", "tasks".';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'scope' => [
                    'type' => 'string',
                    'enum' => ['general', 'projects', 'clients', 'tasks'],
                    'default' => 'general',
                ],
            ],
        ];
    }

    public function execute(array $args, User $user): array
    {
        $scope = $args['scope'] ?? 'general';

        return match ($scope) {
            'projects' => $this->projects(),
            'clients'  => $this->clients(),
            'tasks'    => $this->tasks(),
            default    => $this->general(),
        };
    }

    private function general(): array
    {
        return [
            'users'    => User::count(),
            'clients'  => Client::count(),
            'projects' => Project::count(),
            'tasks'    => Task::count(),
        ];
    }

    private function projects(): array
    {
        return [
            'total'         => Project::count(),
            'by_status'     => Project::query()->selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status')->all(),
            'avg_progress'  => round((float) Project::avg('progress'), 1),
        ];
    }

    private function clients(): array
    {
        return [
            'total'       => Client::count(),
            'by_status'   => Client::query()->selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status')->all(),
            'by_industry' => Client::query()->selectRaw('industry, COUNT(*) as c')->groupBy('industry')->orderByDesc('c')->limit(10)->pluck('c', 'industry')->all(),
        ];
    }

    private function tasks(): array
    {
        return [
            'total'     => Task::count(),
            'pending'   => Task::where('status', '!=', 'completed')->count(),
            'overdue'   => Task::where('status', '!=', 'completed')->whereNotNull('due_date')->where('due_date', '<', now())->count(),
            'by_status' => Task::query()->selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status')->all(),
        ];
    }
}
