<?php

namespace App\Services\Chat\Tools;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UpdateUserTool extends Tool
{
    public function name(): string { return 'update_user'; }

    public function description(): string
    {
        return 'Actualiza datos de un usuario existente (nombre, email, password o rol). '
             . 'Usar cuando el usuario solicite modificar un usuario.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'user_id' => ['type' => 'integer', 'description' => 'ID del usuario a editar'],
                'name' => ['type' => 'string', 'description' => 'Nuevo nombre'],
                'email' => ['type' => 'string', 'description' => 'Nuevo correo'],
                'password' => ['type' => 'string', 'description' => 'Nueva clave (min 8)'],
                'role' => ['type' => 'string', 'description' => 'Nuevo rol (debe existir)'],
            ],
            'required' => ['user_id'],
        ];
    }

    public function authorize(User $user): bool
    {
        return (bool) config('chatbot.tools.allow_mutations', true)
            && $user->can('users.manage');
    }

    public function execute(array $args, User $user): array
    {
        $userId = (int) ($args['user_id'] ?? 0);
        if ($userId <= 0) {
            return ['error' => 'user_id invalido'];
        }

        $target = User::query()->find($userId);
        if (! $target) {
            return ['error' => 'Usuario no encontrado'];
        }

        $v = Validator::make($args, [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email:rfc', 'max:255', Rule::unique('users', 'email')->ignore($target->id)],
            'password' => ['sometimes', 'required', 'string', 'min:8', 'max:255'],
            'role' => ['sometimes', 'required', 'string', 'exists:roles,name'],
        ]);

        if ($v->fails()) {
            return ['error' => 'validacion_fallida', 'details' => $v->errors()->toArray()];
        }

        $payload = [];
        if (array_key_exists('name', $args)) {
            $payload['name'] = (string) $args['name'];
        }
        if (array_key_exists('email', $args)) {
            $payload['email'] = (string) $args['email'];
        }
        if (array_key_exists('password', $args)) {
            $payload['password'] = Hash::make((string) $args['password']);
        }

        if (empty($payload) && ! array_key_exists('role', $args)) {
            return ['error' => 'No hay cambios para aplicar'];
        }

        if (! empty($payload)) {
            $target->update($payload);
        }

        if (array_key_exists('role', $args)) {
            $target->syncRoles([(string) $args['role']]);
        }

        $target->refresh();

        return [
            'ok' => true,
            'message' => 'Usuario actualizado exitosamente.',
            'user' => [
                'id' => $target->id,
                'name' => $target->name,
                'email' => $target->email,
                'role' => $target->getRoleNames()->first(),
            ],
        ];
    }
}
