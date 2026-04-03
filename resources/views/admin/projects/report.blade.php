@extends('layouts.vuexy')

@section('title', 'Reporte · ' . $project->title)

@push('styles')
<style>
    @media print {
        .btn, .no-print { display: none !important; }
        .card { break-inside: avoid; }
        body { background: #fff !important; }
    }
    .report-header { border-bottom: 3px solid var(--bs-primary); padding-bottom: 1rem; margin-bottom: 1.5rem; }
    .kpi-box { border: 1px solid rgba(0,0,0,.1); border-radius: .5rem; padding: 1rem; text-align: center; }
    .kpi-box .value { font-size: 1.8rem; font-weight: 700; }
    .phase-badge { font-size: .7rem; text-transform: uppercase; letter-spacing: .08em; }
</style>
@endpush

@section('content')

{{-- Actions bar --}}
<div class="d-flex gap-2 mb-4 no-print">
    <a href="{{ route('projects.show', $project) }}" class="btn btn-label-secondary btn-sm">
        <i class="ti ti-arrow-left me-1"></i>Volver al proyecto
    </a>
    <button onclick="window.print()" class="btn btn-primary btn-sm">
        <i class="ti ti-printer me-1"></i>Imprimir / Exportar PDF
    </button>
</div>

{{-- Header --}}
<div class="report-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h3 class="mb-1 fw-bold">{{ $project->title }}</h3>
            <p class="mb-0 text-muted">
                <i class="ti ti-building-skyscraper me-1"></i>{{ $project->client?->name ?? 'Sin cliente' }}
                &nbsp;·&nbsp; <i class="ti ti-calendar me-1"></i>Reporte generado: {{ now()->format('d/m/Y H:i') }}
            </p>
        </div>
        <div class="text-end">
            @php
                $statusColors = [
                    'capturado'    => 'secondary',
                    'en_analisis'  => 'warning',
                    'aprobado'     => 'success',
                    'en_ejecucion' => 'primary',
                    'cerrado'      => 'dark',
                ];
            @endphp
            <span class="badge bg-{{ $statusColors[$project->status] ?? 'secondary' }} fs-6 px-3 py-2">
                {{ $project->status_label }}
            </span>
        </div>
    </div>
</div>

{{-- KPI Row --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="kpi-box">
            <div class="value text-primary">{{ $project->progress ?? 0 }}%</div>
            <div class="progress mt-2" style="height:6px">
                <div class="progress-bar" style="width:{{ $project->progress ?? 0 }}%"></div>
            </div>
            <div class="small text-muted mt-1">Progreso general</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="kpi-box">
            <div class="value text-info">{{ $project->estimated_hours ?? '—' }}<small class="fs-6">h</small></div>
            <div class="small text-muted mt-1">Horas estimadas</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="kpi-box">
            <div class="value text-warning">{{ number_format($project->actual_hours ?? 0, 1) }}<small class="fs-6">h</small></div>
            <div class="small text-muted mt-1">Horas reales</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        @php $dev = $project->deviation_percent ?? 0; $devColor = abs($dev) >= 20 ? 'danger' : (abs($dev) >= 10 ? 'warning' : 'success'); @endphp
        <div class="kpi-box">
            <div class="value text-{{ $devColor }}">{{ number_format($dev, 1) }}%</div>
            <div class="small text-muted mt-1">Desvío H-H</div>
        </div>
    </div>
</div>

{{-- Project meta --}}
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0"><i class="ti ti-info-circle me-2"></i>Datos del Proyecto</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted small">Servicio</dt>
                    <dd class="col-7 small">{{ $project->service?->short_name ?? '—' }}</dd>
                    <dt class="col-5 text-muted small">Tipo</dt>
                    <dd class="col-7 small">{{ ucfirst($project->type ?? '—') }}</dd>
                    <dt class="col-5 text-muted small">Urgencia</dt>
                    <dd class="col-7 small">{{ ucfirst($project->urgency ?? '—') }}</dd>
                    <dt class="col-5 text-muted small">Complejidad</dt>
                    <dd class="col-7 small">{{ ucfirst($project->complexity ?? '—') }}</dd>
                    <dt class="col-5 text-muted small">Inicio</dt>
                    <dd class="col-7 small">{{ $project->starts_at?->format('d/m/Y') ?? '—' }}</dd>
                    <dt class="col-5 text-muted small">Fin previsto</dt>
                    <dd class="col-7 small">{{ $project->ends_at?->format('d/m/Y') ?? '—' }}</dd>
                    <dt class="col-5 text-muted small">Cierre real</dt>
                    <dd class="col-7 small">{{ $project->finished_at?->format('d/m/Y') ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0"><i class="ti ti-users me-2"></i>Equipo</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted small">Capturado por</dt>
                    <dd class="col-7 small">{{ $project->capturedBy?->name ?? '—' }}</dd>
                    <dt class="col-5 text-muted small">Consultor/a</dt>
                    <dd class="col-7 small">{{ $project->assignedTo?->name ?? '—' }}</dd>
                    <dt class="col-5 text-muted small">Líder</dt>
                    <dd class="col-7 small">{{ $project->leader?->name ?? '—' }}</dd>
                    <dt class="col-5 text-muted small">Tarifa/hora</dt>
                    <dd class="col-7 small">{{ $project->hourly_rate ? number_format($project->hourly_rate, 2) . ' ' . $project->currency : '—' }}</dd>
                    <dt class="col-5 text-muted small">Cost. estimado</dt>
                    <dd class="col-7 small">{{ $project->estimated_hours && $project->hourly_rate ? number_format($project->estimated_hours * $project->hourly_rate, 2) . ' ' . $project->currency : '—' }}</dd>
                    <dt class="col-5 text-muted small">Cost. real</dt>
                    <dd class="col-7 small fw-bold">{{ $project->final_budget ? number_format($project->final_budget, 2) . ' ' . $project->currency : '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

{{-- Propuesta aprobada --}}
@if($project->proposals->isNotEmpty())
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="ti ti-file-invoice me-2"></i>Propuesta Aprobada</h6>
    </div>
    <div class="card-body">
        @php $approvedProp = $project->proposals->first(); @endphp
        <div class="row g-3">
            <div class="col-md-3">
                <div class="text-muted small">Servicio</div>
                <div class="fw-semibold">{{ $approvedProp->service?->short_name ?? '—' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Horas propuestas</div>
                <div class="fw-semibold">{{ $approvedProp->adjusted_hours ?? $approvedProp->total_hours }}h</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Precio estimado</div>
                <div class="fw-semibold">{{ number_format($approvedProp->price_min ?? 0) }} – {{ number_format($approvedProp->price_max ?? 0) }} {{ $project->currency }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Aprobada el</div>
                <div class="fw-semibold">{{ $approvedProp->approved_at?->format('d/m/Y') ?? '—' }}</div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Avance por fase --}}
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="ti ti-chart-bar me-2"></i>Avance por Fase</h6>
    </div>
    <div class="card-body">
        @if($entriesByPhase->isEmpty())
        <p class="text-muted text-center py-3">Sin avances registrados.</p>
        @else
        <div class="row g-3 mb-4">
            @foreach($entriesByPhase as $phase => $data)
            <div class="col-md-4">
                <div class="kpi-box">
                    <span class="badge bg-label-primary phase-badge mb-1">{{ ucfirst($phase) }}</span>
                    <div class="value text-primary mt-1">{{ $data['avg_progress'] }}%</div>
                    <div class="small text-muted">{{ $data['total_hours'] }}h registradas</div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Full entries table --}}
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Fase</th>
                        <th>Método</th>
                        <th>Avance</th>
                        <th>Horas plan.</th>
                        <th>Horas reales</th>
                        <th>Peso</th>
                        <th>Registrado por</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($project->progressEntries->sortBy('date_worked') as $entry)
                    <tr>
                        <td class="small">{{ \Carbon\Carbon::parse($entry->date_worked)->format('d/m/Y') }}</td>
                        <td><span class="badge bg-label-info small">{{ ucfirst($entry->phase) }}</span></td>
                        <td><span class="badge bg-label-primary small">{{ ucfirst($entry->method ?? '—') }}</span></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height:5px;min-width:50px">
                                    <div class="progress-bar" style="width:{{ $entry->progress_pct }}%"></div>
                                </div>
                                <span class="small">{{ $entry->progress_pct }}%</span>
                            </div>
                        </td>
                        <td class="small">{{ $entry->planned_hours ? $entry->planned_hours . 'h' : '—' }}</td>
                        <td class="small fw-semibold">{{ $entry->actual_hours }}h</td>
                        <td class="small text-muted">{{ $entry->weight }}</td>
                        <td class="small">{{ $entry->recordedBy?->name ?? '—' }}</td>
                        <td class="small text-muted" style="max-width:180px;white-space:normal">{{ $entry->notes ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="4">Total</td>
                        <td class="small">{{ number_format($project->progressEntries->sum('planned_hours'), 1) }}h</td>
                        <td class="small">{{ number_format($project->progressEntries->sum('actual_hours'), 1) }}h</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- Checklists --}}
@if($project->checklists->isNotEmpty())
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="ti ti-checklist me-2"></i>Lista de Verificación</h6>
    </div>
    <div class="card-body">
        @foreach($project->checklists as $checklist)
        <h6 class="fw-semibold">{{ $checklist->title }}</h6>
        @php
            $done  = $checklist->items->where('is_completed', true)->count();
            $total = $checklist->items->count();
        @endphp
        <div class="d-flex align-items-center gap-2 mb-2">
            <div class="progress flex-grow-1" style="height:6px">
                <div class="progress-bar bg-success" style="width:{{ $total > 0 ? round($done/$total*100) : 0 }}%"></div>
            </div>
            <span class="small text-muted">{{ $done }}/{{ $total }}</span>
        </div>
        <ul class="list-group list-group-flush mb-3">
            @foreach($checklist->items->sortBy('order') as $item)
            <li class="list-group-item d-flex align-items-center gap-2 ps-0 border-0 py-1">
                <i class="ti ti-{{ $item->is_completed ? 'circle-check text-success' : 'circle text-muted' }}"></i>
                <span class="{{ $item->is_completed ? 'text-decoration-line-through text-muted' : '' }}">
                    {{ $item->title }}
                    <small class="text-muted">({{ $item->phase_label }})</small>
                </span>
            </li>
            @endforeach
        </ul>
        @endforeach
    </div>
</div>
@endif

@endsection
