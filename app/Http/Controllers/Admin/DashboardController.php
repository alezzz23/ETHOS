<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\Service;
use App\Models\SurveyResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $statusOrder = [
            Project::STATUS_CAPTURADO,
            Project::STATUS_EN_ANALISIS,
            Project::STATUS_APROBADO,
            Project::STATUS_EN_EJECUCION,
            Project::STATUS_CERRADO,
        ];

        $statusLabels = [
            Project::STATUS_CAPTURADO     => 'Capturado',
            Project::STATUS_EN_ANALISIS   => 'En análisis',
            Project::STATUS_APROBADO      => 'Aprobado',
            Project::STATUS_EN_EJECUCION  => 'En ejecución',
            Project::STATUS_CERRADO       => 'Cerrado',
        ];

        $totalClients = Client::count();
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', '!=', 'cerrado')->count();
        $closedProjects = Project::where('status', 'cerrado')->count();

        $projectsByStatusRaw = Project::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $projectsByStatus = collect($statusOrder)->map(function (string $status) use ($projectsByStatusRaw, $statusLabels) {
            return [
                'status' => $status,
                'label' => $statusLabels[$status],
                'total' => (int) ($projectsByStatusRaw[$status] ?? 0),
            ];
        });

        $months = collect(range(5, 0))->reverse()->map(function (int $offset) {
            $date = Carbon::now()->subMonths($offset);

            return [
                'key' => $date->format('Y-m'),
                'label' => $date->translatedFormat('M'),
            ];
        })->values();

        $monthlyRaw = Project::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, COUNT(*) as total")
            ->whereDate('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        $monthlyProjects = [
            'labels' => $months->pluck('label')->values(),
            'series' => $months->map(fn (array $month) => (int) ($monthlyRaw[$month['key']] ?? 0))->values(),
        ];

        $recentProjects = Project::with('client')->latest()->take(5)->get();

        $endingSoon = Project::query()
            ->whereNotNull('ends_at')
            ->whereDate('ends_at', '>=', Carbon::today())
            ->whereDate('ends_at', '<=', Carbon::today()->addDays(30))
            ->count();

        // ─── KPIs ejecutivos adicionales ──────────────────────────
        $totalRevenue = Project::where('status', Project::STATUS_CERRADO)
            ->whereNotNull('final_budget')
            ->sum('final_budget');

        $avgDeviation = Project::whereNotNull('deviation_percent')
            ->avg('deviation_percent');

        $avgCloseDays = Project::where('status', Project::STATUS_CERRADO)
            ->whereNotNull('closed_at')
            ->selectRaw('AVG(DATEDIFF(closed_at, created_at)) as avg_days')
            ->value('avg_days');

        $topServices = Service::query()
            ->select('services.id', 'services.short_name',
                     DB::raw('COUNT(projects.id) as projects_count'))
            ->leftJoin('projects', 'projects.service_id', '=', 'services.id')
            ->groupBy('services.id', 'services.short_name')
            ->orderByDesc('projects_count')
            ->take(5)
            ->get();

        $npsAverage = SurveyResponse::avg('nps_score');

        return view('admin.dashboard', [
            'totalClients'   => $totalClients,
            'totalProjects'  => $totalProjects,
            'activeProjects' => $activeProjects,
            'closedProjects' => $closedProjects,
            'projectsByStatus' => $projectsByStatus,
            'monthlyProjects'  => $monthlyProjects,
            'recentProjects'   => $recentProjects,
            'endingSoon'       => $endingSoon,
            // nuevos KPIs
            'totalRevenue'  => $totalRevenue,
            'avgDeviation'  => round((float) $avgDeviation, 1),
            'avgCloseDays'  => round((float) $avgCloseDays),
            'topServices'   => $topServices,
            'npsAverage'    => $npsAverage !== null ? round((float) $npsAverage, 1) : null,
        ]);
    }
}
