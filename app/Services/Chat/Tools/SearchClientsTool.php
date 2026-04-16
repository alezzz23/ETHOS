<?php

namespace App\Services\Chat\Tools;

use App\Models\Client;
use App\Models\User;

class SearchClientsTool extends Tool
{
    public function name(): string { return 'search_clients'; }

    public function description(): string
    {
        return 'Busca clientes por nombre, razón social o correo de contacto. '
             . 'Retorna hasta 10 coincidencias.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'description' => 'Texto a buscar (mín 2 caracteres)', 'minLength' => 2],
            ],
            'required' => ['query'],
        ];
    }

    public function execute(array $args, User $user): array
    {
        $q = trim((string) ($args['query'] ?? ''));
        if (mb_strlen($q) < 2) return ['error' => 'query demasiado corta'];

        $like = '%' . $q . '%';
        $rows = Client::query()
            ->where(function ($w) use ($like) {
                $w->where('name', 'like', $like)
                  ->orWhere('primary_contact_name',  'like', $like)
                  ->orWhere('primary_contact_email', 'like', $like)
                  ->orWhere('secondary_contact_email','like', $like);
            })
            ->limit(10)
            ->get(['id', 'name', 'primary_contact_name', 'primary_contact_email', 'status', 'industry']);

        return [
            'count'   => $rows->count(),
            'clients' => $rows->toArray(),
        ];
    }
}
