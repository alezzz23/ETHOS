<?php

namespace App\Console\Commands;

use App\Support\Toon\ToonCodec;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use RuntimeException;

class ConvertJsonToToonCommand extends Command
{
    protected $signature = 'format:json-to-toon
                            {input=storage/api-docs/api-docs.json : Archivo JSON de entrada}
                            {--output=storage/toon/api-docs.toon : Archivo TOON de salida}
                            {--report=storage/toon/api-docs.report.json : Reporte JSON de migración}
                            {--validate : Valida roundtrip TOON->JSON}
                            {--strict : Falla si detecta incompatibilidades representacionales}';

    protected $description = 'Convierte JSON a TOON preservando estructura, valida integridad y genera reporte de migración.';

    public function handle(ToonCodec $codec): int
    {
        $inputPath = base_path((string) $this->argument('input'));
        $outputPath = base_path((string) $this->option('output'));
        $reportPath = base_path((string) $this->option('report'));
        $validate = (bool) $this->option('validate');
        $strict = (bool) $this->option('strict');

        if (! File::exists($inputPath)) {
            $this->error("No existe el archivo JSON de entrada: {$inputPath}");
            return self::FAILURE;
        }

        $rawJson = File::get($inputPath);
        $inputBytes = strlen($rawJson);
        $decoded = json_decode($rawJson, true, 512, JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);

        $conversion = $codec->encode($decoded);
        File::ensureDirectoryExists(dirname($outputPath));
        File::put($outputPath, $conversion['content']);
        $outputBytes = File::size($outputPath);

        $report = [
            'format' => 'TOON/1',
            'generated_at' => Carbon::now()->toIso8601String(),
            'source' => [
                'path' => $this->relativePath($inputPath),
                'bytes' => $inputBytes,
                'hash_sha256' => hash('sha256', $rawJson),
            ],
            'target' => [
                'path' => $this->relativePath($outputPath),
                'bytes' => $outputBytes,
                'hash_sha256' => hash_file('sha256', $outputPath),
            ],
            'stats' => [
                'toon_line_count' => $conversion['line_count'],
                'source_model' => $this->analyzeJsonModel($decoded),
                'toon_model' => $conversion['stats'],
            ],
            'incompatibilities' => [
                'numeric_strings_detected' => $conversion['stats']['numeric_strings'],
                'notes' => [
                    'TOON conserva números muy grandes como string para evitar pérdida de precisión.',
                ],
            ],
            'validation' => [
                'enabled' => $validate,
                'passed' => null,
                'differences' => [],
            ],
        ];

        if ($validate) {
            $decodedToon = $codec->decode($conversion['content']);
            $sourceNormalized = $codec->normalize($decoded);
            $targetNormalized = $codec->normalize($decodedToon['data']);
            $same = $sourceNormalized === $targetNormalized;
            $report['validation']['passed'] = $same;
            $report['validation']['toon_model'] = $decodedToon['stats'];

            if (! $same) {
                $report['validation']['differences'][] = 'La normalización estructural JSON y TOON no coincide.';
                if ($strict) {
                    $this->writeReport($reportPath, $report);
                    throw new RuntimeException('Validación fallida en modo strict: JSON y TOON no son equivalentes.');
                }
            }
        }

        $this->writeReport($reportPath, $report);

        if ($strict && Arr::get($report, 'incompatibilities.numeric_strings_detected', 0) > 0) {
            $this->warn('Se detectaron numeric strings durante la migración y strict está activo.');
            return self::FAILURE;
        }

        $this->info('Conversión JSON -> TOON completada.');
        $this->line('Entrada: ' . $this->relativePath($inputPath));
        $this->line('Salida TOON: ' . $this->relativePath($outputPath));
        $this->line('Reporte: ' . $this->relativePath($reportPath));
        $this->line('Nodos convertidos: ' . $conversion['stats']['nodes']);
        if ($validate) {
            $this->line('Validación roundtrip: ' . ($report['validation']['passed'] ? 'OK' : 'FALLÓ'));
        }

        return self::SUCCESS;
    }

    private function writeReport(string $path, array $report): void
    {
        File::ensureDirectoryExists(dirname($path));
        File::put(
            $path,
            json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)
        );
    }

    private function relativePath(string $absolutePath): string
    {
        return ltrim(str_replace(base_path(), '', $absolutePath), DIRECTORY_SEPARATOR);
    }

    private function analyzeJsonModel(mixed $value): array
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

        $walker = function (mixed $node) use (&$walker, &$stats): void {
            $stats['nodes']++;

            if (is_array($node)) {
                if (array_is_list($node)) {
                    $stats['arrays']++;
                } else {
                    $stats['objects']++;
                }
                foreach ($node as $item) {
                    $walker($item);
                }
                return;
            }

            if (is_string($node)) {
                $stats['strings']++;
                if (preg_match('/^-?\d{16,}$/', $node) === 1) {
                    $stats['numeric_strings']++;
                }
                return;
            }

            if (is_int($node) || is_float($node)) {
                $stats['numbers']++;
                return;
            }

            if (is_bool($node)) {
                $stats['booleans']++;
                return;
            }

            if ($node === null) {
                $stats['nulls']++;
            }
        };

        $walker($value);

        return $stats;
    }
}
