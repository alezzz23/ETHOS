<?php

namespace App\Services\Chat;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SystemPromptBuilder
{
    public function __construct(private KnowledgeRetriever $kb) {}

    public function build(?string $userQuery = null): string
    {
        $now = now()->timezone(config('app.timezone', 'America/Caracas'));

        $stats = [
            'total_usuarios'  => User::count(),
            'total_clientes'  => class_exists(Client::class)  ? Client::count()  : 0,
            'total_proyectos' => class_exists(Project::class) ? Project::count() : 0,
        ];

        $kbContext = '';
        if ($userQuery) {
            $entries = $this->kb->retrieve($userQuery);
            if ($entries->isNotEmpty()) {
                $kbContext = $this->formatKb($entries);
            }
        }

        return <<<PROMPT
Eres ETHOS AI, el asistente inteligente exclusivo del panel de administración de ETHOS.

CONTEXTO ACTUAL DEL SISTEMA ({$now->format('d/m/Y H:i')}):
- Usuarios registrados: {$stats['total_usuarios']}
- Clientes registrados: {$stats['total_clientes']}
- Proyectos registrados: {$stats['total_proyectos']}

ROL Y CAPACIDADES:
- Ayudas a los administradores con análisis de datos, gestión de usuarios, proyectos y clientes.
- Interpretas y explicas estadísticas y métricas del sistema.
- Orientas sobre configuración y operaciones del panel.
- Redactas borradores de comunicaciones o reportes.
- Sugieres acciones concretas y pasos de solución a problemas operativos.
- Puedes responder en español o inglés según el idioma del usuario.

LIMITACIONES HONESTAS:
- No tienes acceso directo a leer o modificar la base de datos en tiempo real, solo estadísticas generales.
- Si necesitas hacer algo que requiere acceso real, indica claramente el camino a seguir.

ESTILO:
- Respuestas claras, estructuradas y profesionales.
- Usa listas y formato Markdown para mayor claridad cuando sea apropiado.
- Sé conciso pero completo.
{$kbContext}
PROMPT;
    }

    private function formatKb(Collection $entries): string
    {
        $summaryLen = (int) config('chatbot.rag.summary_len', 400);
        $out = "\n\nBASE DE CONOCIMIENTO RELEVANTE:\n";
        foreach ($entries as $entry) {
            $out .= "--- {$entry->title} ({$entry->category}) ---\n";
            $out .= ($entry->embedding_summary ?? Str::limit((string) $entry->content, $summaryLen, '')) . "\n\n";
        }
        return $out;
    }
}
