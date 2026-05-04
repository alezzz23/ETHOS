<?php

namespace App\Services\Chat;

use App\Http\Controllers\Admin\ServiceController;
use App\Models\Client;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;

class ChatFormFactory
{
    public function entities(): array
    {
        return ['user', 'client', 'project', 'service'];
    }

    public function canOpen(string $entity, $user): bool
    {
        if (! config('chatbot.tools.allow_mutations', true) || ! $user) {
            return false;
        }

        return match ($entity) {
            'user' => $user->can('users.manage'),
            'client' => $user->can('clients.create'),
            'project' => $user->can('projects.create'),
            'service' => $user->can('services.create'),
            default => false,
        };
    }

    public function makeCreationForm(string $entity, array $defaults = []): ?array
    {
        return match ($entity) {
            'user' => $this->userForm($defaults),
            'client' => $this->clientForm($defaults),
            'project' => $this->projectForm($defaults),
            'service' => $this->serviceForm($defaults),
            default => null,
        };
    }

    private function userForm(array $defaults): array
    {
        $roles = Role::query()
            ->orderBy('name')
            ->get(['name'])
            ->map(fn (Role $role): array => [
                'value' => $role->name,
                'label' => $role->name,
            ])
            ->values()
            ->all();

        return $this->baseForm(
            entity: 'user',
            entityLabel: 'usuario',
            title: 'Crear usuario',
            description: 'Completa los datos del nuevo usuario y el sistema lo registrará al enviar el formulario.',
            submitUrl: route('users.store'),
            fields: [
                $this->field('name', 'Nombre completo', 'text', true, 6, $defaults, placeholder: 'Ej. Ana Pérez'),
                $this->field('email', 'Correo electrónico', 'email', true, 6, $defaults, placeholder: 'ana@ethos.com'),
                $this->field('password', 'Contraseña temporal', 'password', true, 6, $defaults, help: 'Debe tener al menos 8 caracteres.'),
                $this->field('password_confirmation', 'Confirmar contraseña', 'password', true, 6, $defaults),
                $this->field('role', 'Rol', 'select', true, 12, $defaults, options: $roles, placeholder: 'Selecciona un rol'),
            ],
        );
    }

    private function clientForm(array $defaults): array
    {
        return $this->baseForm(
            entity: 'client',
            entityLabel: 'cliente',
            title: 'Crear cliente',
            description: 'Registra un cliente nuevo sin salir del chatbot.',
            submitUrl: route('clients.store'),
            fields: [
                $this->field('name', 'Nombre o razón social', 'text', true, 12, $defaults, placeholder: 'Ej. Acme Corp'),
                $this->field('industry', 'Industria', 'text', false, 6, $defaults),
                $this->field('phone', 'Teléfono', 'text', false, 6, $defaults),
                $this->field('primary_contact_name', 'Contacto principal', 'text', false, 6, $defaults),
                $this->field('primary_contact_email', 'Correo del contacto', 'email', false, 6, $defaults),
                $this->field('notes', 'Notas', 'textarea', false, 12, $defaults, placeholder: 'Contexto adicional sobre el cliente'),
            ],
        );
    }

