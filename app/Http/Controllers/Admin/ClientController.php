<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:clients.view')->only(['index', 'show']);
        $this->middleware('permission:clients.create')->only(['create', 'store']);
        $this->middleware('permission:clients.edit')->only(['edit', 'update']);
        $this->middleware('permission:clients.delete')->only(['destroy']);
    }

    public function index()
    {
        $clients = Client::withCount('projects')->latest()->paginate(10);

        return view('admin.clients.index', [
            'clients' => $clients,
            'googleMapsApiKey' => config('services.google_maps.api_key'),
        ]);
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'primary_contact_name' => 'nullable|string|max:255',
            'primary_contact_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            // Ubicación
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'municipality' => 'nullable|string|max:255',
            'parish' => 'nullable|string|max:255',
            // Coordenadas
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $client = Client::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Cliente creado exitosamente',
                'client' => $this->mapClient($client),
            ]);
        }

        return redirect()->route('clients.index')->with('success', 'Cliente creado exitosamente');
    }

    public function show(Client $client, Request $request)
    {
        if ($request->expectsJson()) {
            $client->load(['projects' => function ($query) {
                $query->latest()->limit(10);
            }]);

            return response()->json([
                'client' => [
                    'id' => $client->id,
                    'name' => $client->name,
                    'industry' => $client->industry,
                    'industry_label' => $client->industry ?: 'Sin industria',
                    'primary_contact_name' => $client->primary_contact_name,
                    'primary_contact_name_label' => $client->primary_contact_name ?: 'Sin contacto',
                    'primary_contact_email' => $client->primary_contact_email,
                    'primary_contact_email_label' => $client->primary_contact_email ?: 'Sin email',
                    'primary_contact_phone' => $client->phone,
                    'primary_contact_phone_label' => $client->phone ?: 'Sin teléfono',
                    'secondary_contact_name' => $client->secondary_contact_name,
                    'secondary_contact_email' => $client->secondary_contact_email,
                    'secondary_contact_phone' => null,
                    'business_type' => $client->type,
                    'business_type_label' => $client->type ?: 'No especificado',
                    'website' => null,
                    // Ubicación completa
                    'address' => $client->address,
                    'city' => $client->city,
                    'state' => $client->state,
                    'country' => $client->country,
                    'municipality' => $client->municipality,
                    'parish' => $client->parish,
                    'postal_code' => null,
                    // Coordenadas
                    'latitude' => $client->latitude,
                    'longitude' => $client->longitude,
                    'status' => $client->status,
                    'notes' => $client->notes,
                    'created_at' => $client->created_at?->format('d/m/Y H:i'),
                    'updated_at' => $client->updated_at?->format('d/m/Y H:i'),
                    'projects_count' => $client->projects->count(),
                    'projects' => $client->projects->map(function ($project) {
                        return [
                            'id' => $project->id,
                            'title' => $project->title,
                            'status' => $project->status,
                            'status_label' => ucfirst(str_replace('_', ' ', $project->status)),
                            'starts_at' => $project->starts_at ? \Carbon\Carbon::parse($project->starts_at)->format('d/m/Y') : null,
                            'ends_at' => $project->ends_at ? \Carbon\Carbon::parse($project->ends_at)->format('d/m/Y') : null,
                        ];
                    }),
                ],
            ]);
        }

        return view('admin.clients.show', [
            'client' => $client,
            'googleMapsApiKey' => config('services.google_maps.api_key'),
            'googleMapsMapId' => config('services.google_maps.map_id'),
        ]);
    }

    public function markers(Request $request)
    {
        $query = Client::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        $contactType = $request->string('contact_type')->toString();
        if ($contactType === 'primary') {
            $query->whereNotNull('primary_contact_name');
        } elseif ($contactType === 'secondary') {
            $query->whereNotNull('secondary_contact_name');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type')->toString());
        }

        $clients = $query
            ->orderBy('name')
            ->limit(2000)
            ->get(['id', 'name', 'type', 'status', 'primary_contact_name', 'secondary_contact_name', 'address', 'city', 'state', 'country', 'latitude', 'longitude']);

        return response()->json([
            'markers' => $clients->map(function (Client $client) {
                return [
                    'id' => $client->id,
                    'title' => $client->name,
                    'type' => $client->type,
                    'status' => $client->status,
                    'contact' => [
                        'primary' => $client->primary_contact_name,
                        'secondary' => $client->secondary_contact_name,
                    ],
                    'address' => trim(implode(', ', array_filter([
                        $client->address,
                        $client->city,
                        $client->state,
                        $client->country,
                    ]))),
                    'position' => [
                        'lat' => (float) $client->latitude,
                        'lng' => (float) $client->longitude,
                    ],
                    'url' => route('clients.show', $client),
                ];
            })->values(),
        ]);
    }

    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'primary_contact_name' => 'nullable|string|max:255',
            'primary_contact_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            // Ubicación
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'municipality' => 'nullable|string|max:255',
            'parish' => 'nullable|string|max:255',
            // Coordenadas
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $client->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Cliente actualizado exitosamente',
                'client' => $this->mapClient($client->fresh()),
            ]);
        }

        return redirect()->route('clients.index')->with('success', 'Cliente actualizado exitosamente');
    }

    public function destroy(Client $client)
    {
        $client->projects()->delete();
        $client->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Cliente eliminado exitosamente']);
        }

        return redirect()->route('clients.index')->with('success', 'Cliente eliminado exitosamente');
    }

    private function mapClient(Client $client): array
    {
        return [
            'id' => $client->id,
            'name' => $client->name,
            'industry' => $client->industry,
            'industry_label' => $client->industry ?: 'Sin industria',
            'primary_contact_name' => $client->primary_contact_name,
            'primary_contact_name_label' => $client->primary_contact_name ?: 'Sin contacto',
            'primary_contact_email' => $client->primary_contact_email,
            'primary_contact_email_label' => $client->primary_contact_email ?: 'Sin email',
            'phone' => $client->phone,
            'notes' => $client->notes,
            // Ubicación
            'address' => $client->address,
            'city' => $client->city,
            'state' => $client->state,
            'country' => $client->country,
            'municipality' => $client->municipality,
            'parish' => $client->parish,
            // Coordenadas
            'latitude' => $client->latitude,
            'longitude' => $client->longitude,
        ];
    }
}
