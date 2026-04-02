<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Proposal;
use App\Models\Service;
use Illuminate\Support\Collection;

class ServiceRecommenderService
{
    /**
     * Returns up to $limit services recommended for the given client,
     * excluding services they already have approved proposals for.
     */
    public function recommendForClient(Client $client, int $limit = 3): Collection
    {
        // 1. Services this client already has approved proposals for
        $usedServiceIds = Proposal::whereHas('project', fn ($q) => $q->where('client_id', $client->id))
            ->where('status', 'approved')
            ->pluck('service_id')
            ->unique();

        // 2. Functional areas the client has been served in
        $servedAreas = Service::whereIn('id', $usedServiceIds)
            ->get()
            ->flatMap(fn ($s) => $s->functional_areas ?? [])
            ->unique()
            ->values();

        if ($servedAreas->isEmpty()) {
            // No history: recommend the most popular active services
            return Service::active()
                ->whereNotIn('id', $usedServiceIds)
                ->withCount(['proposals' => fn ($q) => $q->where('status', 'approved')])
                ->orderByDesc('proposals_count')
                ->limit($limit)
                ->get();
        }

        // 3. Services in the same functional areas, ordered by popularity
        $candidates = Service::active()
            ->whereNotIn('id', $usedServiceIds)
            ->withCount(['proposals' => fn ($q) => $q->where('status', 'approved')])
            ->get()
            ->sortByDesc('proposals_count');

        // Score: count overlapping functional areas with client history
        $scored = $candidates->map(function (Service $service) use ($servedAreas) {
            $overlap = collect($service->functional_areas ?? [])
                ->intersect($servedAreas)
                ->count();

            return ['service' => $service, 'score' => $overlap * 10 + $service->proposals_count];
        })
        ->sortByDesc('score')
        ->take($limit);

        return $scored->pluck('service');
    }

    /**
     * Returns recommendations for a given project's client.
     */
    public function recommendForProject(\App\Models\Project $project, int $limit = 3): Collection
    {
        if (!$project->client) {
            return collect();
        }

        return $this->recommendForClient($project->client, $limit);
    }
}
