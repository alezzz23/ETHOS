<?php

namespace App\Support\Toon;

use InvalidArgumentException;
use RuntimeException;

class ToonCodec
{
    public const HEADER = 'TOON/1';

    public function encode(mixed $data): array
    {
        $stats = [
            'nodes' => 0,
            'objects' => 0,
            'arrays' => 0,
            'strings' => 0,
            'numbers' => 0,
            'booleans' => 0,
            'nulls' => 0,
            'numeric_strings' => 0,
        ];

        $lines = [self::HEADER];
        $this->encodeNode('/', $data, $lines, $stats);

        return [
            'content' => implode(PHP_EOL, $lines) . PHP_EOL,
            'stats' => $stats,
            'line_count' => count($lines),
        ];
    }

    public function decode(string $toonContent): array
    {
        $rows = preg_split('/\R/', $toonContent);
        $rows = array_values(array_filter($rows, static fn ($line) => trim((string) $line) !== ''));

        if ($rows === [] || trim((string) $rows[0]) !== self::HEADER) {
            throw new InvalidArgumentException('Contenido TOON inválido: encabezado faltante.');
        }

        $entries = [];
        for ($i = 1; $i < count($rows); $i++) {
            $parts = explode("\t", (string) $rows[$i], 3);
            if (count($parts) < 2) {
                throw new InvalidArgumentException("Línea TOON inválida en índice {$i}.");
            }

            $pointer = $parts[0];
            $type = $parts[1];
            $raw = $parts[2] ?? null;
            $entries[$pointer] = ['type' => $type, 'raw' => $raw];
        }

        if (! array_key_exists('/', $entries)) {
            throw new InvalidArgumentException('Contenido TOON inválido: no existe nodo raíz.');
        }

        $pointers = array_keys($entries);
        usort($pointers, static function (string $a, string $b): int {
            $depthA = substr_count($a, '/');
            $depthB = substr_count($b, '/');
            return $depthA <=> $depthB ?: strcmp($a, $b);
        });

        $root = null;
        foreach ($pointers as $pointer) {
            $entry = $entries[$pointer];
            $value = $this->decodeValue($entry['type'], $entry['raw']);

            if ($pointer === '/') {
                $root = $value;
                continue;
            }

            if ($root === null) {
                throw new RuntimeException('No se pudo inicializar raíz antes de asignar nodos.');
            }

            $this->setPointerValue($root, $pointer, $value);
        }

        $stats = [
            'nodes' => 0,
            'objects' => 0,
            'arrays' => 0,
            'strings' => 0,
            'numbers' => 0,
            'booleans' => 0,
            'nulls' => 0,
            'numeric_strings' => 0,
        ];
        $this->analyzeNode($root, $stats);

        foreach ($entries as $pointer => $entry) {
            if (! in_array($entry['type'], ['object', 'array'], true)) {
                continue;
            }

            $declaredSize = (int) ($entry['raw'] ?? 0);
            $actualValue = $this->getPointerValue($root, $pointer);
            $actualSize = is_array($actualValue) ? count($actualValue) : 0;
            if ($declaredSize !== $actualSize) {
                throw new RuntimeException("Integridad TOON inválida en {$pointer}: tamaño declarado {$declaredSize}, real {$actualSize}.");
            }
        }

        return [
            'data' => $root,
            'stats' => $stats,
            'line_count' => count($rows),
        ];
    }

    public function normalize(mixed $data): mixed
    {
        if (! is_array($data)) {
            return $data;
        }

        if (array_is_list($data)) {
            return array_map(fn ($item) => $this->normalize($item), $data);
        }

        $normalized = [];
        $keys = array_keys($data);
        sort($keys);
        foreach ($keys as $key) {
            $normalized[$key] = $this->normalize($data[$key]);
        }

        return $normalized;
    }

    private function encodeNode(string $pointer, mixed $value, array &$lines, array &$stats): void
    {
        $stats['nodes']++;

        if (is_array($value)) {
            if (array_is_list($value)) {
                $stats['arrays']++;
                $lines[] = "{$pointer}\tarray\t" . count($value);
                foreach ($value as $index => $item) {
                    $childPointer = $pointer . '/i:' . $index;
                    $this->encodeNode($childPointer, $item, $lines, $stats);
                }
                return;
            }

            $stats['objects']++;
            $lines[] = "{$pointer}\tobject\t" . count($value);
            foreach ($value as $key => $item) {
                $encodedKey = $this->escapeToken((string) $key);
                $childPointer = $pointer . '/k:' . $encodedKey;
                $this->encodeNode($childPointer, $item, $lines, $stats);
            }
            return;
        }

        if (is_string($value)) {
            $stats['strings']++;
            if (preg_match('/^-?\d{16,}$/', $value) === 1) {
                $stats['numeric_strings']++;
            }
            $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
            $lines[] = "{$pointer}\tstring\t{$json}";
            return;
        }

        if (is_int($value) || is_float($value)) {
            if (is_float($value) && (! is_finite($value))) {
                throw new InvalidArgumentException("Valor no compatible en {$pointer}: float no finito.");
            }
            $stats['numbers']++;
            $lines[] = "{$pointer}\tnumber\t" . (string) $value;
            return;
        }

        if (is_bool($value)) {
            $stats['booleans']++;
            $lines[] = "{$pointer}\tboolean\t" . ($value ? 'true' : 'false');
            return;
        }

        if ($value === null) {
            $stats['nulls']++;
            $lines[] = "{$pointer}\tnull";
            return;
        }

        throw new InvalidArgumentException("Tipo de dato no soportado en {$pointer}: " . gettype($value));
    }

