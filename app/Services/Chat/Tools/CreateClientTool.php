<?php

namespace App\Services\Chat\Tools;

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class CreateClientTool extends Tool
{
    public function name(): string { return 'create_client'; }

    public function description(): string
    {
        return 'Crea un cliente nuevo en el sistema. '
             . 'Usar cuando el usuario pida explícitamente registrar un cliente.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string', 'description' => 'Nombre o razon social'],
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
            'required' => ['name'],
        ];
    }

    public function authorize(User $user): bool
    {
        return (bool) config('chatbot.tools.allow_mutations', true)
            && $user->can('clients.create');
    }

    public function execute(array $args, User $user): array
    {
        $v = Validator::make($args, [
            'name' => ['required', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'primary_contact_name' => ['nullable', 'string', 'max:255'],
            'primary_contact_email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'municipality' => ['nullable', 'string', 'max:255'],
            'parish' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        if ($v->fails()) {
            return ['error' => 'validacion_fallida', 'details' => $v->errors()->toArray()];
        }

        $client = Client::create($v->validated());

        return [
            'ok' => true,
            'message' => 'Cliente creado exitosamente.',
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
