<?php

namespace App\Services\Chat;

use Closure;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cliente para el proveedor LLM (OpenRouter / OpenAI-compatible).
 *
 * Dos modos:
 *  - complete(): request síncrono (retorna reply + tokens + ms).
 *  - stream(): consume SSE upstream e invoca callbacks por cada token.
 */
class LlmClient
{
    public function __construct() {}

    /**
     * @param array $messages
     * @param string|null $model
     * @param array $tools  Tool schemas (OpenAI-compatible).
     * @return array{ok:bool, reply?:string, tokens?:int, ms?:int, status?:int, error?:string, tool_calls?:array, finish_reason?:string, model?:string}
     */
    public function complete(array $messages, ?string $model = null, array $tools = []): array
    {
        $apiKey  = (string) config('chatbot.llm.api_key', '');
        $baseUrl = (string) config('chatbot.llm.base_url');
        $model ??= (string) config('chatbot.llm.models.primary');
        $timeout = (int)    config('chatbot.llm.timeout', 30);

        if ($apiKey === '') {
            return ['ok' => false, 'status' => 503, 'error' => 'api_key_missing'];
        }

        $start = (int) round(microtime(true) * 1000);

        $payload = [
            'model'       => $model,
            'messages'    => $messages,
            'temperature' => (float) config('chatbot.llm.temperature', 0.55),
            'max_tokens'  => (int)   config('chatbot.llm.max_tokens', 1200),
        ];
        if (! empty($tools)) {
            $payload['tools']       = $tools;
            $payload['tool_choice'] = 'auto';
        }

        /** @var Response $res */
        $res = Http::timeout($timeout)
            ->withHeaders($this->headers($apiKey))
            ->post($baseUrl . '/chat/completions', $payload);

        $ms = (int) round(microtime(true) * 1000) - $start;

        if (! $res->successful()) {
            Log::warning('llm_client_error', [
                'status' => $res->status(),
                'body'   => substr($res->body(), 0, 500),
            ]);
            return ['ok' => false, 'status' => $res->status(), 'error' => 'upstream_error', 'ms' => $ms];
        }

        $json      = $res->json();
        $reply     = trim((string) data_get($json, 'choices.0.message.content', ''));
        $tokens    = (int)          data_get($json, 'usage.total_tokens', 0);
        $toolCalls = (array)        data_get($json, 'choices.0.message.tool_calls', []);
        $finish    = (string)       data_get($json, 'choices.0.finish_reason', '');

        return [
            'ok'            => true,
            'reply'         => $reply,
            'tokens'        => $tokens,
            'ms'            => $ms,
            'model'         => $model,
            'tool_calls'    => $toolCalls,
            'finish_reason' => $finish,
        ];
    }

