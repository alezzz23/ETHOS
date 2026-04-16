<?php

namespace App\Services\Chat\Tools;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class ToolRegistry
{
    /** @var array<string, Tool> */
    private array $tools = [];

    public function register(Tool $tool): void
    {
        $this->tools[$tool->name()] = $tool;
    }

    public function all(): array
    {
        return $this->tools;
    }

    public function has(string $name): bool
    {
        return isset($this->tools[$name]);
    }

    public function schemas(): array
    {
        return array_values(array_map(fn (Tool $t) => $t->toSchema(), $this->tools));
    }

    /**
     * Ejecuta la tool con autorización. Retorna siempre un array JSON-serializable.
     */
    public function dispatch(string $name, array $args, User $user): array
    {
        if (! $this->has($name)) {
            return ['error' => "Tool no registrada: {$name}"];
        }

        $tool = $this->tools[$name];

        if (! $tool->authorize($user)) {
            Log::warning('tool_unauthorized', ['tool' => $name, 'user_id' => $user->id]);
            return ['error' => 'No autorizado para usar esta herramienta.'];
        }

        try {
            $result = $tool->execute($args, $user);
            Log::info('tool_executed', [
                'tool' => $name, 'user_id' => $user->id,
                'args' => $args,
            ]);
            return $result;
        } catch (\Throwable $e) {
            Log::error('tool_exception', ['tool' => $name, 'error' => $e->getMessage()]);
            return ['error' => 'Error ejecutando la herramienta.'];
        }
    }
}
