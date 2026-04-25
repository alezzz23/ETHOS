<?php

namespace App\Services\Chat\Tools;

use App\Models\User;

class SearchUsersTool extends Tool
{
    public function name(): string { return 'search_users'; }

    public function description(): string
    {
        return 'Busca usuarios por nombre o email y retorna hasta 10 coincidencias. '
             . 'Usar cuando el usuario pida ubicar o listar usuarios específicos.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => [
                    'type' => 'string',
                    'description' => 'Texto a buscar (min 2 caracteres)',
                    'minLength' => 2,
                ],
            ],
            'required' => ['query'],
        ];
    }

    public function authorize(User $user): bool
    {
        return $user->can('users.manage');
    }

    public function execute(array $args, User $user): array
    {
        $q = trim((string) ($args['query'] ?? ''));
        if (mb_strlen($q) < 2) {
            return ['error' => 'query demasiado corta'];
        }

        $like = '%' . $q . '%';
        $rows = User::query()
            ->where(function ($w) use ($like) {
                $w->where('name', 'like', $like)
                  ->orWhere('email', 'like', $like);
            })
            ->with('roles:id,name')
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'email', 'created_at']);

        $users = $rows->map(function (User $row): array {
            return [
                'id' => $row->id,
                'name' => $row->name,
                'email' => $row->email,
                'role' => $row->getRoleNames()->first(),
                'created_at' => $row->created_at?->toDateTimeString(),
            ];
        })->values();

        return [
            'count' => $users->count(),
            'users' => $users,
        ];
    }
}
