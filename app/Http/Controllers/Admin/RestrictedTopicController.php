<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestrictedTopic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RestrictedTopicController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:admin.access');
    }

    // ─── Index ─────────────────────────────────────────────────────

    public function index(Request $request): View|JsonResponse
    {
        $topics = RestrictedTopic::with('createdBy')->latest()->get();

        if ($request->expectsJson()) {
            return response()->json(['topics' => $topics]);
        }

        return view('admin.restricted-topics.index', ['topics' => $topics]);
    }

    // ─── Store ─────────────────────────────────────────────────────

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'topic'            => 'required|string|max:255',
            'keywords'         => 'required|array|min:1|max:30',
            'keywords.*'       => 'string|max:120',
            'response_message' => 'required|string|max:1000',
            'is_active'        => 'boolean',
        ]);

        $topic = RestrictedTopic::create([
            'topic'            => $validated['topic'],
            'keywords'         => array_values(array_filter($validated['keywords'])),
            'response_message' => $validated['response_message'],
            'is_active'        => $validated['is_active'] ?? true,
            'created_by'       => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Tópico restringido creado.',
            'topic'   => $topic,
        ], 201);
    }

    // ─── Update ────────────────────────────────────────────────────

    public function update(Request $request, RestrictedTopic $restrictedTopic): JsonResponse
    {
        $validated = $request->validate([
            'topic'            => 'required|string|max:255',
            'keywords'         => 'required|array|min:1|max:30',
            'keywords.*'       => 'string|max:120',
            'response_message' => 'required|string|max:1000',
            'is_active'        => 'boolean',
        ]);

        $restrictedTopic->update([
            'topic'            => $validated['topic'],
            'keywords'         => array_values(array_filter($validated['keywords'])),
            'response_message' => $validated['response_message'],
            'is_active'        => $validated['is_active'] ?? $restrictedTopic->is_active,
        ]);

        return response()->json([
            'message' => 'Tópico restringido actualizado.',
            'topic'   => $restrictedTopic->fresh(),
        ]);
    }

    // ─── Destroy ───────────────────────────────────────────────────

    public function destroy(RestrictedTopic $restrictedTopic): JsonResponse
    {
        $restrictedTopic->delete();

        return response()->json(['message' => 'Tópico eliminado.']);
    }
}