    /**
     * Consume SSE upstream. Invoca:
     *   $onDelta(string $token)
     *   $onUsage(int $prompt, int $completion)
     *   $onError(string $message)
     *   $shouldAbort(): bool    // se consulta entre chunks
     *
     * Si `$tools` está poblado y el modelo decide llamar herramientas,
     * el stream termina sin texto pero se retornan `tool_calls` en el resultado
     * (finish_reason === 'tool_calls').
     *
     * @return array{ok:bool, reply:string, tokens:int, ms:int, model:string, tool_calls?:array, finish_reason?:string}
     */
    public function stream(
        array $messages,
        Closure $onDelta,
        ?Closure $onUsage = null,
        ?Closure $onError = null,
        ?Closure $shouldAbort = null,
        ?string $model = null,
        array $tools = [],
    ): array {
        $apiKey  = (string) config('chatbot.llm.api_key', '');
        $baseUrl = (string) config('chatbot.llm.base_url');
        $model ??= (string) config('chatbot.llm.models.primary');
        $timeout = (int)    config('chatbot.llm.timeout', 30);

        $start       = (int) round(microtime(true) * 1000);
        $reply       = '';
        $promptTok   = 0;
        $compTok     = 0;
        $buffer      = '';
        $toolCalls   = []; // index => ['id', 'function' => ['name','arguments']]
        $finish      = '';

        if ($apiKey === '') {
            $onError && $onError('Asistente no disponible temporalmente.');
            return ['ok' => false, 'reply' => '', 'tokens' => 0, 'ms' => 0, 'model' => $model];
        }

        try {
            $client = new GuzzleClient(['timeout' => $timeout]);
            $payload = [
                'model'       => $model,
                'messages'    => $messages,
                'temperature' => (float) config('chatbot.llm.temperature', 0.55),
                'max_tokens'  => (int)   config('chatbot.llm.max_tokens', 1200),
                'stream'      => true,
            ];
            if (! empty($tools)) {
                $payload['tools']       = $tools;
                $payload['tool_choice'] = 'auto';
            }

            $up = $client->request('POST', $baseUrl . '/chat/completions', [
                'headers'        => $this->headers($apiKey),
                'json'           => $payload,
                'stream'         => true,
                'http_errors'    => false,
                'decode_content' => true,
            ]);

            if ($up->getStatusCode() >= 400) {
                $body = (string) $up->getBody()->getContents();
                Log::warning('llm_client_stream_error', [
                    'status' => $up->getStatusCode(),
                    'body'   => substr($body, 0, 500),
                ]);
                $onError && $onError('No se pudo obtener respuesta del asistente.');
                return ['ok' => false, 'reply' => '', 'tokens' => 0, 'ms' => 0, 'model' => $model];
            }

            $stream = $up->getBody();
            while (! $stream->eof()) {
                if ($shouldAbort && $shouldAbort()) { break; }

                $chunk = $stream->read(4096);
                if ($chunk === '' || $chunk === false) { continue; }
                $buffer .= $chunk;

                while (($sep = strpos($buffer, "\n\n")) !== false) {
                    $rawEvent = substr($buffer, 0, $sep);
                    $buffer   = substr($buffer, $sep + 2);

                    foreach (preg_split('/\r?\n/', $rawEvent) as $line) {
                        $line = trim($line);
                        if ($line === '' || ! str_starts_with($line, 'data:')) { continue; }

                        $data = trim(substr($line, 5));
                        if ($data === '[DONE]') { break 2; }

                        $decoded = json_decode($data, true);
                        if (! is_array($decoded)) { continue; }

                        $delta = (string) data_get($decoded, 'choices.0.delta.content', '');
                        if ($delta !== '') {
                            $reply .= $delta;
                            $onDelta($delta);
                        }

                        // Acumular deltas de tool_calls (OpenAI-compatible).
                        $tcDeltas = (array) data_get($decoded, 'choices.0.delta.tool_calls', []);
                        foreach ($tcDeltas as $tc) {
                            $idx = (int) ($tc['index'] ?? 0);
                            $toolCalls[$idx] ??= ['id' => '', 'type' => 'function', 'function' => ['name' => '', 'arguments' => '']];
                            if (! empty($tc['id'])) $toolCalls[$idx]['id'] = $tc['id'];
                            if (isset($tc['function']['name']))      $toolCalls[$idx]['function']['name']      .= (string) $tc['function']['name'];
                            if (isset($tc['function']['arguments'])) $toolCalls[$idx]['function']['arguments'] .= (string) $tc['function']['arguments'];
                        }

                        if ($fr = (string) data_get($decoded, 'choices.0.finish_reason', '')) $finish = $fr;

                        if (isset($decoded['usage'])) {
                            $promptTok = (int) data_get($decoded, 'usage.prompt_tokens', 0);
                            $compTok   = (int) data_get($decoded, 'usage.completion_tokens', 0);
                            $onUsage && $onUsage($promptTok, $compTok);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('llm_client_stream_exception', ['error' => $e->getMessage()]);
            $onError && $onError('Error de red con el proveedor IA.');
            return ['ok' => false, 'reply' => trim($reply), 'tokens' => $promptTok + $compTok, 'ms' => 0, 'model' => $model];
        }

        $ms = (int) round(microtime(true) * 1000) - $start;
        return [
            'ok'            => true,
            'reply'         => trim($reply),
            'tokens'        => $promptTok + $compTok,
            'ms'            => $ms,
            'model'         => $model,
            'tool_calls'    => array_values($toolCalls),
            'finish_reason' => $finish,
        ];
    }

    private function headers(string $apiKey): array
    {
        return [
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type'  => 'application/json',
            'Accept'        => 'text/event-stream',
            'HTTP-Referer'  => (string) config('app.url'),
            'X-Title'       => (string) config('app.name', 'ETHOS Admin'),
        ];
    }
}