    private function decodeValue(string $type, ?string $raw): mixed
    {
        return match ($type) {
            'object' => [],
            'array' => [],
            'string' => json_decode((string) $raw, true, 512, JSON_THROW_ON_ERROR),
            'number' => $this->decodeNumber((string) $raw),
            'boolean' => match ($raw) {
                'true' => true,
                'false' => false,
                default => throw new InvalidArgumentException('Booleano TOON inválido: ' . (string) $raw),
            },
            'null' => null,
            default => throw new InvalidArgumentException('Tipo TOON no reconocido: ' . $type),
        };
    }

    private function decodeNumber(string $raw): int|float|string
    {
        if ($raw === '') {
            throw new InvalidArgumentException('Número TOON vacío.');
        }

        if (preg_match('/^-?\d+$/', $raw) === 1) {
            if (strlen(ltrim($raw, '-')) > 18) {
                return $raw;
            }

            return (int) $raw;
        }

        if (preg_match('/^-?\d+(\.\d+)?([eE][+-]?\d+)?$/', $raw) === 1) {
            return (float) $raw;
        }

        throw new InvalidArgumentException('Número TOON inválido: ' . $raw);
    }

    private function setPointerValue(array &$root, string $pointer, mixed $value): void
    {
        $segments = $this->decodePointerSegments($pointer);
        $last = array_pop($segments);
        if ($last === null) {
            throw new InvalidArgumentException("Pointer inválido: {$pointer}");
        }

        $current = &$root;
        foreach ($segments as $segment) {
            if ($segment['kind'] === 'i') {
                $index = $segment['value'];
                if (! array_key_exists($index, $current) || ! is_array($current[$index])) {
                    $current[$index] = [];
                }
                $current = &$current[$index];
                continue;
            }

            $key = $segment['value'];
            if (! array_key_exists($key, $current) || ! is_array($current[$key])) {
                $current[$key] = [];
            }
            $current = &$current[$key];
        }

        if ($last['kind'] === 'i') {
            $current[$last['value']] = $value;
            return;
        }

        $current[$last['value']] = $value;
    }

    private function getPointerValue(mixed $root, string $pointer): mixed
    {
        if ($pointer === '/') {
            return $root;
        }

        $segments = $this->decodePointerSegments($pointer);
        $current = $root;
        foreach ($segments as $segment) {
            if (! is_array($current)) {
                throw new RuntimeException("Ruta TOON inválida: {$pointer}");
            }

            $key = $segment['value'];
            if (! array_key_exists($key, $current)) {
                throw new RuntimeException("Nodo TOON faltante en ruta {$pointer}");
            }
            $current = $current[$key];
        }

        return $current;
    }

    private function decodePointerSegments(string $pointer): array
    {
        $trimmed = ltrim($pointer, '/');
        if ($trimmed === '') {
            return [];
        }

        $tokens = explode('/', $trimmed);
        $segments = [];
        foreach ($tokens as $token) {
            if (str_starts_with($token, 'k:')) {
                $segments[] = ['kind' => 'k', 'value' => $this->unescapeToken(substr($token, 2))];
                continue;
            }

            if (str_starts_with($token, 'i:')) {
                $rawIndex = substr($token, 2);
                if (! ctype_digit($rawIndex)) {
                    throw new InvalidArgumentException("Índice TOON inválido: {$token}");
                }
                $segments[] = ['kind' => 'i', 'value' => (int) $rawIndex];
                continue;
            }

            throw new InvalidArgumentException("Segmento TOON inválido: {$token}");
        }

        return $segments;
    }

    private function escapeToken(string $token): string
    {
        return str_replace(['~', '/'], ['~0', '~1'], $token);
    }

    private function unescapeToken(string $token): string
    {
        return str_replace(['~1', '~0'], ['/', '~'], $token);
    }

    private function analyzeNode(mixed $value, array &$stats): void
    {
        $stats['nodes']++;

        if (is_array($value)) {
            if (array_is_list($value)) {
                $stats['arrays']++;
                foreach ($value as $item) {
                    $this->analyzeNode($item, $stats);
                }
                return;
            }

            $stats['objects']++;
            foreach ($value as $item) {
                $this->analyzeNode($item, $stats);
            }
            return;
        }

        if (is_string($value)) {
            $stats['strings']++;
            if (preg_match('/^-?\d{16,}$/', $value) === 1) {
                $stats['numeric_strings']++;
            }
            return;
        }

        if (is_int($value) || is_float($value)) {
            $stats['numbers']++;
            return;
        }

        if (is_bool($value)) {
            $stats['booleans']++;
            return;
        }

        if ($value === null) {
            $stats['nulls']++;
        }
    }
}
