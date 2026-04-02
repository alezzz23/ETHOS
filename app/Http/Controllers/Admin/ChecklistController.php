<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChecklistItem;
use App\Models\ProjectChecklist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChecklistController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:proposals.view')->only(['index', 'show']);
        $this->middleware('permission:proposals.edit')->only(['completeItem', 'update']);
    }

    // ─── List checklists for a project ────────────────────────────

    public function index(Request $request): View
    {
        $checklists = ProjectChecklist::with(['project.client', 'service', 'items'])
            ->when($request->filled('project_id'), fn ($q) => $q->where('project_id', $request->project_id))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.checklists.index', compact('checklists'));
    }

    // ─── Show single checklist JSON ───────────────────────────────

    public function show(ProjectChecklist $checklist): JsonResponse
    {
        $checklist->load(['items.assignedTo', 'project', 'service']);

        return response()->json($checklist);
    }

    // ─── Toggle a checklist item complete/incomplete ───────────────

    public function completeItem(Request $request, ChecklistItem $item): JsonResponse
    {
        $item->update([
            'is_completed' => !$item->is_completed,
            'completed_at' => !$item->is_completed ? now() : null,
        ]);

        // Check if all items done → mark checklist complete
        $checklist = $item->checklist;
        $allDone   = $checklist->items()->where('is_completed', false)->doesntExist();

        if ($allDone && $checklist->status !== 'completed') {
            $checklist->update(['status' => 'completed', 'completed_at' => now()]);
        }

        return response()->json([
            'is_completed'       => $item->is_completed,
            'checklist_status'   => $checklist->fresh()->status,
            'completion_percent' => $checklist->completion_percent,
        ]);
    }

    // ─── Assign item to a user ────────────────────────────────────

    public function assignItem(Request $request, ChecklistItem $item): JsonResponse
    {
        $validated = $request->validate([
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        $item->update(['assigned_to' => $validated['assigned_to']]);

        return response()->json(['message' => 'Ítem asignado.', 'item' => $item]);
    }
}
