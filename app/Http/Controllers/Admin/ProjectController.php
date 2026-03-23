<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:projects.view')->only(['index', 'show']);
        $this->middleware('permission:projects.create')->only(['create', 'store']);
        $this->middleware('permission:projects.update')->only(['edit', 'update']);
    }

    public function index()
    {
        $projects = Project::with('client')->latest()->paginate(10);
        $clients = Client::all();

        return view('admin.projects.index', compact('projects', 'clients'));
    }

    public function create()
    {
        $clients = Client::all();

        return view('admin.projects.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
        ]);

        $project = Project::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Proyecto creado exitosamente',
                'project' => $this->mapProject($project->fresh()->load('client')),
            ]);
        }

        return redirect()->route('projects.index')->with('success', 'Proyecto creado exitosamente');
    }

    public function edit(Project $project)
    {
        $clients = Client::all();

        return view('admin.projects.edit', compact('project', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
        ]);

        $project->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Proyecto actualizado exitosamente',
                'project' => $this->mapProject($project->fresh()->load('client')),
            ]);
        }

        return redirect()->route('projects.index')->with('success', 'Proyecto actualizado exitosamente');
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
            'starts_at_raw' => $project->starts_at,
            'ends_at_raw' => $project->ends_at,
            'starts_at_label' => $project->starts_at ? Carbon::parse($project->starts_at)->format('d/m/Y') : 'Sin fecha',
            'ends_at_label' => $project->ends_at ? Carbon::parse($project->ends_at)->format('d/m/Y') : 'Sin fecha',
        ];
    }
}
