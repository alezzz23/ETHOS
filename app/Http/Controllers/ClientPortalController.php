<?php

namespace App\Http\Controllers;

use App\Models\ClientPortalToken;
use App\Models\Project;
use Illuminate\Http\Request;

class ClientPortalController extends Controller
{
    /**
     * Show portal view for a given token.
     */
    public function show(Request $request, string $token)
    {
        $portalToken = ClientPortalToken::valid()
            ->where('token', $token)
            ->with(['project.client', 'project.proposals.service', 'project.checklists.items'])
            ->first();

        if (!$portalToken) {
            abort(404, 'Enlace de portal no válido o expirado.');
        }

        $portalToken->markAccessed();

        $project    = $portalToken->project;
        $proposals  = $project->proposals()->where('status', 'approved')->with('service')->get();
        $checklists = $project->checklists()->with('items')->get();

        return view('portal.project', compact('project', 'proposals', 'checklists', 'portalToken'));
    }
}
