<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceDocument;
use App\Models\ServiceRequirement;
use App\Services\HourCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:services.view')->only(['index', 'show']);
        $this->middleware('permission:services.create')->only(['create', 'store']);
        $this->middleware('permission:services.edit')->only(['edit', 'update']);
        $this->middleware('permission:services.deactivate')->only(['toggleStatus']);
    }

    // ── Constants ──────────────────────────────────────────────────

    public const FUNCTIONAL_AREAS = [
        'RRHH', 'Finanzas', 'Logística', 'Compras', 'Operaciones',
        'TI', 'Legal', 'Comercial', 'Calidad', 'Producción',
    ];

    public const CLIENT_TYPES = [
        'micro'        => 'Microempresa',
        'pequeña'      => 'Pequeña empresa',
        'mediana'      => 'Mediana empresa',
        'gran_empresa' => 'Gran empresa',
    ];

    public const DOCUMENT_TYPES = [
        'manual'       => 'Manual',
        'diagnostico'  => 'Diagnóstico',
        'plan_accion'  => 'Plan de acción',
        'informe'      => 'Informe',
        'otro'         => 'Otro',
    ];

    // ─── Index ─────────────────────────────────────────────────────

    public function index(Request $request): View|JsonResponse
    {
        $query = Service::with(['documents', 'requirements', 'createdBy'])
            ->withCount(['documents', 'requirements']);

        if ($request->filled('area')) {
            $query->whereJsonContains('functional_areas', $request->area);
        }

        if ($request->filled('type')) {
            $query->whereJsonContains('client_types', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $services = $query->latest()->paginate(12);

        if ($request->expectsJson()) {
            return response()->json(['services' => $services]);
        }

        return view('admin.services.index', [
            'services'       => $services,
            'functionalAreas' => self::FUNCTIONAL_AREAS,
            'clientTypes'    => self::CLIENT_TYPES,
            'documentTypes'  => self::DOCUMENT_TYPES,
        ]);
    }

    // ─── Show ──────────────────────────────────────────────────────

    public function show(Service $service, Request $request): View|JsonResponse
    {
        $service->load(['documents', 'requirements', 'auditLogs.changedBy']);

        if ($request->expectsJson()) {
            return response()->json(['service' => $service]);
        }

        return view('admin.services.show', [
            'service'        => $service,
            'functionalAreas' => self::FUNCTIONAL_AREAS,
            'clientTypes'    => self::CLIENT_TYPES,
            'documentTypes'  => self::DOCUMENT_TYPES,
        ]);
    }

    // ─── Store ─────────────────────────────────────────────────────

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'short_name'                   => 'required|string|max:120',
            'description'                  => 'required|string|max:5000',
            'functional_areas'             => 'nullable|array',
            'functional_areas.*'           => 'string|max:80',
            'client_types'                 => 'nullable|array',
            'client_types.*'               => 'string|in:micro,pequeña,mediana,gran_empresa',
            'documents'                    => 'nullable|array|max:20',
            'documents.*.name'             => 'required_with:documents|string|max:255',
            'documents.*.type'             => 'required_with:documents|in:manual,diagnostico,plan_accion,informe,otro',
            'documents.*.description'      => 'nullable|string|max:500',
            'requirements'                 => 'nullable|array|max:30',
            'requirements.*.description'   => 'required_with:requirements|string|max:500',
        ]);

        $service = Service::create([
            'short_name'       => $validated['short_name'],
            'description'      => $validated['description'],
            'functional_areas' => $validated['functional_areas'] ?? [],
            'client_types'     => $validated['client_types'] ?? [],
            'status'           => 'active',
            'version'          => 1,
            'created_by'       => $request->user()->id,
            'updated_by'       => $request->user()->id,
        ]);

        $this->syncDocuments($service, $validated['documents'] ?? []);
        $this->syncRequirements($service, $validated['requirements'] ?? []);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Servicio creado exitosamente.',
                'service' => $service->load(['documents', 'requirements']),
            ], 201);
        }

        return redirect()->route('services.index')->with('success', 'Servicio creado exitosamente.');
    }

    // ─── Update ────────────────────────────────────────────────────

    public function update(Request $request, Service $service): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'short_name'                   => 'required|string|max:120',
            'description'                  => 'required|string|max:5000',
            'functional_areas'             => 'nullable|array',
            'functional_areas.*'           => 'string|max:80',
            'client_types'                 => 'nullable|array',
            'client_types.*'               => 'string|in:micro,pequeña,mediana,gran_empresa',
            'documents'                    => 'nullable|array|max:20',
            'documents.*.name'             => 'required_with:documents|string|max:255',
            'documents.*.type'             => 'required_with:documents|in:manual,diagnostico,plan_accion,informe,otro',
            'documents.*.description'      => 'nullable|string|max:500',
            'requirements'                 => 'nullable|array|max:30',
            'requirements.*.description'   => 'required_with:requirements|string|max:500',
        ]);

        $service->update([
            'short_name'       => $validated['short_name'],
            'description'      => $validated['description'],
            'functional_areas' => $validated['functional_areas'] ?? [],
            'client_types'     => $validated['client_types'] ?? [],
            'version'          => $service->version + 1,
            'updated_by'       => $request->user()->id,
        ]);

        $this->syncDocuments($service, $validated['documents'] ?? []);
        $this->syncRequirements($service, $validated['requirements'] ?? []);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Servicio actualizado exitosamente.',
                'service' => $service->fresh()->load(['documents', 'requirements']),
            ]);
        }

        return redirect()->route('services.index')->with('success', 'Servicio actualizado exitosamente.');
    }

    // ─── Toggle status ─────────────────────────────────────────────

    public function calculate(Request $request, Service $service): JsonResponse
    {
        $validated = $request->validate([
            'client_size'    => ['required', 'string'],
            'hourly_rate'    => ['nullable', 'numeric', 'min:0'],
            'margin_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'persons'        => ['nullable', 'integer', 'min:1'],
        ]);

        $calculator = new HourCalculatorService();

        $result = $calculator->calculate(
            $service,
            $validated['client_size'],
            (float) ($validated['hourly_rate']    ?? 25.0),
            (float) ($validated['margin_percent'] ?? 20.0),
            isset($validated['persons']) ? (int) $validated['persons'] : null
        );

        return response()->json($result);
    }

    public function toggleStatus(Request $request, Service $service): JsonResponse
    {
        $service->update([
            'status'     => $service->status === 'active' ? 'inactive' : 'active',
            'updated_by' => $request->user()->id,
        ]);

        $label = $service->status === 'active' ? 'activado' : 'desactivado';

        return response()->json([
            'message' => "Servicio {$label} exitosamente.",
            'status'  => $service->status,
        ]);
    }

    // ─── Private helpers ───────────────────────────────────────────

    private function syncDocuments(Service $service, array $docs): void
    {
        $service->documents()->delete();

        foreach ($docs as $i => $doc) {
            if (empty($doc['name'])) {
                continue;
            }
            ServiceDocument::create([
                'service_id'  => $service->id,
                'name'        => $doc['name'],
                'type'        => $doc['type'] ?? 'otro',
                'description' => $doc['description'] ?? null,
                'order'       => $i,
            ]);
        }
    }

    private function syncRequirements(Service $service, array $reqs): void
    {
        $service->requirements()->delete();

        foreach ($reqs as $i => $req) {
            if (empty($req['description'])) {
                continue;
            }
            ServiceRequirement::create([
                'service_id'  => $service->id,
                'description' => $req['description'],
                'order'       => $i,
            ]);
        }
    }
}
