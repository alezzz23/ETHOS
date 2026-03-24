<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $statusOrder = [
            'capturado',
            'clasificacion_pendiente',
            'priorizado',
            'asignacion_lider_pendiente',
            'en_diagnostico',
            'en_diseno',
            'en_implementacion',
            'en_seguimiento',
            'cerrado',
        ];

        $statusLabels = [
            'capturado' => 'Capturado',
            'clasificacion_pendiente' => 'Clasificación pendiente',
            'priorizado' => 'Priorizado',
            'asignacion_lider_pendiente' => 'Asignación líder pendiente',
            'en_diagnostico' => 'En diagnóstico',
            'en_diseno' => 'En diseño',
            'en_implementacion' => 'En implementación',
            'en_seguimiento' => 'En seguimiento',
            'cerrado' => 'Cerrado',
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

        return view('admin.dashboard', [
            'totalClients' => $totalClients,
            'totalProjects' => $totalProjects,
            'activeProjects' => $activeProjects,
            'closedProjects' => $closedProjects,
            'projectsByStatus' => $projectsByStatus,
            'monthlyProjects' => $monthlyProjects,
            'recentProjects' => $recentProjects,
            'endingSoon' => $endingSoon,
        ]);
    }
}
