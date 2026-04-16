<?php

namespace App\Services\Chat\Tools;

use App\Models\User;

/**
 * Contrato que toda tool debe cumplir para ser registrada.
 *
 * Las tools se exponen al LLM siguiendo el formato OpenAI/OpenRouter:
 *
 *   { type: "function", function: { name, description, parameters: JSON-Schema } }
 */
abstract class Tool
{
    /** Nombre único (snake_case), usado por el LLM para llamar. */
    abstract public function name(): string;

    /** Descripción para el LLM — debe incluir cuándo llamar. */
    abstract public function description(): string;

    /** JSON-Schema de los parámetros aceptados. */
    abstract public function parameters(): array;

    /**
     * Ejecuta la tool. Debe retornar un array JSON-serializable.
     * Cualquier excepción será capturada y convertida en un resultado de error.
     */
    abstract public function execute(array $args, User $user): array;

    /**
     * Define qué permiso/rol mínimo es necesario para invocar esta tool.
     * Por defecto: cualquiera con `admin.access`.
     */
    public function authorize(User $user): bool
    {
        return $user->can('admin.access');
    }

    /** Schema OpenAI-compatible listo para enviar al LLM. */
    public function toSchema(): array
    {
        return [
            'type'     => 'function',
            'function' => [
                'name'        => $this->name(),
                'description' => $this->description(),
                'parameters'  => $this->parameters(),
            ],
        ];
    }
}
