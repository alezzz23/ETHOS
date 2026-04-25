<?php

namespace App\Services\Chat\Tools;

use App\Models\User;

class DeleteUserTool extends Tool
{
    public function name(): string { return 'delete_user'; }

    public function description(): string
    {
        return 'Elimina un usuario por ID. Requiere confirm=true. '
             . 'Usar solo cuando el usuario confirme explícitamente la eliminación.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'user_id' => ['type' => 'integer', 'description' => 'ID del usuario a eliminar'],
                'confirm' => ['type' => 'boolean', 'description' => 'Debe ser true para ejecutar la eliminación'],
            ],
            'required' => ['user_id', 'confirm'],
        ];
    }

    public function authorize(User $user): bool
    {
        return (bool) config('chatbot.tools.allow_mutations', true)
            && (bool) config('chatbot.tools.allow_destructive', true)
            && $user->can('users.manage');
    }

    public function execute(array $args, User $user): array
    {
        $id = (int) ($args['user_id'] ?? 0);
        $confirm = (bool) ($args['confirm'] ?? false);

        if ($id <= 0) {
            return ['error' => 'user_id invalido'];
        }
        if (! $confirm) {
            return ['error' => 'Operacion cancelada: confirm debe ser true'];
        }
        if ($id === $user->id) {
            return ['error' => 'No puedes eliminar tu propia cuenta'];
        }

        $target = User::query()->find($id);
        if (! $target) {
            return ['error' => 'Usuario no encontrado'];
        }

        $deleted = [
            'id' => $target->id,
            'name' => $target->name,
            'email' => $target->email,
            'role' => $target->getRoleNames()->first(),
        ];

        $target->delete();

        return [
            'ok' => true,
            'message' => 'Usuario eliminado exitosamente.',
            'user' => $deleted,
        ];
    }
}
