<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:projects.view')->only(['index', 'show']);
        $this->middleware('permission:projects.create')->only(['create', 'store']);
        $this->middleware('permission:projects.edit')->only(['edit', 'update']);
        $this->middleware('permission:projects.delete')->only(['destroy']);
    }

    public function index()
    {
        $projects = Project::with('client')->latest()->paginate(10);
        $clients = Client::all();
        $users = User::all();

        return view('admin.projects.index', compact('projects', 'clients', 'users'));
    }

    public function create()
    {
        $clients = Client::all();

        return view('admin.projects.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $rules = [
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:capturado,clasificacion_pendiente,priorizado,asignacion_lider_pendiente,en_diagnostico,en_diseno,en_implementacion,en_seguimiento,cerrado',
            'type' => 'nullable|string|in:desarrollo_web,infraestructura,consultoria,soporte,mobile,otro',
            'subtype' => 'nullable|string|max:100',
            'complexity' => 'nullable|string|in:baja,media,alta',
            'urgency' => 'nullable|string|in:baja,media,alta',
            'estimated_budget' => 'nullable|numeric|min:0',
            'final_budget' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'priority_score' => 'nullable|numeric|min:1|max:10',
            'priority_level' => 'nullable|string|in:baja,media,alta',
            'assigned_to' => 'nullable|exists:users,id',
            'validated_by' => 'nullable|exists:users,id',
            'progress' => 'nullable|integer|min:0|max:100',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'finished_at' => 'nullable|date',
        ];

        if (auth()->user()->hasRole('consultor')) {
            $rules['assigned_to'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules);

        // Auto-asignar captured_by al usuario autenticado
        $validated['captured_by'] = auth()->id();

        $project = Project::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Proyecto creado exitosamente',
                'project' => $this->mapProject($project->fresh()->load('client')),
            ]);
        }

        return redirect()->route('projects.index')->with('success', 'Proyecto creado exitosamente');
    }

    public function show(Project $project, Request $request)
    {
        if ($request->expectsJson()) {
            $project->load(['client', 'capturedBy', 'assignedTo', 'validatedBy']);

            return response()->json([
                'project' => [
                    'id' => $project->id,
                    'title' => $project->title,
                    'description' => $project->description,
                    'status' => $project->status,
                    'status_label' => ucfirst(str_replace('_', ' ', $project->status)),
                    'client_id' => $project->client_id,
                    'client_name' => $project->client?->name,
                    'client_name_label' => $project->client?->name ?: 'Sin cliente',
                    // Clasificación
                    'type' => $project->type,
                    'type_label' => ucfirst(str_replace('_', ' ', $project->type ?? '')) ?: 'Sin tipo',
                    'subtype' => $project->subtype,
                    'complexity' => $project->complexity,
                    'complexity_label' => $project->complexity_label,
                    'urgency' => $project->urgency,
                    'urgency_label' => $project->urgency_label,
                    // Presupuesto
                    'estimated_budget' => $project->estimated_budget,
                    'estimated_budget_label' => $project->estimated_budget_label,
                    'final_budget' => $project->final_budget,
                    'final_budget_label' => $project->final_budget_label,
                    'budget_difference' => $project->budget_difference,
                    'budget_difference_label' => $project->budget_difference_label,
                    'currency' => $project->currency,
                    // Prioridad
                    'priority_score' => $project->priority_score,
                    'priority_label' => $project->priority_label,
                    'priority_level' => $project->priority_level,
                    // Progreso
                    'progress' => $project->progress,
                    'progress_percent' => $project->progress_percent,
                    // Responsables
                    'captured_by_id' => $project->captured_by,
                    'captured_by_name' => $project->capturedBy?->name,
                    'captured_by_name_label' => $project->capturedBy?->name ?: 'No asignado',
                    'assigned_to_id' => $project->assigned_to,
                    'assigned_to_name' => $project->assignedTo?->name,
                    'assigned_to_name_label' => $project->assignedTo?->name ?: 'No asignado',
                    'validated_by_id' => $project->validated_by,
                    'validated_by_name' => $project->validatedBy?->name,
                    'validated_by_name_label' => $project->validatedBy?->name ?: 'No validado',
                    // Fechas
                    'starts_at' => $project->starts_at,
                    'starts_at_label' => $project->starts_at ? Carbon::parse($project->starts_at)->format('d/m/Y') : 'Sin fecha',
                    'ends_at' => $project->ends_at,
                    'ends_at_label' => $project->ends_at ? Carbon::parse($project->ends_at)->format('d/m/Y') : 'Sin fecha',
                    'finished_at' => $project->finished_at,
                    'finished_at_label' => $project->finished_at ? Carbon::parse($project->finished_at)->format('d/m/Y') : 'Sin fecha',
                    'created_at' => $project->created_at?->format('d/m/Y H:i'),
                    'updated_at' => $project->updated_at?->format('d/m/Y H:i'),
                ],
            ]);
        }

        return redirect()->route('projects.index');
    }

    public function edit(Project $project)
    {
        $clients = Client::all();

        return view('admin.projects.edit', compact('project', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $rules = [
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:capturado,clasificacion_pendiente,priorizado,asignacion_lider_pendiente,en_diagnostico,en_diseno,en_implementacion,en_seguimiento,cerrado',
            'type' => 'nullable|string|in:desarrollo_web,infraestructura,consultoria,soporte,mobile,otro',
            'subtype' => 'nullable|string|max:100',
            'complexity' => 'nullable|string|in:baja,media,alta',
            'urgency' => 'nullable|string|in:baja,media,alta',
            'estimated_budget' => 'nullable|numeric|min:0',
            'final_budget' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'priority_score' => 'nullable|numeric|min:1|max:10',
            'priority_level' => 'nullable|string|in:baja,media,alta',
            'assigned_to' => 'nullable|exists:users,id',
            'validated_by' => 'nullable|exists:users,id',
            'progress' => 'nullable|integer|min:0|max:100',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'finished_at' => 'nullable|date',
        ];

        if (auth()->user()->hasRole('consultor')) {
            $rules['assigned_to'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules);

        $project->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Proyecto actualizado exitosamente',
                'project' => $this->mapProject($project->fresh()->load('client')),
            ]);
        }

        return redirect()->route('projects.index')->with('success', 'Proyecto actualizado exitosamente');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Proyecto eliminado exitosamente']);
        }

        return redirect()->route('projects.index')->with('success', 'Proyecto eliminado exitosamente');
    }

    private function mapProject(Project $project): array
    {
        return [
            'id' => $project->id,
            'client_id' => $project->client_id,
            'client_name' => $project->client?->name,
            'client_name_label' => $project->client?->name ?: 'Sin cliente',
            'title' => $project->title,
            'description' => $project->description,
            'status' => $project->status,
            'status_label' => ucfirst(str_replace('_', ' ', $project->status)),
            // Clasificación
            'type' => $project->type,
            'type_label' => ucfirst(str_replace('_', ' ', $project->type ?? '')) ?: 'Sin tipo',
            'subtype' => $project->subtype,
            'complexity' => $project->complexity,
            'complexity_label' => $project->complexity_label,
            'urgency' => $project->urgency,
            'urgency_label' => $project->urgency_label,
            // Presupuesto
            'estimated_budget' => $project->estimated_budget,
            'estimated_budget_label' => $project->estimated_budget_label,
            'final_budget' => $project->final_budget,
            'final_budget_label' => $project->final_budget_label,
            'budget_difference' => $project->budget_difference,
            'budget_difference_label' => $project->budget_difference_label,
            'currency' => $project->currency,
            // Prioridad
            'priority_score' => $project->priority_score,
            'priority_label' => $project->priority_label,
            'priority_level' => $project->priority_level,
            // Progreso
            'progress' => $project->progress,
            'progress_percent' => $project->progress_percent,
            // Responsables
            'captured_by_id' => $project->captured_by,
            'captured_by_name' => $project->capturedBy?->name,
            'captured_by_name_label' => $project->capturedBy?->name ?: 'No asignado',
            'assigned_to_id' => $project->assigned_to,
            'assigned_to_name' => $project->assignedTo?->name,
            'assigned_to_name_label' => $project->assignedTo?->name ?: 'No asignado',
            'validated_by_id' => $project->validated_by,
            'validated_by_name' => $project->validatedBy?->name,
            'validated_by_name_label' => $project->validatedBy?->name ?: 'No validado',
            // Fechas
            'starts_at_raw' => $project->starts_at ? Carbon::parse($project->starts_at)->format('Y-m-d') : null,
            'ends_at_raw' => $project->ends_at ? Carbon::parse($project->ends_at)->format('Y-m-d') : null,
            'finished_at_raw' => $project->finished_at ? Carbon::parse($project->finished_at)->format('Y-m-d') : null,
            'starts_at_label' => $project->starts_at ? Carbon::parse($project->starts_at)->format('d/m/Y') : 'Sin fecha',
            'ends_at_label' => $project->ends_at ? Carbon::parse($project->ends_at)->format('d/m/Y') : 'Sin fecha',
            'finished_at_label' => $project->finished_at ? Carbon::parse($project->finished_at)->format('d/m/Y') : 'Sin fecha',
            'created_at' => $project->created_at?->format('d/m/Y H:i'),
            'updated_at' => $project->updated_at?->format('d/m/Y H:i'),
        ];
    }
}
