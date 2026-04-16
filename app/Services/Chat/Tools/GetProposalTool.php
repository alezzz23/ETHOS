<?php

namespace App\Services\Chat\Tools;

use App\Models\Proposal;
use App\Models\User;

class GetProposalTool extends Tool
{
    public function name(): string { return 'get_proposal'; }

    public function description(): string
    {
        return 'Obtiene el resumen financiero y estado de una propuesta por su ID.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'proposal_id' => ['type' => 'integer'],
            ],
            'required' => ['proposal_id'],
        ];
    }

    public function execute(array $args, User $user): array
    {
        $id = (int) ($args['proposal_id'] ?? 0);
        if ($id <= 0) return ['error' => 'proposal_id inválido'];

        $p = Proposal::query()
            ->with(['project:id,title', 'service:id,name'])
            ->find($id, [
                'id', 'project_id', 'service_id', 'status', 'client_size',
                'hourly_rate', 'margin_percent', 'total_hours', 'adjusted_hours',
                'price_min', 'price_max', 'sent_at', 'approved_at', 'rejected_at',
            ]);

        if (! $p) return ['error' => "Propuesta {$id} no encontrada."];

        return [
            'id'             => $p->id,
            'project'        => $p->project?->title,
            'service'        => $p->service?->name,
            'status'         => $p->status,
            'client_size'    => $p->client_size,
            'hourly_rate'    => (float) $p->hourly_rate,
            'margin_percent' => (float) $p->margin_percent,
            'hours'          => [
                'total'    => (float) $p->total_hours,
                'adjusted' => (float) $p->adjusted_hours,
            ],
            'price_range' => [
                'min' => (float) $p->price_min,
                'max' => (float) $p->price_max,
            ],
            'sent_at'     => $p->sent_at?->toDateString(),
            'approved_at' => $p->approved_at?->toDateString(),
            'rejected_at' => $p->rejected_at?->toDateString(),
        ];
    }
}
