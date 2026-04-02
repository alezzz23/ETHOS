<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceDocument;
use App\Models\ServiceRequirement;
use App\Models\User;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::whereHas('roles', fn ($q) => $q->where('name', 'super_admin'))->first();

        $services = [
            [
                'short_name'       => 'Auditoría Fiscal',
                'description'      => 'Revisión exhaustiva de los registros contables y tributarios de la empresa. Identifica riesgos fiscales, errores en declaraciones y oportunidades de optimización impositiva. Incluye análisis de cumplimiento con la legislación vigente y recomendaciones para reducir contingencias.',
                'functional_areas' => ['Finanzas', 'Legal'],
                'client_types'     => ['mediana', 'gran_empresa'],
                'documents' => [
                    ['name' => 'Informe de Auditoría Fiscal', 'type' => 'informe', 'description' => 'Resultados y hallazgos de la auditoría'],
                    ['name' => 'Plan de Corrección Tributaria', 'type' => 'plan_accion', 'description' => 'Pasos para corregir desviaciones identificadas'],
                ],
                'requirements' => [
                    'Acceso a declaraciones de IVA e ISLR de los últimos 3 años',
                    'Estados financieros auditados o internos',
                    'Registro de libros contables digitales o físicos',
                    'Contacto con el contador o responsable tributario',
                ],
            ],
            [
                'short_name'       => 'Diseño de Procesos Corporativos',
                'description'      => 'Levantamiento, análisis y rediseño de los procesos operativos de la empresa. Identificamos cuellos de botella, redundancias y oportunidades de automatización. Entregamos mapas de proceso (BPMN), indicadores de gestión y un plan de implementación priorizado.',
                'functional_areas' => ['Operaciones', 'RRHH', 'Calidad'],
                'client_types'     => ['pequeña', 'mediana', 'gran_empresa'],
                'documents' => [
                    ['name' => 'Mapa de Procesos (BPMN)', 'type' => 'diagnostico', 'description' => 'Diagrama de flujo detallado por área'],
                    ['name' => 'Manual de Procedimientos', 'type' => 'manual', 'description' => 'Descripción paso a paso de cada proceso'],
                    ['name' => 'Plan de Mejora', 'type' => 'plan_accion', 'description' => 'Hoja de ruta de implementación'],
                ],
                'requirements' => [
                    'Disponibilidad de al menos un responsable por área a intervenir',
                    'Acceso a flujos de trabajo actuales (digitales o documentados)',
                    'Autorización de la gerencia para realizar observaciones en planta/oficinas',
                    'Listado de indicadores o métricas que actualmente se gestionan',
                ],
            ],
            [
                'short_name'       => 'Manuales Operativos y de Funciones',
                'description'      => 'Elaboración de manuales de funciones por cargo y manuales de procedimientos operativos. Documenta las responsabilidades, líneas de reporte, procedimientos estándar y políticas internas. Facilita la inducción, la delegación efectiva y el control de calidad.',
                'functional_areas' => ['RRHH', 'Operaciones'],
                'client_types'     => ['micro', 'pequeña', 'mediana'],
                'documents' => [
                    ['name' => 'Manual de Funciones por Cargo', 'type' => 'manual', 'description' => 'Descripción detallada de cada puesto'],
                    ['name' => 'Manual de Procedimientos Operativos', 'type' => 'manual', 'description' => 'Guías paso a paso para operaciones clave'],
                ],
                'requirements' => [
                    'Organigrama actualizado de la empresa',
                    'Listado de cargos y sus responsables',
                    'Acceso a los responsables para entrevistas de levantamiento',
                    'Políticas internas existentes (si las hay)',
                ],
            ],
            [
                'short_name'       => 'Estructuración Organizacional',
                'description'      => 'Diagnóstico y rediseño de la estructura organizacional para alinearse con la estrategia del negocio. Analiza la distribución de roles, el tramo de control, la cadena de mando y los niveles jerárquicos. Propone organigramas optimizados y planes de transición.',
                'functional_areas' => ['RRHH', 'Comercial', 'Operaciones'],
                'client_types'     => ['pequeña', 'mediana', 'gran_empresa'],
                'documents' => [
                    ['name' => 'Diagnóstico Organizacional', 'type' => 'diagnostico', 'description' => 'Análisis del estado actual de la estructura'],
                    ['name' => 'Propuesta de Reorganización', 'type' => 'plan_accion', 'description' => 'Nuevo organigrama y plan de transición'],
                ],
                'requirements' => [
                    'Organigrama actual (formal e informal)',
                    'Descripciones de cargo existentes',
                    'Plan estratégico o declaración de visión/misión',
                    'Acceso a entrevistas con directivos y mandos medios',
                ],
            ],
            [
                'short_name'       => 'Sistema de Control Contable',
                'description'      => 'Implementación o mejora de sistemas de control interno contable. Diseñamos políticas de cierre, conciliaciones, flujos de aprobación y controles de acceso a la información financiera. Reduce el riesgo de fraude, errores y pérdida de información clave.',
                'functional_areas' => ['Finanzas'],
                'client_types'     => ['micro', 'pequeña', 'mediana'],
                'documents' => [
                    ['name' => 'Diagnóstico de Control Interno', 'type' => 'diagnostico', 'description' => 'Evaluación de riesgos y controles existentes'],
                    ['name' => 'Manual de Control Contable', 'type' => 'manual', 'description' => 'Políticas y procedimientos de control'],
                    ['name' => 'Plan de Implementación', 'type' => 'plan_accion', 'description' => 'Cronograma de mejoras a implementar'],
                ],
                'requirements' => [
                    'Acceso al sistema contable utilizado',
                    'Estados financieros de los últimos 12 meses',
                    'Presencia del contador o responsable financiero',
                    'Políticas de firma o aprobación vigentes',
                ],
            ],
            [
                'short_name'       => 'Protocolos Internos Empresariales',
                'description'      => 'Diseño y documentación de protocolos internos: atención al cliente, manejo de quejas, seguridad de la información, onboarding de empleados, protocolo de crisis y otros. Estandariza comportamientos clave y reduce la dependencia de personas específicas.',
                'functional_areas' => ['RRHH', 'Comercial', 'Legal', 'Operaciones'],
                'client_types'     => ['micro', 'pequeña', 'mediana', 'gran_empresa'],
                'documents' => [
                    ['name' => 'Protocolos Documentados', 'type' => 'manual', 'description' => 'Conjunto de protocolos diseñados'],
                    ['name' => 'Guía de Implementación', 'type' => 'plan_accion', 'description' => 'Cómo poner en marcha los protocolos'],
                ],
                'requirements' => [
                    'Identificar las 3-5 situaciones críticas a protocolizar',
                    'Acceso a los responsables de cada área involucrada',
                    'Información sobre incidentes previos o problemáticas recurrentes',
                ],
            ],
        ];

        foreach ($services as $data) {
            $docs  = $data['documents'] ?? [];
            $reqs  = $data['requirements'] ?? [];

            unset($data['documents'], $data['requirements']);

            $service = Service::firstOrCreate(
                ['short_name' => $data['short_name']],
                array_merge($data, [
                    'status'     => 'active',
                    'version'    => 1,
                    'created_by' => $admin?->id,
                    'updated_by' => $admin?->id,
                ])
            );

            if ($service->wasRecentlyCreated) {
                foreach ($docs as $i => $doc) {
                    ServiceDocument::create([
                        'service_id'  => $service->id,
                        'name'        => $doc['name'],
                        'type'        => $doc['type'],
                        'description' => $doc['description'] ?? null,
                        'order'       => $i,
                    ]);
                }

                foreach ($reqs as $i => $req) {
                    ServiceRequirement::create([
                        'service_id'  => $service->id,
                        'description' => $req,
                        'order'       => $i,
                    ]);
                }
            }
        }
    }
}
