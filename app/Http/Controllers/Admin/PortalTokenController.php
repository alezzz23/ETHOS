<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientPortalToken;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PortalTokenController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:projects.edit');
    }

    public function store(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'expires_days' => ['nullable', 'integer', 'min:1', 'max:365'],
        ]);

        $expiresAt = isset($validated['expires_days'])
            ? now()->addDays((int) $validated['expires_days'])
            : null;

        $token = ClientPortalToken::generate($project, $expiresAt);

        $url = url("/portal/{$token->token}");

        return response()->json([
            'message'    => 'Enlace de portal generado.',
            'url'        => $url,
            'token'      => $token->token,
            'expires_at' => $token->expires_at?->toIso8601String(),
        ], 201);
    }

    public function revoke(Request $request, ClientPortalToken $token): JsonResponse
    {
        $token->update(['is_active' => false]);

        return response()->json(['message' => 'Enlace revocado.']);
    }
}
