@extends('layouts.vuexy')
@section('title', 'Dashboard')

@push('styles')
<style>
    .ethos-stat-card{
        border-radius: var(--ethos-radius);
        box-shadow: var(--ethos-depth-1);
        transition: transform 0.35s var(--ethos-ease), box-shadow 0.35s var(--ethos-ease), border-color 0.35s ease;
        position: relative;
        overflow: hidden;
        background: var(--vz-card-bg);
        border: 1px solid rgba(18, 45, 86, 0.12);
    }
    .ethos-stat-card::before{
        content:'';
        position:absolute;
        inset:0;
        background:
            radial-gradient(900px 220px at 12% 0%, rgba(var(--vz-info-rgb), 0.18) 0%, rgba(var(--vz-info-rgb), 0) 60%),
            linear-gradient(135deg, rgba(var(--vz-primary-rgb), 0.10) 0%, rgba(214,179,106,0.08) 55%, rgba(var(--vz-primary-rgb), 0.06) 100%);
        pointer-events:none;
        opacity:0.95;
    }
    .ethos-stat-card::after{
        content:'';
        position:absolute;
        top:14px;
        bottom:14px;
        left:14px;
        width:10px;
        border-radius:999px;
        background:
            repeating-linear-gradient(
                180deg,
                rgba(214,179,106,0.26) 0,
                rgba(214,179,106,0.26) 6px,
                rgba(214,179,106,0) 6px,
                rgba(214,179,106,0) 12px
            );
        pointer-events:none;
        opacity:0.55;
    }
    .ethos-stat-card:hover{
        transform: translateY(-4px);
        box-shadow: var(--ethos-depth-2);
        border-color: rgba(var(--vz-primary-rgb), 0.28);
    }
    .ethos-stat-card:focus-within{
        border-color: rgba(var(--vz-info-rgb), 0.55);
        box-shadow: 0 0 0 0.22rem rgba(var(--vz-info-rgb), 0.16), var(--ethos-depth-2);
    }

    .ethos-stat-card.primary-hover:hover{ border-color: rgba(var(--vz-primary-rgb), 0.30); }
    .ethos-stat-card.info-hover:hover{ border-color: rgba(var(--vz-info-rgb), 0.38); }
    .ethos-stat-card.warning-hover:hover{ border-color: rgba(var(--vz-warning-rgb), 0.40); }
    .ethos-stat-card.success-hover:hover{ border-color: rgba(var(--vz-success-rgb), 0.34); }

    .ethos-stat-title {
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: color-mix(in srgb, var(--vz-body-color) 78%, transparent);
    }
    .ethos-stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--vz-heading-color);
        line-height: 1.2;
        font-family: var(--ethos-display-font);
    }
    
    .ethos-glass-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .ethos-glass-table th {
        background: rgba(var(--vz-primary-rgb), 0.04) !important;
        color: color-mix(in srgb, var(--vz-body-color) 85%, transparent) !important;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        border-bottom: 1px solid var(--vz-border-color);
        padding: 1rem 1.25rem;
    }
    .ethos-glass-table td {
        background: transparent !important;
        color: var(--vz-body-color);
        border-bottom: 1px solid rgba(18, 45, 86, 0.10);
        padding: 1rem 1.25rem;
        vertical-align: middle;
    }
    .ethos-glass-table tbody tr {
        transition: background-color 0.2s ease;
    }
    .ethos-glass-table tbody tr:hover {
        background-color: rgba(var(--vz-primary-rgb), 0.04) !important;
    }
    .ethos-glass-table tbody tr:hover td {
        background-color: transparent !important;
    }
    
    .ethos-badge-glow {
        box-shadow: 0 0 10px currentcolor;
    }
    
    /* ApexCharts Dark Mode Overrides */
    .apexcharts-legend-text {
        color: color-mix(in srgb, var(--vz-body-color) 85%, transparent) !important;
    }
    .apexcharts-tooltip {
        background: var(--vz-card-bg) !important;
        border: 1px solid var(--vz-border-color) !important;
        box-shadow: var(--ethos-depth-1) !important;
        color: var(--vz-heading-color) !important;
    }
    .apexcharts-tooltip-title {
        background: rgba(var(--vz-primary-rgb), 0.04) !important;
        border-bottom: 1px solid var(--vz-border-color) !important;
        font-family: inherit !important;
    }