    private function projectForm(array $defaults): array
    {
        $clients = Client::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Client $client): array => [
                'value' => (string) $client->id,
                'label' => $client->name,
            ])
            ->values()
            ->all();

        $today = Carbon::today()->format('Y-m-d');

        return $this->baseForm(
            entity: 'project',
            entityLabel: 'proyecto',
            title: 'Crear proyecto',
            description: 'Captura rápida del proyecto desde el chat. Luego podrás completarlo desde el módulo de proyectos.',
            submitUrl: route('projects.store'),
            fields: [
                $this->field('client_id', 'Cliente', 'select', true, 12, $defaults, options: $clients, placeholder: 'Selecciona un cliente'),
                $this->field('title', 'Título del proyecto', 'text', true, 12, $defaults, placeholder: 'Ej. Implementación ISO 9001'),
                $this->field('description', 'Descripción', 'textarea', false, 12, $defaults, placeholder: 'Objetivo, alcance o notas iniciales'),
                $this->field('type', 'Tipo', 'text', false, 6, $defaults, placeholder: 'Ej. Consultoría'),
                $this->field('subtype', 'Subtipo', 'text', false, 6, $defaults, placeholder: 'Ej. Diagnóstico'),
                $this->field('urgency', 'Urgencia', 'select', false, 6, $defaults, options: $this->labelValueOptions(['baja', 'media', 'alta']), placeholder: 'Selecciona la urgencia'),
                $this->field('complexity', 'Complejidad', 'select', false, 6, $defaults, options: $this->labelValueOptions(['baja', 'media', 'alta']), placeholder: 'Selecciona la complejidad'),
                $this->field('starts_at', 'Fecha de inicio', 'date', false, 6, $defaults, min: $today),
                $this->field('estimated_budget', 'Presupuesto estimado', 'number', false, 3, $defaults, min: '0', step: '0.01'),
                $this->field('currency', 'Moneda', 'select', false, 3, $defaults + ['currency' => $defaults['currency'] ?? 'USD'], options: $this->currencyOptions(), placeholder: 'Moneda'),
            ],
        );
    }

    private function serviceForm(array $defaults): array
    {
        $functionalAreas = collect(ServiceController::FUNCTIONAL_AREAS)
            ->map(fn (string $area): array => ['value' => $area, 'label' => $area])
            ->values()
            ->all();

        $clientTypes = collect(ServiceController::CLIENT_TYPES)
            ->map(fn (string $label, string $value): array => ['value' => $value, 'label' => $label])
            ->values()
            ->all();

        return $this->baseForm(
            entity: 'service',
            entityLabel: 'servicio',
            title: 'Crear servicio',
            description: 'Registra un servicio nuevo con su descripción y público objetivo desde el mismo chat.',
            submitUrl: route('services.store'),
            fields: [
                $this->field('short_name', 'Nombre corto', 'text', true, 12, $defaults, placeholder: 'Ej. Diagnóstico organizacional'),
                $this->field('description', 'Descripción', 'textarea', true, 12, $defaults, placeholder: 'Describe alcance, entregables y propósito del servicio'),
                $this->field('functional_areas', 'Áreas funcionales', 'checkboxes', false, 12, $defaults, options: $functionalAreas),
                $this->field('client_types', 'Tipos de cliente', 'checkboxes', false, 12, $defaults, options: $clientTypes),
            ],
        );
    }

    private function baseForm(
        string $entity,
        string $entityLabel,
        string $title,
        string $description,
        string $submitUrl,
        array $fields,
    ): array {
        return [
            'entity' => $entity,
            'entity_label' => $entityLabel,
            'title' => $title,
            'description' => $description,
            'submit' => [
                'url' => $submitUrl,
                'method' => 'POST',
                'label' => 'Crear ' . $entityLabel,
            ],
            'fields' => $fields,
        ];
    }

    private function field(
        string $name,
        string $label,
        string $type,
        bool $required,
        int $span,
        array $defaults,
        array $options = [],
        ?string $placeholder = null,
        ?string $help = null,
        ?string $min = null,
        ?string $step = null,
    ): array {
        $field = [
            'name' => $name,
            'label' => $label,
            'type' => $type,
            'required' => $required,
            'span' => $span,
            'value' => $defaults[$name] ?? ($type === 'checkboxes' ? [] : ''),
        ];

        if ($options !== []) {
            $field['options'] = $options;
        }

        if ($placeholder !== null) {
            $field['placeholder'] = $placeholder;
        }

        if ($help !== null) {
            $field['help'] = $help;
        }

        if ($min !== null) {
            $field['min'] = $min;
        }

        if ($step !== null) {
            $field['step'] = $step;
        }

        return $field;
    }

    private function labelValueOptions(array $values): array
    {
        return array_map(fn (string $value): array => [
            'value' => $value,
            'label' => ucfirst($value),
        ], $values);
    }

    private function currencyOptions(): array
    {
        return [
            ['value' => 'USD', 'label' => 'USD'],
            ['value' => 'EUR', 'label' => 'EUR'],
            ['value' => 'VES', 'label' => 'VES'],
        ];
    }
}