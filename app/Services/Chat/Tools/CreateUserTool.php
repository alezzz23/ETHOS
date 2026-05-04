<?php

namespace App\Services\Chat\Tools;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class CreateUserTool extends Tool
{
    public function name(): string { return 'create_user'; }

    public function description(): string
    {
        return 'Crea un usuario nuevo en el sistema con rol asignado. '
             . 'Usar solo cuando el usuario ya haya proporcionado nombre, email, contraseña y rol. '
             . 'Si faltan datos o conviene capturarlos de forma guiada, usa open_user_creation_form.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string', 'description' => 'Nombre completo del usuario'],
                'email' => ['type' => 'string', 'description' => 'Correo del usuario'],
                'password' => ['type' => 'string', 'description' => 'Clave temporal o definitiva (min 8)'],
                'role' => ['type' => 'string', 'description' => 'Rol existente (ej: super_admin, marketing, consultor)'],
            ],
            'required' => ['name', 'email', 'password', 'role'],
        ];
    }

    public function authorize(User $user): bool
    {
        return (bool) config('chatbot.tools.allow_mutations', true)
            && $user->can('users.manage');
    }

    public function execute(array $args, User $user): array
    {
        $v = Validator::make($args, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        if ($v->fails()) {
            return ['error' => 'validacion_fallida', 'details' => $v->errors()->toArray()];
        }

        $new = User::create([
            'name' => $args['name'],
            'email' => $args['email'],
            'password' => Hash::make((string) $args['password']),
        ]);

        $role = Role::query()->where('name', (string) $args['role'])->first();
        if ($role) {
            $new->assignRole($role);
        }

        return [
            'ok' => true,
            'message' => 'Usuario creado exitosamente.',
            'user' => [
                'id' => $new->id,
                'name' => $new->name,
                'email' => $new->email,
                'role' => $new->getRoleNames()->first(),
            ],
        ];
    }
}
