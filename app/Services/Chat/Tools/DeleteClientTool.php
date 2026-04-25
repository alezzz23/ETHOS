<?php

namespace App\Services\Chat\Tools;

use App\Models\Client;
use App\Models\User;

class DeleteClientTool extends Tool
{
    public function name(): string { return 'delete_client'; }

    public function description(): string
    {
        return 'Elimina un cliente por ID (y sus proyectos asociados). Requiere confirm=true. '
             . 'Usar solo si el usuario confirma explícitamente la eliminación.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'client_id' => ['type' => 'integer', 'description' => 'ID del cliente a eliminar'],
                'confirm' => ['type' => 'boolean', 'description' => 'Debe ser true para ejecutar la eliminación'],
            ],
            'required' => ['client_id', 'confirm'],
        ];
    }

    public function authorize(User $user): bool
    {
        return (bool) config('chatbot.tools.allow_mutations', true)
            && (bool) config('chatbot.tools.allow_destructive', true)
            && $user->can('clients.delete');
    }

    public function execute(array $args, User $user): array
    {
        $id = (int) ($args['client_id'] ?? 0);
        $confirm = (bool) ($args['confirm'] ?? false);

        if ($id <= 0) {
            return ['error' => 'client_id invalido'];
        }
        if (! $confirm) {
            return ['error' => 'Operacion cancelada: confirm debe ser true'];
        }

        $client = Client::query()->find($id);
        if (! $client) {
            return ['error' => 'Cliente no encontrado'];
        }

        $deleted = [
            'id' => $client->id,
            'name' => $client->name,
            'primary_contact_name' => $client->primary_contact_name,
            'primary_contact_email' => $client->primary_contact_email,
            'projects_count' => $client->projects()->count(),
        ];

        $client->projects()->delete();
        $client->delete();

        return [
            'ok' => true,
            'message' => 'Cliente eliminado exitosamente.',
            'client' => $deleted,
        ];
    }
}
