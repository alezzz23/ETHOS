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
        $this->middleware('permission:clients.update')->only(['edit', 'update']);
    }

    public function index()
    {
        $clients = Client::latest()->paginate(10);

        return view('admin.clients.index', compact('clients'));
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
            'notes' => 'nullable|string',
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
            'notes' => 'nullable|string',
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
            'notes' => $client->notes,
        ];
    }
}
