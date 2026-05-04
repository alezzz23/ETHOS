<?php

namespace App\Services\Chat\Tools;

use App\Models\User;

class OpenCreationFormTool extends Tool
{
    public function __construct(
        private string $entity,
        private string $permission,
        private string $label,
    ) {}

    public function name(): string
    {
        return "open_{$this->entity}_creation_form";
    }

    public function description(): string
    {
        return "Abre un formulario guiado dentro del chat para crear un {$this->label}. "
            . "Úsala cuando el usuario pida crear o registrar un {$this->label} y falten datos, "
            . "o cuando sea más cómodo capturarlos en un formulario. "
            . "Si ya conoces algunos valores, envíalos en defaults para prellenar campos.";
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'defaults' => [
                    'type' => 'object',
                    'description' => 'Valores iniciales para prellenar el formulario con datos ya proporcionados por el usuario.',
                    'additionalProperties' => true,
                ],
            ],
        ];
    }

    public function authorize(User $user): bool
    {
        return (bool) config('chatbot.tools.allow_mutations', true)
            && $user->can($this->permission);
    }

    public function execute(array $args, User $user): array
    {
        $defaults = is_array($args['defaults'] ?? null) ? $args['defaults'] : [];

        return [
            'ok' => true,
            'message' => "Formulario de {$this->label} listo.",
            'chat_form_request' => [
                'entity' => $this->entity,
                'defaults' => $defaults,
            ],
        ];
    }
}