<?php

namespace App\Services\Chat\Tools;

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UpdateClientTool extends Tool
{
    public function name(): string { return 'update_client'; }

    public function description(): string
    {
        return 'Actualiza un cliente existente por ID. '
             . 'Usar cuando el usuario solicite modificar datos de un cliente.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'client_id' => ['type' => 'integer', 'description' => 'ID del cliente a editar'],
                'name' => ['type' => 'string'],
                'industry' => ['type' => 'string'],
                'primary_contact_name' => ['type' => 'string'],
                'primary_contact_email' => ['type' => 'string'],
                'phone' => ['type' => 'string'],
                'notes' => ['type' => 'string'],
                'address' => ['type' => 'string'],
                'city' => ['type' => 'string'],
                'state' => ['type' => 'string'],
                'country' => ['type' => 'string'],
                'municipality' => ['type' => 'string'],
                'parish' => ['type' => 'string'],
                'latitude' => ['type' => 'number'],
                'longitude' => ['type' => 'number'],
            ],
            'required' => ['client_id'],
        ];
    }

    public function authorize(User $user): bool
    {
        return (bool) config('chatbot.tools.allow_mutations', true)
            && $user->can('clients.edit');
    }

    public function execute(array $args, User $user): array
    {
        $id = (int) ($args['client_id'] ?? 0);
        if ($id <= 0) {
            return ['error' => 'client_id invalido'];
        }

        $client = Client::query()->find($id);
        if (! $client) {
            return ['error' => 'Cliente no encontrado'];
        }

        $v = Validator::make($args, [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'industry' => ['sometimes', 'nullable', 'string', 'max:255'],
            'primary_contact_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'primary_contact_email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:255'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'city' => ['sometimes', 'nullable', 'string', 'max:255'],
            'state' => ['sometimes', 'nullable', 'string', 'max:255'],
            'country' => ['sometimes', 'nullable', 'string', 'max:255'],
            'municipality' => ['sometimes', 'nullable', 'string', 'max:255'],
            'parish' => ['sometimes', 'nullable', 'string', 'max:255'],
            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
        ]);

        if ($v->fails()) {
            return ['error' => 'validacion_fallida', 'details' => $v->errors()->toArray()];
        }

        $payload = $v->validated();
        unset($payload['client_id']);

        if (empty($payload)) {
            return ['error' => 'No hay cambios para aplicar'];
        }

        $client->update($payload);
        $client->refresh();

        return [
            'ok' => true,
            'message' => 'Cliente actualizado exitosamente.',
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'industry' => $client->industry,
                'primary_contact_name' => $client->primary_contact_name,
                'primary_contact_email' => $client->primary_contact_email,
                'phone' => $client->phone,
            ],
        ];
    }
}
