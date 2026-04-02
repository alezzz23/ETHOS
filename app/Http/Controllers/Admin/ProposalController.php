<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\Project;
use App\Models\Service;
use App\Models\ClientSizeConfig;
use App\Services\ChecklistGeneratorService;
use App\Services\HourCalculatorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProposalController extends Controller
{
    public function __construct(
        private readonly HourCalculatorService    $calculator,
        private readonly ChecklistGeneratorService $checklistGenerator
    ) {
        $this->middleware('permission:proposals.view')->only(['index', 'show']);
        $this->middleware('permission:proposals.create')->only(['create', 'store']);
        $this->middleware('permission:proposals.edit')->only(['update', 'approve', 'reject']);
    }

    // ─── Index ────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $query = Proposal::with(['project.client', 'service', 'createdBy'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $proposals = $query->paginate(15)->withQueryString();
        $projects  = Project::orderBy('title')->get(['id', 'title']);
        $services  = Service::active()->orderBy('short_name')->get(['id', 'short_name']);
        $sizes     = ClientSizeConfig::orderBy('min_employees')->get();

        return view('admin.proposals.index', compact('proposals', 'projects', 'services', 'sizes'));
    }

    // ─── Show ─────────────────────────────────────────────────────

    public function show(Proposal $proposal): JsonResponse
    {
        $proposal->load(['project.client', 'service', 'createdBy', 'approvedBy']);

        return response()->json($proposal);
    }

    // ─── Create / Store ───────────────────────────────────────────

    public function create(Request $request): View
    {
        $projects = Project::with('client')->orderBy('title')->get();
        $services = Service::active()->with('processes.methods')->orderBy('short_name')->get();
        $sizes    = ClientSizeConfig::orderBy('min_employees')->get();

        $selectedProject = $request->project_id
            ? Project::find($request->project_id)
            : null;

        return view('admin.proposals.create', compact(
            'projects', 'services', 'sizes', 'selectedProject'
        ));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id'        => ['required', 'exists:projects,id'],
            'service_id'        => ['required', 'exists:services,id'],
            'client_size'       => ['required', 'string'],
            'hourly_rate'       => ['required', 'numeric', 'min:1'],
            'margin_percent'    => ['required', 'numeric', 'min:0', 'max:100'],
            'target_persons'    => ['nullable', 'integer', 'min:1'],
            'adjusted_hours'    => ['nullable', 'numeric', 'min:0'],
            'adjustment_reason' => ['nullable', 'string', 'max:500'],
            'payment_milestones' => ['nullable', 'array'],
            'payment_milestones.*.label'      => ['required_with:payment_milestones', 'string'],
            'payment_milestones.*.percentage' => ['required_with:payment_milestones', 'numeric', 'min:0', 'max:100'],
            'payment_milestones.*.due_days'   => ['nullable', 'integer', 'min:0'],
        ]);

        $service = Service::findOrFail($validated['service_id']);

        // Run hour calculation
        $calc = $this->calculator->calculate(
            $service,
            $validated['client_size'],
            (float) $validated['hourly_rate'],
            (float) $validated['margin_percent'],
            isset($validated['target_persons']) ? (int) $validated['target_persons'] : null
        );

        $adjustedHours  = $validated['adjusted_hours'] ?? $calc['total_hours'];
        $adjustedFactor = $calc['total_hours'] > 0
            ? ($adjustedHours / $calc['total_hours'])
            : 1;

        $proposal = Proposal::create([
            'project_id'         => $validated['project_id'],
            'service_id'         => $validated['service_id'],
            'created_by'         => $request->user()->id,
            'client_size'        => $validated['client_size'],
            'hourly_rate'        => $validated['hourly_rate'],
            'margin_percent'     => $validated['margin_percent'],
            'target_persons'     => $validated['target_persons'] ?? null,
            'total_hours'        => $calc['total_hours'],
            'adjusted_hours'     => $adjustedHours,
            'adjustment_reason'  => $validated['adjustment_reason'] ?? null,
            'price_min'          => $calc['price_min'] * $adjustedFactor,
            'price_max'          => $calc['price_max'] * $adjustedFactor,
            'payment_milestones' => $validated['payment_milestones'] ?? null,
            'status'             => 'draft',
        ]);

        return response()->json([
            'message'  => 'Propuesta creada exitosamente.',
            'proposal' => $proposal->load(['project', 'service']),
        ], 201);
    }

    // ─── Update ───────────────────────────────────────────────────

    public function update(Request $request, Proposal $proposal): JsonResponse
    {
        if (!in_array($proposal->status, ['draft', 'rejected'])) {
            return response()->json(['message' => 'Solo se pueden editar propuestas en borrador o rechazadas.'], 422);
        }

        $validated = $request->validate([
            'adjustment_reason'  => ['nullable', 'string', 'max:500'],
            'payment_milestones' => ['nullable', 'array'],
        ]);

        $proposal->update($validated);

        return response()->json(['message' => 'Propuesta actualizada.', 'proposal' => $proposal]);
    }

    // ─── Generate PDF ─────────────────────────────────────────────

    public function generatePdf(Proposal $proposal)
    {
        $proposal->load(['project.client', 'service', 'createdBy']);

        $pdf = Pdf::loadView('admin.proposals.pdf', compact('proposal'))
            ->setPaper('a4', 'portrait');

        $filename  = "propuesta-{$proposal->id}-{$proposal->project->id}.pdf";
        $path      = "proposals/{$filename}";

        \Illuminate\Support\Facades\Storage::put("public/{$path}", $pdf->output());
        $proposal->update(['pdf_path' => $path]);

        return $pdf->stream($filename);
    }

    // ─── Send ─────────────────────────────────────────────────────

    public function send(Request $request, Proposal $proposal): JsonResponse
    {
        if ($proposal->status !== 'draft') {
            return response()->json(['message' => 'Solo se pueden enviar propuestas en borrador.'], 422);
        }

        $proposal->update([
            'status'  => 'sent',
            'sent_at' => now(),
        ]);

        return response()->json(['message' => 'Propuesta marcada como enviada.', 'proposal' => $proposal]);
    }

    // ─── Approve ──────────────────────────────────────────────────

    public function approve(Request $request, Proposal $proposal): JsonResponse
    {
        if ($proposal->status !== 'sent') {
            return response()->json(['message' => 'Solo se pueden aprobar propuestas enviadas.'], 422);
        }

        $proposal->update([
            'status'      => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        // Auto-generate checklist on approval
        $this->checklistGenerator->generateFromProposal($proposal);

        return response()->json(['message' => 'Propuesta aprobada. Lista de levantamiento generada.', 'proposal' => $proposal]);
    }

    // ─── Reject ───────────────────────────────────────────────────

    public function reject(Request $request, Proposal $proposal): JsonResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        if ($proposal->status !== 'sent') {
            return response()->json(['message' => 'Solo se pueden rechazar propuestas enviadas.'], 422);
        }

        $proposal->update([
            'status'           => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'rejected_at'      => now(),
        ]);

        return response()->json(['message' => 'Propuesta rechazada.', 'proposal' => $proposal]);
    }
}
