<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:60'],
            'category' => ['nullable', 'string', 'in:all,clients,projects,navigation'],
        ]);

        $q = trim($validated['q']);
        $category = $validated['category'] ?? 'all';

        $results = [
            'query' => $q,
            'category' => $category,
            'items' => [],
        ];

        $add = function (array $item) use (&$results) {
            $results['items'][] = $item;
        };

        if ($category === 'all' || $category === 'navigation') {
            $nav = [
                [
                    'type' => 'navigation',
                    'category' => 'Navegación',
                    'title' => 'Inicio',
                    'subtitle' => '/admin/dashboard',
                    'url' => '/admin/dashboard',
                    'icon' => 'ti ti-smart-home',
                ],
                [
                    'type' => 'navigation',
                    'category' => 'Navegación',
                    'title' => 'Clientes',
                    'subtitle' => '/admin/clients',
                    'url' => '/admin/clients',
                    'icon' => 'ti ti-users',
                ],
                [
                    'type' => 'navigation',
                    'category' => 'Navegación',
                    'title' => 'Proyectos',
                    'subtitle' => '/admin/projects',
                    'url' => '/admin/projects',
                    'icon' => 'ti ti-briefcase',
                ],
            ];

            foreach ($nav as $item) {
                $haystack = mb_strtolower($item['title'] . ' ' . $item['subtitle']);
                if (str_contains($haystack, mb_strtolower($q))) {
                    $add($item);
                }
            }
        }

        if ($category === 'all' || $category === 'clients') {
            $clients = Client::query()
                ->select(['id', 'name', 'industry', 'primary_contact_name'])
                ->where(function ($query) use ($q) {
                    $query
                        ->where('name', 'like', "%{$q}%")
                        ->orWhere('industry', 'like', "%{$q}%")
                        ->orWhere('primary_contact_name', 'like', "%{$q}%");
                })
                ->orderByDesc('id')
                ->limit(6)
                ->get();

            foreach ($clients as $client) {
                $add([
                    'type' => 'client',
                    'category' => 'Clientes',
                    'title' => $client->name,
                    'subtitle' => $client->industry ?: 'Sin industria',
                    'url' => '/admin/clients',
                    'icon' => 'ti ti-building-skyscraper',
                    'meta' => ['id' => $client->id],
                ]);
            }
        }

        if ($category === 'all' || $category === 'projects') {
            $projects = Project::query()
                ->select(['id', 'title', 'status', 'client_id'])
                ->with(['client:id,name'])
                ->where(function ($query) use ($q) {
                    $query
                        ->where('title', 'like', "%{$q}%")
                        ->orWhere('status', 'like', "%{$q}%");
                })
                ->orderByDesc('id')
                ->limit(6)
                ->get();

            foreach ($projects as $project) {
                $add([
                    'type' => 'project',
                    'category' => 'Proyectos',
                    'title' => $project->title,
                    'subtitle' => $project->client?->name ?: ucfirst(str_replace('_', ' ', (string) $project->status)),
                    'url' => '/admin/projects',
                    'icon' => 'ti ti-briefcase-2',
                    'meta' => ['id' => $project->id],
                ]);
            }
        }

        $results['items'] = array_slice($results['items'], 0, 12);

        return response()->json($results);
    }
}
