<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChecklistItem;
use App\Models\Client;
use App\Models\ClientSizeConfig;
use App\Models\Project;
use App\Models\ProjectProgressEntry;
use App\Models\Service;
use App\Models\Task;
use App\Models\User;
use App\Services\ChecklistGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:projects.view')->only(['index', 'show']);
        $this->middleware('permission:projects.create')->only(['create', 'store']);
        $this->middleware('permission:projects.edit')->only([
            'edit', 'update', 'analyze', 'approve', 'startExecution', 'close', 'logProgress',
        ]);
        $this->middleware('permission:projects.delete')->only(['destroy']);
    }

    // ─── FASE 1: Captura rápida ──────────────────────────────────

    public function index(): View
    {
        $projects = Project::with(['client', 'service'])->latest()->paginate(12);
        $clients  = Client::orderBy('name')->get();
        $services = Service::where('status', 'active')->orderBy('short_name')->get();
        $users    = User::orderBy('name')->get();

        return view('admin.projects.index', compact('projects', 'clients', 'services', 'users'));
    }

    public function create(): View
    {
        $clients  = Client::orderBy('name')->get();
        $services = Service::where('status', 'active')->orderBy('short_name')->get();

        return view('admin.projects.create', compact('clients', 'services'));
    }

    /**
     * Fase 1 – store: solo los campos mínimos, siempre capturado.
     * Observer se encarga de bloquear campos y notificar consultores.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'client_id'        => 'required|exists:clients,id',
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'type'             => 'nullable|string|max:100',
            'subtype'          => 'nullable|string|max:100',
            'urgency'          => 'nullable|in:baja,media,alta',
            'complexity'       => 'nullable|in:baja,media,alta',
            'starts_at'        => 'nullable|date',
            'estimated_budget' => 'nullable|numeric|min:0',
            'currency'         => 'nullable|string|size:3',
        ]);

        $validated['status']      = Project::STATUS_CAPTURADO;
        $validated['captured_by'] = auth()->id();

        $project = Project::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Proyecto capturado exitosamente. Se notificó a las consultoras.',
                'project' => $this->projectPayload($project->fresh(['client', 'service', 'capturedBy'])),
            ]);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Proyecto capturado exitosamente.');
    }

    public function show(Project $project, Request $request): JsonResponse|View
    {
        $project->load(['client', 'service', 'capturedBy', 'assignedTo', 'validatedBy', 'leader',
                        'tasks', 'proposals', 'checklists.items', 'progressEntries.recordedBy']);

        if ($request->expectsJson()) {
            return response()->json(['project' => $this->projectPayload($project)]);
        }

        $users    = User::orderBy('name')->get();
        $services = Service::where('status', 'active')->orderBy('short_name')->get();
        $sizes    = ClientSizeConfig::orderBy('min_employees')->get();
        $consultors = User::whereHas('roles', fn ($q) => $q->where('name', 'consultor'))
                        ->orderBy('name')->get();
        $leaders    = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['lider_proyecto', 'consultor']))
                        ->orderBy('name')->get();

        return view('admin.projects.show', compact(
            'project', 'users', 'services', 'sizes', 'consultors', 'leaders'
        ));
    }

    public function edit(Project $project): View
    {
        $clients  = Client::orderBy('name')->get();
        $services = Service::where('status', 'active')->orderBy('short_name')->get();

        return view('admin.projects.edit', compact('project', 'clients', 'services'));
    }

    /**
     * General update — enforces field locking for non-privileged users.
     */
    public function update(Request $request, Project $project): JsonResponse|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $isLocked   = $project->is_locked;
        $canUnlock  = $project->userCanEditLockedFields($user);

        $rules = [
            'client_id'   => 'required|exists:clients,id',
            'title'       => $isLocked && !$canUnlock ? 'prohibited' : 'required|string|max:255',
            'description' => $isLocked && !$canUnlock ? 'prohibited' : 'nullable|string',
            'type'        => $isLocked && !$canUnlock ? 'prohibited' : 'nullable|string|max:100',
            'subtype'     => $isLocked && !$canUnlock ? 'prohibited' : 'nullable|string|max:100',
            'urgency'     => $isLocked && !$canUnlock ? 'prohibited' : 'nullable|in:baja,media,alta',
            'complexity'  => $isLocked && !$canUnlock ? 'prohibited' : 'nullable|in:baja,media,alta',
            'starts_at'   => $isLocked && !$canUnlock ? 'prohibited' : 'nullable|date',
            'estimated_budget' => $isLocked && !$canUnlock ? 'prohibited' : 'nullable|numeric|min:0',
            // Always-editable
            'currency'      => 'nullable|string|size:3',
            'final_budget'  => 'nullable|numeric|min:0',
            'ends_at'       => 'nullable|date',
            'finished_at'   => 'nullable|date',
            'assigned_to'   => 'nullable|exists:users,id',
            'leader_id'     => 'nullable|exists:users,id',
            'validated_by'  => 'nullable|exists:users,id',
            'progress'      => 'nullable|integer|min:0|max:100',
            'priority_score' => 'nullable|numeric|min:1|max:10',
            'priority_level' => 'nullable|in:baja,media,alta',
        ];

        $validated = $request->validate($rules);

        // If locked fields are being set and user can unlock, keep them
        if ($isLocked && !$canUnlock) {
            foreach (Project::LOCKED_FIELDS as $field) {
                unset($validated[$field]);
            }
        }

        $project->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Proyecto actualizado.',
                'project' => $this->projectPayload($project->fresh(['client', 'service', 'capturedBy'])),
            ]);
        }

        return redirect()->route('projects.index')->with('success', 'Proyecto actualizado.');
    }

    public function destroy(Project $project): JsonResponse|RedirectResponse
    {
        $project->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Proyecto eliminado.']);
        }

        return redirect()->route('projects.index')->with('success', 'Proyecto eliminado.');
    }

    // ─── FASE 2: Análisis de consultora ───────────────────────────

    /**
     * POST /admin/projects/{project}/analyze
     * Consultora define servicio, horas, tarifa y líder; mueve a en_analisis.
     */
    public function analyze(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'service_id'      => 'required|exists:services,id',
            'leader_id'       => 'required|exists:users,id',
            'estimated_hours' => 'required|numeric|min:1',
            'hourly_rate'     => 'required|numeric|min:0',
            'assigned_to'     => 'nullable|exists:users,id',
        ]);

        $validated['status'] = Project::STATUS_EN_ANALISIS;

        $project->update($validated);

        return response()->json([
            'message' => 'Proyecto en análisis. Consultora y líder asignados.',
            'project' => $this->projectPayload($project->fresh(['client', 'service', 'leader'])),
        ]);
    }

    // ─── FASE 3: Aprobación ───────────────────────────────────────

    /**
     * POST /admin/projects/{project}/approve
     * Aprueba el proyecto; observer notifica al líder y recalcula prioridad.
     */
    public function approve(Request $request, Project $project): JsonResponse
    {
        $request->validate([
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        if (in_array($project->status, [Project::STATUS_APROBADO, Project::STATUS_EN_EJECUCION, Project::STATUS_CERRADO])) {
            return response()->json(['message' => 'El proyecto ya está aprobado o más avanzado.'], 422);
        }

        $project->update([
            'status'  => Project::STATUS_APROBADO,
            'ends_at' => $request->ends_at,
        ]);

        return response()->json([
            'message' => 'Proyecto aprobado. Líder notificado y prioridad recalculada.',
            'project' => $this->projectPayload($project->fresh(['client', 'service', 'leader'])),
        ]);
    }

    /**
     * POST /admin/projects/{project}/start-execution
     * Mueve a en_ejecucion; observer registra timestamp.
     */
    public function startExecution(Request $request, Project $project): JsonResponse
    {
        if ($project->status !== Project::STATUS_APROBADO) {
            return response()->json(['message' => 'El proyecto debe estar aprobado para iniciar ejecución.'], 422);
        }

        $project->update(['status' => Project::STATUS_EN_EJECUCION]);

        return response()->json([
            'message' => 'Ejecución iniciada.',
            'project' => $this->projectPayload($project->fresh()),
        ]);
    }

    // ─── FASE 4: Ejecución ────────────────────────────────────────

    /**
     * POST /admin/projects/{project}/progress
     * El líder registra avance por método de investigación.
     * Los totales de horas reales y desvío se recalculan automáticamente.
     */
    public function logProgress(Request $request, Project $project): JsonResponse
    {
        if ($project->status !== Project::STATUS_EN_EJECUCION) {
            return response()->json(['message' => 'El proyecto no está en ejecución.'], 422);
        }

        $validated = $request->validate([
            'method'            => 'required|in:encuesta,entrevista,observacion,documental',
            'phase'             => 'required|in:levantamiento,diagnostico,propuesta,implementacion,seguimiento',
            'planned_hours'     => 'nullable|numeric|min:0',
            'actual_hours'      => 'required|numeric|min:0',
            'progress_pct'      => 'required|integer|min:0|max:100',
            'weight'            => 'nullable|numeric|min:0.1|max:10',
            'notes'             => 'nullable|string|max:1000',
            'date_worked'       => 'required|date|before_or_equal:today',
            'checklist_item_id' => 'nullable|exists:checklist_items,id',
        ]);

        $validated['project_id']  = $project->id;
        $validated['recorded_by'] = auth()->id();
        $validated['weight']      = $validated['weight'] ?? 1.0;

        ProjectProgressEntry::create($validated);

        // If linked to a checklist item and progress is 100%, auto-complete it
        if (!empty($validated['checklist_item_id']) && (int) $validated['progress_pct'] === 100) {
            $checklistItem = ChecklistItem::find($validated['checklist_item_id']);
            if ($checklistItem && !$checklistItem->is_completed) {
                $checklistItem->update(['is_completed' => true, 'completed_at' => now()]);

                // Check if all items done → mark checklist complete
                $checklist = $checklistItem->checklist;
                $allDone   = $checklist->items()->where('is_completed', false)->doesntExist();
                if ($allDone) {
                    $checklist->update(['status' => 'completed', 'completed_at' => now()]);
                }
            }
        }

        // Update project actual_hours = sum of all entries
        $totalActual = $project->progressEntries()->sum('actual_hours');
        $project->actual_hours = $totalActual;
        $project->save(); // triggers observer checkDeviation

        // Recalculate overall progress from weighted entries
        $project->recalculateProgress();

        return response()->json([
            'message'          => 'Avance registrado.',
            'actual_hours'     => $project->fresh()->actual_hours,
            'deviation_percent'=> $project->fresh()->deviation_percent,
            'progress'         => $project->fresh()->progress,
        ]);
    }

    /**
     * POST /admin/projects/{project}/close
     * Cierra el proyecto, calcula costo final real.
     */
    public function close(Request $request, Project $project): JsonResponse
    {
        if ($project->status === Project::STATUS_CERRADO) {
            return response()->json(['message' => 'El proyecto ya está cerrado.'], 422);
        }

        $project->update([
            'status'        => Project::STATUS_CERRADO,
            'finished_at'   => now()->toDateString(),
            'final_budget'  => ($project->actual_hours ?? 0) * ($project->hourly_rate ?? 0),
        ]);

        return response()->json([
            'message'      => 'Proyecto cerrado. Encuesta de satisfacción enviada al cliente.',
            'final_budget' => $project->fresh()->final_budget,
        ]);
    }

    // ─── Reporte de Progreso ─────────────────────────────────────

    /**
     * GET /admin/projects/{project}/report
     * Reporte imprimible del proyecto con desglose de avances.
     */
    public function progressReport(Project $project): View
    {
        $project->load([
            'client', 'service', 'capturedBy', 'leader', 'assignedTo',
            'proposals' => fn ($q) => $q->where('status', 'approved')->with('service'),
            'checklists.items',
            'progressEntries.recordedBy',
        ]);

        $entriesByPhase = $project->progressEntries
            ->groupBy('phase')
            ->map(fn ($group) => [
                'total_hours'   => $group->sum('actual_hours'),
                'avg_progress'  => round($group->avg('progress_pct'), 1),
                'entries'       => $group->sortBy('date_worked')->values(),
            ]);

        return view('admin.projects.report', compact('project', 'entriesByPhase'));
    }

    // ─── Payload helper ──────────────────────────────────────────

    private function projectPayload(Project $project): array
    {
        return [
            'id'          => $project->id,
            'client_id'   => $project->client_id,
            'client_name' => $project->client?->name,
            'client_name_label' => $project->client?->name ?: 'Sin cliente',
            'service_id'  => $project->service_id,
            'service_name'=> $project->service?->short_name,
            'title'       => $project->title,
            'description' => $project->description,
            'status'      => $project->status,
            'status_label'=> $project->status_label,
            'status_color'=> $project->status_color,
            'is_locked'   => $project->is_locked,

            // Clasificación
            'type'             => $project->type,
            'subtype'          => $project->subtype,
            'complexity'       => $project->complexity,
            'complexity_label' => $project->complexity_label,
            'urgency'          => $project->urgency,
            'urgency_label'    => $project->urgency_label,

            // Presupuesto
            'estimated_budget'       => $project->estimated_budget,
            'estimated_budget_label' => $project->estimated_budget_label,
            'final_budget'           => $project->final_budget,
            'final_budget_label'     => $project->final_budget_label,
            'budget_difference_label'=> $project->budget_difference_label,
            'currency'               => $project->currency,

            // Horas
            'estimated_hours'   => $project->estimated_hours,
            'hourly_rate'       => $project->hourly_rate,
            'actual_hours'      => $project->actual_hours,
            'deviation_percent' => $project->deviation_percent,
            'deviation_label'   => $project->deviation_label,

            // Prioridad
            'priority_score' => $project->priority_score,
            'priority_label' => $project->priority_label,
            'priority_level' => $project->priority_level,

            // Progreso
            'progress'         => $project->progress,
            'progress_percent' => $project->progress_percent,

            // Responsables
            'captured_by_id'          => $project->captured_by,
            'captured_by_name_label'  => $project->capturedBy?->name ?: 'No asignado',
            'assigned_to_id'          => $project->assigned_to,
            'assigned_to_name_label'  => $project->assignedTo?->name ?: 'No asignado',
            'leader_id'               => $project->leader_id,
            'leader_name'             => $project->leader?->name ?: 'Sin líder',
            'validated_by_id'         => $project->validated_by,
            'validated_by_name_label' => $project->validatedBy?->name ?: 'No validado',

            // Fechas
            'starts_at'       => $project->starts_at?->format('Y-m-d'),
            'starts_at_label' => $project->starts_at ? $project->starts_at->format('d/m/Y') : 'Sin fecha',
            'ends_at'         => $project->ends_at?->format('Y-m-d'),
            'ends_at_label'   => $project->ends_at ? $project->ends_at->format('d/m/Y') : 'Sin fecha',
            'finished_at'     => $project->finished_at?->format('Y-m-d'),
            'finished_at_label' => $project->finished_at ? $project->finished_at->format('d/m/Y') : 'Sin fecha',
            'approved_at'     => $project->approved_at?->format('d/m/Y H:i'),
            'created_at'      => $project->created_at?->format('d/m/Y H:i'),
            'updated_at'      => $project->updated_at?->format('d/m/Y H:i'),
        ];
    }
}