</style>
@endpush

@section('content')
@php
    $statusBadge = [
        'capturado' => 'bg-label-primary',
        'clasificacion_pendiente' => 'bg-label-warning',
        'priorizado' => 'bg-label-info',
        'asignacion_lider_pendiente' => 'bg-label-warning',
        'en_diagnostico' => 'bg-label-dark',
        'en_diseno' => 'bg-label-secondary',
        'en_implementacion' => 'bg-label-primary',
        'en_seguimiento' => 'bg-label-info',
        'cerrado' => 'bg-label-success',
    ];
@endphp
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 ethos-stat-card primary-hover">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span class="ethos-stat-title">Clientes</span>
                        <div class="d-flex align-items-center my-2">
                            <h3 class="ethos-stat-value mb-0">{{ number_format($totalClients) }}</h3>
                        </div>
                    </div>
                    <span class="avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
                        <i class="ti ti-users fs-3"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 ethos-stat-card info-hover">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span class="ethos-stat-title">Proyectos Totales</span>
                        <div class="d-flex align-items-center my-2">
                            <h3 class="ethos-stat-value mb-0">{{ number_format($totalProjects) }}</h3>
                        </div>
                    </div>
                    <span class="avatar-md bg-label-info rounded p-2 d-flex align-items-center justify-content-center">
                        <i class="ti ti-briefcase fs-3"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 ethos-stat-card warning-hover">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span class="ethos-stat-title">Proyectos Activos</span>
                        <div class="d-flex align-items-center my-2">
                            <h3 class="ethos-stat-value mb-0">{{ number_format($activeProjects) }}</h3>
                        </div>
                    </div>
                    <span class="avatar-md bg-label-warning rounded p-2 d-flex align-items-center justify-content-center">
                        <i class="ti ti-progress fs-3"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 ethos-stat-card success-hover">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span class="ethos-stat-title">Por Cerrar (30 Días)</span>
                        <div class="d-flex align-items-center my-2">
                            <h3 class="ethos-stat-value mb-0">{{ number_format($endingSoon) }}</h3>
                        </div>
                    </div>
                    <span class="avatar-md bg-label-success rounded p-2 d-flex align-items-center justify-content-center">
                        <i class="ti ti-calendar-check fs-3"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card h-100 ethos-stat-card" style="border-color: rgba(255,255,255,0.02);">
            <div class="card-header pb-1">
                <h5 class="card-title mb-0 fw-bold">Proyectos creados por mes</h5>
            </div>
            <div class="card-body">
                <div id="projectsByMonthChart" style="min-height: 300px;"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100 ethos-stat-card" style="border-color: rgba(255,255,255,0.02);">
            <div class="card-header pb-1">
                <h5 class="card-title mb-0 fw-bold">Distribución por estado</h5>
            </div>
            <div class="card-body">
                <div id="projectsByStatusChart" style="min-height: 300px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="card ethos-stat-card" style="border-color: rgba(255,255,255,0.02);">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom border-light border-opacity-10 pb-3">
                <h5 class="card-title mb-0 fw-bold"><i class="ti ti-list-details me-2 text-primary"></i>Proyectos Recientes</h5>
                <a href="{{ route('projects.index') }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                    Ver todos <i class="ti ti-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive text-nowrap">
                    <table class="ethos-glass-table">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Cliente</th>
                                <th>Estado</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentProjects as $project)
                            <tr>
                                <td class="fw-medium text-heading">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-label-primary rounded-circle me-3 d-flex align-items-center justify-content-center">
                                            <i class="ti ti-briefcase-2"></i>
                                        </div>
                                        {{ $project->title }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-building-skyscraper text-muted me-2"></i>
                                        {{ $project->client?->name ?? 'Sin cliente' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill {{ $statusBadge[$project->status] ?? 'bg-label-secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="ti ti-calendar-event me-1"></i>
                                        {{ $project->starts_at ? \Carbon\Carbon::parse($project->starts_at)->format('d/m/Y') : 'Sin fecha' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="ti ti-calendar-check me-1"></i>
                                        {{ $project->ends_at ? \Carbon\Carbon::parse($project->ends_at)->format('d/m/Y') : 'Sin fecha' }}
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center opacity-50">
                                        <i class="ti ti-inbox fs-1 mb-2"></i>
                                        <p class="mb-0">Aún no hay proyectos registrados.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const css = getComputedStyle(document.documentElement);
    const primary = (css.getPropertyValue('--vz-primary') || '#1F6FEB').trim();
    const info = (css.getPropertyValue('--vz-info') || '#2ED3FF').trim();
    const success = (css.getPropertyValue('--vz-success') || '#28C76F').trim();
    const warning = (css.getPropertyValue('--vz-warning') || '#FF9F43').trim();
    const secondary = (css.getPropertyValue('--vz-secondary') || '#A8AAAE').trim();
    const headingColor = getComputedStyle(document.documentElement).getPropertyValue('--vz-heading-color').trim() || '#cfd3ec';
    const borderColor = getComputedStyle(document.documentElement).getPropertyValue('--vz-border-color').trim() || 'rgba(255,255,255,0.1)';

    const monthLabels = @json($monthlyProjects['labels']);
    const monthValues = @json($monthlyProjects['series']);
    const statusLabels = @json($projectsByStatus->pluck('label')->values());
    const statusValues = @json($projectsByStatus->pluck('total')->values());

    const isDarkStyle = document.documentElement.classList.contains('dark-style') || document.body.classList.contains('dark-mode');
    const themeMode = isDarkStyle ? 'dark' : 'light';

    const monthElement = document.querySelector('#projectsByMonthChart');
    if (monthElement) {
        new ApexCharts(monthElement, {
            chart: { 
                type: 'bar', 
                height: 300, 
                toolbar: { show: false },
                fontFamily: 'inherit',
                foreColor: '#a8aaae'
            },
            series: [{ name: 'Proyectos', data: monthValues }],
            colors: [primary],
            plotOptions: { 
                bar: { 
                    borderRadius: 6, 
                    columnWidth: '35%',
                    colors: {
                        backgroundBarColors: [borderColor],
                        backgroundBarOpacity: 0.1,
                        backgroundBarRadius: 6,
                    }
                } 
            },
            dataLabels: { enabled: false },
            xaxis: { 
                categories: monthLabels,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: '#a8aaae' } }
            },
            yaxis: { 
                min: 0, 
                forceNiceScale: true,
                labels: { style: { colors: '#a8aaae' } }
            },
            grid: {
                borderColor: borderColor,
                strokeDashArray: 4,
                padding: { top: 0, bottom: 0, left: 10, right: 10 }
            },
            tooltip: { theme: themeMode }
        }).render();
    }

    const statusElement = document.querySelector('#projectsByStatusChart');
    if (statusElement) {
        new ApexCharts(statusElement, {
            chart: { 
                type: 'donut', 
                height: 320,
                fontFamily: 'inherit',
                foreColor: '#a8aaae'
            },
            series: statusValues,
            labels: statusLabels,
            colors: [primary, warning, info, secondary, '#0B3D91', '#061B3D', '#4B8CFF', info, success],
            legend: { 
                position: 'bottom',
                markers: { radius: 12 },
                itemMargin: { horizontal: 10, vertical: 5 },
                labels: { colors: '#a8aaae' }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%',
                        labels: {
                            show: true,
                            name: { fontSize: '0.875rem', color: '#a8aaae' },
                            value: {
                                fontSize: '2rem',
                                fontWeight: 700,
                                color: headingColor,
                                formatter: function (val) { return val; }
                            },
                            total: {
                                show: true,
                                fontSize: '0.875rem',
                                color: '#a8aaae',
                                label: 'Total',
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
            stroke: { width: 0 },
            tooltip: { theme: themeMode }
        }).render();
    }
});
</script>
@endpush
