<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBaseEntry;
use App\Models\Service;
use App\Models\SurveyResponse;
use App\Models\SatisfactionSurvey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KnowledgeBaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:admin.access');
    }

    // ─── NPS Dashboard ────────────────────────────────────────────

    public function dashboard(): View
    {
        $responses = SurveyResponse::with('survey.project.client')->get();

        $npsScores = $responses->whereNotNull('nps_score')->pluck('nps_score');
        $promoters  = $npsScores->filter(fn($s) => $s >= 9)->count();
        $passives   = $npsScores->filter(fn($s) => $s >= 7 && $s < 9)->count();
        $detractors = $npsScores->filter(fn($s) => $s < 7)->count();
        $total      = $npsScores->count();
        $nps        = $total > 0
            ? round((($promoters - $detractors) / $total) * 100)
            : null;

        $cesAvg  = $responses->whereNotNull('ces_score')->avg('ces_score');
        $csatAvg = $responses->whereNotNull('csat_score')->avg('csat_score');

        $pendingSurveys = SatisfactionSurvey::pending()->count();

        $entries  = KnowledgeBaseEntry::with(['service', 'createdBy'])->latest()->paginate(15);
        $services = Service::active()->orderBy('short_name')->get(['id', 'short_name']);

        return view('admin.knowledge-base.dashboard', compact(
            'nps', 'promoters', 'passives', 'detractors', 'total',
            'cesAvg', 'csatAvg', 'pendingSurveys',
            'entries', 'services', 'responses'
        ));
    }

    // ─── KB CRUD ──────────────────────────────────────────────────

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_id'        => ['nullable', 'exists:services,id'],
            'category'          => ['required', 'string', 'in:faq,process,case_study,definition'],
            'title'             => ['required', 'string', 'max:255'],
            'content'           => ['required', 'string'],
            'embedding_summary' => ['nullable', 'string', 'max:500'],
        ]);

        $entry = KnowledgeBaseEntry::create([
            ...$validated,
            'created_by' => $request->user()->id,
            'is_active'  => true,
        ]);

        return response()->json([
            'message' => 'Entrada de base de conocimiento creada.',
            'entry'   => $entry->load(['service', 'createdBy']),
        ], 201);
    }

    public function update(Request $request, KnowledgeBaseEntry $entry): JsonResponse
    {
        $validated = $request->validate([
            'service_id'        => ['nullable', 'exists:services,id'],
            'category'          => ['required', 'string', 'in:faq,process,case_study,definition'],
            'title'             => ['required', 'string', 'max:255'],
            'content'           => ['required', 'string'],
            'embedding_summary' => ['nullable', 'string', 'max:500'],
            'is_active'         => ['boolean'],
        ]);

        $entry->update($validated);

        return response()->json(['message' => 'Entrada actualizada.', 'entry' => $entry]);
    }

    public function destroy(KnowledgeBaseEntry $entry): JsonResponse
    {
        $entry->delete();

        return response()->json(['message' => 'Entrada eliminada.']);
    }
}
