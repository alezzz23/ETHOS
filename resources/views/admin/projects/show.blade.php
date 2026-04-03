@extends('layouts.vuexy')

@section('title', 'Ficha del Proyecto: ' . $project->title)

@section('content')
@php
    /** @var \App\Models\Project $project */
    $canEdit = auth()->user()?->can('projects.edit');
    $isLocked = $project->is_locked;
    $canEditLocked = $project->userCanEditLockedFields(auth()->user());

    $phases = [
        'capturado'    => 1,
        'en_analisis'  => 2,
        'aprobado'     => 3,
        'en_ejecucion' => 4,
        'cerrado'      => 4,
    ];
    $currentPhase = $phases[$project->status] ?? 1;

    $statusColors = [
        'capturado'    => 'secondary',
        'en_analisis'  => 'warning',
        'aprobado'     => 'success',
        'en_ejecucion' => 'primary',
        'cerrado'      => 'dark',
    ];
    $statusColor = $statusColors[$project->status] ?? 'secondary';
    $statusLabel = $project->status_label;
@endphp

{{-- Page Header --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('projects.index') }}" class="btn btn-icon btn-label-secondary rounded-pill" title="Volver a proyectos">
                    <i class="ti ti-arrow-left"></i>
                </a>
                <div>
                    <h4 class="mb-0 fw-bold">{{ $project->title }}</h4>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="badge bg-{{ $statusColor }}">{{ $statusLabel }}</span>
                        <span class="text-muted small"><i class="ti ti-building-skyscraper me-1"></i>{{ $project->client?->name ?? 'Sin cliente' }}</span>
                        @if($isLocked)
                            <span class="badge bg-label-secondary"><i class="ti ti-lock me-1"></i>Campos bloqueados</span>
                        @endif
                    </div>
                </div>
            </div>
            {{-- Progress stepper --}}
            <div class="d-flex align-items-center gap-1">
                @foreach(['1'=>'Captura','2'=>'Análisis','3'=>'Aprobación','4'=>'Ejecución'] as $n => $label)
                    <div class="d-flex align-items-center gap-1">
                        <div class="d-flex flex-column align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                 style="width:32px;height:32px;font-size:.8rem;
                                 background:{{ $currentPhase >= $n ? 'var(--bs-primary)' : '#e7e5eb' }};
                                 color:{{ $currentPhase >= $n ? '#fff' : '#9e9e9e' }}">
                                 {{ $n }}
                            </div>
                            <span style="font-size:.7rem;color:{{ $currentPhase >= $n ? 'var(--bs-primary)' : '#9e9e9e' }}">{{ $label }}</span>
                        </div>
                        @if($n < 4)
                        <div style="width:30px;height:2px;background:{{ $currentPhase > $n ? 'var(--bs-primary)' : '#e7e5eb' }};margin-bottom:14px"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Global feedback --}}
<div id="projectShowFeedback" class="alert d-none" role="alert" aria-live="polite"></div>

{{-- ══════════════════════════════════════════════════════════
     TABS
════════════════════════════════════════════════════════════ --}}
<ul class="nav nav-tabs mb-4" id="projectTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-f1" data-bs-toggle="tab" data-bs-target="#fase1" type="button" role="tab">
            <i class="ti ti-flag me-1"></i> Fase 1 · Captura
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $currentPhase < 2 ? 'disabled' : '' }}" id="tab-f2" data-bs-toggle="tab" data-bs-target="#fase2" type="button" role="tab">
            <i class="ti ti-stethoscope me-1"></i> Fase 2 · Análisis
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $currentPhase < 3 ? 'disabled' : '' }}" id="tab-f3" data-bs-toggle="tab" data-bs-target="#fase3" type="button" role="tab">
            <i class="ti ti-circle-check me-1"></i> Fase 3 · Aprobación
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $currentPhase < 4 ? 'disabled' : '' }}" id="tab-f4" data-bs-toggle="tab" data-bs-target="#fase4" type="button" role="tab">
            <i class="ti ti-settings-cog me-1"></i> Fase 4 · Ejecución
        </button>
    </li>
</ul>

<div class="tab-content" id="projectTabsContent">

{{-- ─────────────────────────────────────────────────────────
     FASE 1 — Información capturada
──────────────────────────────────────────────────────────── --}}
<div class="tab-pane fade show active" id="fase1" role="tabpanel">
    <div class="row g-4">
        {{-- Datos capturados (posiblemente bloqueados) --}}
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="ti ti-info-circle me-2"></i>Datos Capturados</h6>
                    @if($isLocked && !$canEditLocked)
                    <span class="badge bg-label-secondary"><i class="ti ti-lock me-1"></i>Solo lectura</span>
                    @elseif($canEdit)
                    <button class="btn btn-sm btn-label-primary" data-bs-toggle="collapse" data-bs-target="#editF1Form">
                        <i class="ti ti-edit me-1"></i>Editar
                    </button>
                    @endif
                </div>
                <div class="card-body">
                    {{-- Read-only display --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Cliente</label>
                            <div class="fw-semibold">{{ $project->client?->name ?? 'Sin cliente' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Tipo</label>
                            <div class="fw-semibold">{{ ucfirst($project->type ?? 'Sin definir') }}</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small mb-1">Descripción</label>
                            <div>{{ $project->description ?? '—' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small mb-1">Urgencia</label>
                            <div>
                                <span class="badge bg-{{ $project->urgency === 'alta' ? 'danger' : ($project->urgency === 'media' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($project->urgency ?? 'Sin definir') }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small mb-1">Complejidad</label>
                            <div>
                                <span class="badge bg-{{ $project->complexity === 'alta' ? 'danger' : ($project->complexity === 'media' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($project->complexity ?? 'Sin definir') }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small mb-1">Inicio tentativo</label>
                            <div>{{ $project->starts_at?->format('d/m/Y') ?? '—' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small mb-1">Presupuesto estimado</label>
                            <div>{{ $project->estimated_budget_label }}</div>
                        </div>
                    </div>

                    {{-- Edit form (collapsed, only shown if user can edit) --}}
                    @if($canEdit && (!$isLocked || $canEditLocked))
                    <div class="collapse" id="editF1Form">
                        <hr>
                        <form id="formEditF1" method="POST" action="{{ route('projects.update', $project) }}" novalidate>
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="client_id" value="{{ $project->client_id }}">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Título</label>
                                    <input type="text" name="title" class="form-control" value="{{ $project->title }}" required {{ $isLocked && !$canEditLocked ? 'readonly' : '' }}>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Descripción</label>
                                    <textarea name="description" class="form-control" rows="3" {{ $isLocked && !$canEditLocked ? 'readonly' : '' }}>{{ $project->description }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tipo</label>
                                    <select name="type" class="form-select" {{ $isLocked && !$canEditLocked ? 'disabled' : '' }}>
                                        <option value="">Sin definir</option>
                                        @foreach(['consultoria','desarrollo_web','infraestructura','soporte','mobile','otro'] as $t)
                                        <option value="{{ $t }}" @selected($project->type === $t)>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Urgencia</label>
                                    <select name="urgency" class="form-select" {{ $isLocked && !$canEditLocked ? 'disabled' : '' }}>
                                        <option value="">Sin definir</option>
                                        @foreach(['baja','media','alta'] as $v)
                                        <option value="{{ $v }}" @selected($project->urgency === $v)>{{ ucfirst($v) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Complejidad</label>
                                    <select name="complexity" class="form-select" {{ $isLocked && !$canEditLocked ? 'disabled' : '' }}>
                                        <option value="">Sin definir</option>
                                        @foreach(['baja','media','alta'] as $v)
                                        <option value="{{ $v }}" @selected($project->complexity === $v)>{{ ucfirst($v) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha de inicio</label>
                                    <input type="date" name="starts_at" class="form-control" value="{{ $project->starts_at?->format('Y-m-d') }}" {{ $isLocked && !$canEditLocked ? 'readonly' : '' }}>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Presupuesto estimado</label>
                                    <input type="number" name="estimated_budget" class="form-control" step="0.01" min="0" value="{{ $project->estimated_budget }}" {{ $isLocked && !$canEditLocked ? 'readonly' : '' }}>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Moneda</label>
                                    <select name="currency" class="form-select">
                                        @foreach(['USD','EUR','VES'] as $c)
                                        <option value="{{ $c }}" @selected($project->currency === $c)>{{ $c }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="ti ti-device-floppy me-1"></i>Guardar cambios
                                    </button>
                                    <button type="button" class="btn btn-label-secondary btn-sm ms-2" data-bs-toggle="collapse" data-bs-target="#editF1Form">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar: meta --}}
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-title"><i class="ti ti-user me-2"></i>Responsables</h6>
                    <dl class="row mb-0">
                        <dt class="col-6 text-muted small">Capturado por</dt>
                        <dd class="col-6 small">{{ $project->capturedBy?->name ?? '—' }}</dd>
                        <dt class="col-6 text-muted small">Asignado a</dt>
                        <dd class="col-6 small">{{ $project->assignedTo?->name ?? '—' }}</dd>
                        <dt class="col-6 text-muted small">Líder</dt>
                        <dd class="col-6 small">{{ $project->leader?->name ?? '—' }}</dd>
                    </dl>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title"><i class="ti ti-clock me-2"></i>Fechas de ciclo</h6>
                    <dl class="row mb-0">
                        <dt class="col-6 text-muted small">Captura</dt>
                        <dd class="col-6 small">{{ $project->created_at?->format('d/m/Y H:i') }}</dd>
                        <dt class="col-6 text-muted small">Bloqueado</dt>
                        <dd class="col-6 small">{{ $project->locked_fields_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                        <dt class="col-6 text-muted small">Aprobado</dt>
                        <dd class="col-6 small">{{ $project->approved_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                        <dt class="col-6 text-muted small">Ejecución inicio</dt>
                        <dd class="col-6 small">{{ $project->execution_started_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                        <dt class="col-6 text-muted small">Cierre</dt>
                        <dd class="col-6 small">{{ $project->closed_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ─────────────────────────────────────────────────────────
     FASE 2 — Análisis de consultora
──────────────────────────────────────────────────────────── --}}
<div class="tab-pane fade" id="fase2" role="tabpanel">
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="ti ti-stethoscope me-2"></i>Análisis Técnico</h6>
                </div>
                <div class="card-body">
                    @if($project->status === 'capturado' && $canEdit)
                    {{-- Formulario de análisis --}}
                    <form id="formAnalyze" method="POST" action="{{ route('projects.analyze', $project) }}" novalidate>
                        @csrf
                        @method('PATCH')
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Servicio <span class="text-danger">*</span></label>
                                <select name="service_id" class="form-select" required id="selectServiceId">
                                    <option value="">Seleccione el servicio</option>
                                    @foreach($services as $service)
                                    <option value="{{ $service->id }}" @selected($project->service_id === $service->id)>
                                        {{ $service->short_name }} — {{ $service->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tamaño del cliente <span class="text-danger">*</span></label>
                                <select name="client_size_calc" class="form-select" id="selectClientSize">
                                    <option value="">Seleccione tamaño</option>
                                    @foreach($sizes as $sz)
                                    <option value="{{ $sz->size_key }}">{{ ucfirst(str_replace('_',' ',$sz->size_key)) }} ({{ $sz->min_employees }}–{{ $sz->max_employees ?? '∞' }} emp.)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="button" class="btn btn-label-info w-100" id="btnCalcHours">
                                    <i class="ti ti-calculator me-1"></i>Calcular horas automáticamente
                                </button>
                            </div>
                            {{-- Breakdown panel (hidden until calc runs) --}}
                            <div class="col-12 d-none" id="hoursBreakdownPanel">
                                <div class="alert alert-info py-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong><i class="ti ti-chart-bar me-1"></i>Desglose de horas calculado</strong>
                                        <button type="button" class="btn-close btn-sm" onclick="document.getElementById('hoursBreakdownPanel').classList.add('d-none')"></button>
                                    </div>
                                    <div id="hoursBreakdownBody" class="small"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Horas estimadas (H-H) <span class="text-danger">*</span></label>
                                <input type="number" name="estimated_hours" class="form-control" step="0.5" min="1"
                                       value="{{ $project->estimated_hours }}" required id="inputEstimatedHours">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tarifa por hora <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ $project->currency }}</span>
                                    <input type="number" name="hourly_rate" class="form-control" step="0.01" min="0"
                                           value="{{ $project->hourly_rate }}" required id="inputHourlyRate">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-light py-2 mb-0">
                                    <strong>Costo estimado total:</strong>
                                    <span id="costPreview">{{ $project->estimated_hours && $project->hourly_rate ? number_format($project->estimated_hours * $project->hourly_rate, 2) : '—' }}</span>
                                    {{ $project->currency }}
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Líder del Proyecto <span class="text-danger">*</span></label>
                                <select name="leader_id" class="form-select" required>
                                    <option value="">Seleccione el líder</option>
                                    @foreach($leaders as $leader)
                                    <option value="{{ $leader->id }}" @selected($project->leader_id === $leader->id)>{{ $leader->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Consultor/a Asignada</label>
                                <select name="assigned_to" class="form-select">
                                    <option value="">Sin asignar</option>
                                    @foreach($consultors as $c)
                                    <option value="{{ $c->id }}" @selected($project->assigned_to === $c->id)>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-warning" id="btnAnalyze">
                                    <i class="ti ti-send me-1"></i>Enviar a Análisis
                                </button>
                            </div>
                        </div>
                    </form>
                    @elseif(in_array($project->status, ['en_analisis','aprobado','en_ejecucion','cerrado']))
                    {{-- Mostrar datos de análisis --}}
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Servicio</label>
                            <div class="fw-semibold">{{ $project->service?->short_name ?? '—' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small">Horas estimadas</label>
                            <div class="fw-semibold">{{ $project->estimated_hours ?? '—' }} h</div>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small">Tarifa/hora</label>
                            <div class="fw-semibold">{{ $project->hourly_rate ? number_format($project->hourly_rate, 2) . ' ' . $project->currency : '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Líder</label>
                            <div class="fw-semibold">{{ $project->leader?->name ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Consultor/a</label>
                            <div class="fw-semibold">{{ $project->assignedTo?->name ?? '—' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-light py-2 mb-0">
                                <strong>Costo estimado:</strong>
                                {{ $project->estimated_hours && $project->hourly_rate
                                    ? number_format($project->estimated_hours * $project->hourly_rate, 2) . ' ' . $project->currency
                                    : '—' }}
                            </div>
                        </div>
                        @if($project->status === 'en_analisis' && $canEdit)
                        <div class="col-12">
                            <a href="{{ route('proposals.create', ['project_id' => $project->id, 'service_id' => $project->service_id]) }}"
                               class="btn btn-primary">
                                <i class="ti ti-file-plus me-1"></i>Generar Propuesta
                            </a>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="ti ti-hourglass-empty fs-2 mb-2 d-block"></i>
                        El análisis se completará cuando la consultora asignada revise el proyecto.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            {{-- KPIs de análisis --}}
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title"><i class="ti ti-chart-bar me-2"></i>Indicadores</h6>
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <div class="fs-4 fw-bold text-primary">{{ $project->estimated_hours ?? '—' }}</div>
                                <div class="small text-muted">Horas estimadas</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <div class="fs-4 fw-bold text-warning">{{ $project->priority_score ?? '—' }}</div>
                                <div class="small text-muted">Score prioridad</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-light rounded">
                                <div class="fs-5 fw-bold text-success">
                                    {{ $project->estimated_hours && $project->hourly_rate
                                        ? number_format($project->estimated_hours * $project->hourly_rate, 2) . ' ' . $project->currency
                                        : '—' }}
                                </div>
                                <div class="small text-muted">Costo estimado total</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ─────────────────────────────────────────────────────────
     FASE 3 — Aprobación
──────────────────────────────────────────────────────────── --}}
<div class="tab-pane fade" id="fase3" role="tabpanel">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="ti ti-circle-check me-2"></i>Estado de Aprobación</h6>
                </div>
                <div class="card-body">
                    @if($project->status === 'en_analisis' && $canEdit)
                    <form id="formApprove" method="POST" action="{{ route('projects.approve', $project) }}" novalidate>
                        @csrf
                        @method('PATCH')
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Fecha límite de entrega</label>
                                <input type="date" name="ends_at" class="form-control" value="{{ $project->ends_at?->format('Y-m-d') }}">
                            </div>
                            <div class="col-12">
                                <div class="alert alert-warning py-2">
                                    <i class="ti ti-alert-triangle me-1"></i>
                                    Al aprobar, el líder <strong>{{ $project->leader?->name ?? '(sin líder)' }}</strong> recibirá notificación y se generará la lista de verificación.
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success">
                                    <i class="ti ti-thumb-up me-1"></i>Aprobar Proyecto
                                </button>
                            </div>
                        </div>
                    </form>
                    @elseif(in_array($project->status, ['aprobado','en_ejecucion','cerrado']))
                    <div class="text-center py-3">
                        <div class="avatar avatar-xl mb-3">
                            <span class="avatar-initial rounded-circle bg-success">
                                <i class="ti ti-check fs-2"></i>
                            </span>
                        </div>
                        <h5 class="text-success">Proyecto Aprobado</h5>
                        <p class="text-muted mb-1">Aprobado el: <strong>{{ $project->approved_at?->format('d/m/Y H:i') ?? '—' }}</strong></p>
                        <p class="text-muted">Líder asignado: <strong>{{ $project->leader?->name ?? '—' }}</strong></p>

                        @if($project->status === 'aprobado' && $canEdit)
                        <form method="POST" action="{{ route('projects.start-execution', $project) }}" id="formStartExecution">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-primary mt-2">
                                <i class="ti ti-player-play me-1"></i>Iniciar Ejecución
                            </button>
                        </form>
                        @endif
                    </div>
                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="ti ti-lock fs-2 mb-2 d-block"></i>
                        El proyecto debe completar el análisis antes de poder aprobarse.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Checklist / Tareas --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="ti ti-checklist me-2"></i>Lista de Verificación</h6>
                </div>
                <div class="card-body">
                    @forelse($project->checklists ?? [] as $checklist)
                        <h6 class="fw-semibold">{{ $checklist->title }}</h6>
                        <ul class="list-group list-group-flush">
                        @foreach($checklist->items ?? [] as $item)
                            <li class="list-group-item d-flex align-items-center gap-2 ps-0">
                                <span class="{{ $item->is_completed ? 'text-success' : 'text-muted' }}">
                                    <i class="ti ti-{{ $item->is_completed ? 'circle-check' : 'circle' }}"></i>
                                </span>
                                <span class="{{ $item->is_completed ? 'text-decoration-line-through text-muted' : '' }}">
                                    {{ $item->title }}
                                    @if($item->phase)<small class="text-muted ms-1">({{ $item->phase_label }})</small>@endif
                                </span>
                            </li>
                        @endforeach
                        </ul>
                    @empty
                    <div class="text-muted text-center py-3">
                        <i class="ti ti-list-check d-block fs-2 mb-1"></i>
                        La lista de verificación se generará automáticamente al aprobar el proyecto.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Propuestas vinculadas al proyecto --}}
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="ti ti-file-invoice me-2"></i>Propuestas del Proyecto</h6>
                    @if(in_array($project->status, ['en_analisis','aprobado']) && $canEdit)
                    <a href="{{ route('proposals.create', ['project_id' => $project->id, 'service_id' => $project->service_id]) }}"
                       class="btn btn-sm btn-primary">
                        <i class="ti ti-plus me-1"></i>Nueva propuesta
                    </a>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($project->proposals->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="ti ti-file-invoice d-block fs-2 mb-1"></i>
                        No hay propuestas. Créalas desde Fase 2 una vez el proyecto esté en análisis.
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Servicio</th>
                                    <th>Tamaño cliente</th>
                                    <th>Horas</th>
                                    <th>Precio estimado</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($project->proposals->sortByDesc('created_at') as $prop)
                                @php
                                    $propBadge = match($prop->status) {
                                        'draft'    => 'bg-label-secondary',
                                        'sent'     => 'bg-label-info',
                                        'approved' => 'bg-label-success',
                                        'rejected' => 'bg-label-danger',
                                        default    => 'bg-label-secondary',
                                    };
                                    $propLabel = match($prop->status) {
                                        'draft'    => 'Borrador',
                                        'sent'     => 'Enviada',
                                        'approved' => 'Aprobada',
                                        'rejected' => 'Rechazada',
                                        default    => $prop->status,
                                    };
                                @endphp
                                <tr>
                                    <td class="small text-muted">#{{ $prop->id }}</td>
                                    <td class="small">{{ $prop->service?->short_name ?? '—' }}</td>
                                    <td class="small">{{ ucfirst(str_replace('_',' ',$prop->client_size ?? '—')) }}</td>
                                    <td class="small">{{ $prop->adjusted_hours ?? $prop->total_hours }}h</td>
                                    <td class="small">{{ $prop->price_min ? number_format($prop->price_min, 0) . '–' . number_format($prop->price_max, 0) : '—' }}</td>
                                    <td><span class="badge {{ $propBadge }}">{{ $propLabel }}</span></td>
                                    <td>
                                        <a href="{{ route('proposals.index', ['project_id' => $project->id]) }}" class="btn btn-xs btn-label-primary p-1 px-2 small">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @if($prop->status === 'approved')
                                <tr class="table-success">
                                    <td colspan="7" class="small py-2 ps-4">
                                        <i class="ti ti-circle-check text-success me-1"></i>
                                        <strong>Propuesta aprobada el {{ $prop->approved_at?->format('d/m/Y') ?? '—' }}</strong>
                                        — Precio final: {{ number_format($prop->price_max ?? 0, 2) }} —
                                        Ajuste de horas: {{ $prop->adjusted_hours ?? $prop->total_hours }}h
                                        @if($prop->adjustment_reason) · {{ $prop->adjustment_reason }} @endif
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ─────────────────────────────────────────────────────────
     FASE 4 — Ejecución y seguimiento
──────────────────────────────────────────────────────────── --}}
<div class="tab-pane fade" id="fase4" role="tabpanel">
    <div class="row g-4">

        {{-- KPIs de ejecución --}}
        <div class="col-12">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body py-3">
                            <div class="fs-3 fw-bold text-primary">{{ $project->progress ?? 0 }}%</div>
                            <div class="progress mt-2" style="height:6px">
                                <div class="progress-bar" role="progressbar" style="width:{{ $project->progress ?? 0 }}%"></div>
                            </div>
                            <div class="small text-muted mt-1">Progreso general</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body py-3">
                            <div class="fs-3 fw-bold text-info">{{ $project->estimated_hours ?? '—' }}h</div>
                            <div class="small text-muted mt-2">Horas estimadas</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body py-3">
                            <div class="fs-3 fw-bold text-warning">{{ $project->actual_hours ?? 0 }}h</div>
                            <div class="small text-muted mt-2">Horas reales</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body py-3">
                            @php
                                $dev = $project->deviation_percent ?? 0;
                                $devColor = abs($dev) >= 20 ? 'danger' : (abs($dev) >= 10 ? 'warning' : 'success');
                            @endphp
                            <div class="fs-3 fw-bold text-{{ $devColor }}">{{ number_format($dev, 1) }}%</div>
                            <div class="small text-muted mt-2">Desvío</div>
                            @if(abs($dev) >= 20)
                            <span class="badge bg-danger mt-1"><i class="ti ti-alert-triangle me-1"></i>Alerta</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Registrar avance --}}
        @if($project->status === 'en_ejecucion' && $canEdit)
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="ti ti-plus me-2"></i>Registrar Avance</h6>
                </div>
                <div class="card-body">
                    <form id="formProgress" novalidate>
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Método <span class="text-danger">*</span></label>
                                <select name="method" class="form-select" required>
                                    <option value="">Seleccione</option>
                                    <option value="encuesta">Encuesta</option>
                                    <option value="entrevista">Entrevista</option>
                                    <option value="observacion">Observación</option>
                                    <option value="documental">Documental</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fase <span class="text-danger">*</span></label>
                                <select name="phase" class="form-select" required>
                                    <option value="">Seleccione</option>
                                    <option value="levantamiento">Levantamiento</option>
                                    <option value="diagnostico">Diagnóstico</option>
                                    <option value="propuesta">Propuesta</option>
                                    <option value="implementacion">Implementación</option>
                                    <option value="seguimiento">Seguimiento</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Horas trabajadas <span class="text-danger">*</span></label>
                                <input type="number" name="actual_hours" class="form-control" step="0.25" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Horas planeadas</label>
                                <input type="number" name="planned_hours" class="form-control" step="0.25" min="0">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Ítem de checklist vinculado</label>
                                <select name="checklist_item_id" class="form-select" id="selectChecklistItem">
                                    <option value="">Ninguno</option>
                                    @foreach($project->checklists as $cl)
                                        @foreach($cl->items->where('is_completed', false) as $clItem)
                                        <option value="{{ $clItem->id }}">[{{ $clItem->phase_label }}] {{ $clItem->title }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                                <div class="form-text">Si el avance llega a 100%, el ítem se marcará automáticamente como completado.</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label d-flex justify-content-between">
                                    <span>% Avance <span class="text-danger">*</span></span>
                                    <strong id="progressValueF4">0%</strong>
                                </label>
                                <input type="range" name="progress_pct" class="form-range" value="0" min="0" max="100" id="progressRangeF4" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha trabajada <span class="text-danger">*</span></label>
                                <input type="date" name="date_worked" class="form-control" value="{{ today()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Peso</label>
                                <input type="number" name="weight" class="form-control" step="0.1" min="0.1" max="10" value="1">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notas</label>
                                <textarea name="notes" class="form-control" rows="2" maxlength="1000"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100" id="btnLogProgress">
                                    <span class="spinner-border spinner-border-sm d-none" id="progressSpinner"></span>
                                    <i class="ti ti-plus"></i> Registrar Avance
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- Historial de avances --}}
        <div class="{{ $project->status === 'en_ejecucion' && $canEdit ? 'col-lg-7' : 'col-12' }}">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="ti ti-history me-2"></i>Historial de Avances</h6>
                    <div class="d-flex gap-2">
                        <a href="{{ route('projects.report', $project) }}" class="btn btn-sm btn-label-info" target="_blank">
                            <i class="ti ti-report me-1"></i>Ver reporte
                        </a>
                        @if($project->status === 'en_ejecucion' && $canEdit)
                        <form method="POST" action="{{ route('projects.close', $project) }}" id="formClose"
                              onsubmit="return confirm('¿Cerrar el proyecto? Esta acción notificará al cliente.')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-dark">
                                <i class="ti ti-lock me-1"></i>Cerrar Proyecto
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($project->progressEntries->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="ti ti-activity d-block fs-2 mb-1"></i>
                        Aún no se han registrado avances.
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Método</th>
                                    <th>Fase</th>
                                    <th>Avance</th>
                                    <th>Horas</th>
                                    <th>Registrado por</th>
                                </tr>
                            </thead>
                            <tbody id="progressEntriesBody">
                                @foreach($project->progressEntries->sortByDesc('date_worked') as $entry)
                                <tr>
                                    <td class="small">{{ \Carbon\Carbon::parse($entry->date_worked)->format('d/m/Y') }}</td>
                                    <td><span class="badge bg-label-primary">{{ ucfirst($entry->method ?? '—') }}</span></td>
                                    <td class="small text-muted">{{ ucfirst($entry->phase ?? '—') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height:6px;min-width:50px">
                                                <div class="progress-bar" style="width:{{ $entry->progress_pct }}%"></div>
                                            </div>
                                            <span class="small">{{ $entry->progress_pct }}%</span>
                                        </div>
                                    </td>
                                    <td class="small">{{ $entry->actual_hours }}h</td>
                                    <td class="small">{{ $entry->recordedBy?->name ?? '—' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

</div>{{-- end tab-content --}}
@endsection

@push('scripts')
<script>
(() => {
    const projectId    = {{ $project->id }};
    const progressRoute = `/admin/projects/${projectId}/progress`;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    const feedback = document.getElementById('projectShowFeedback');
    const showFeedback = (type, msg) => {
        feedback.className = `alert alert-${type === 'success' ? 'success' : 'danger'}`;
        feedback.textContent = msg;
        feedback.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => feedback.classList.add('d-none'), 6000);
    };

    // Cost preview (Fase 2)
    const calcCost = () => {
        const h = parseFloat(document.getElementById('inputEstimatedHours')?.value) || 0;
        const r = parseFloat(document.getElementById('inputHourlyRate')?.value) || 0;
        const el = document.getElementById('costPreview');
        if (el) el.textContent = h && r ? (h * r).toLocaleString('es-VE', {minimumFractionDigits:2,maximumFractionDigits:2}) : '—';
    };
    document.getElementById('inputEstimatedHours')?.addEventListener('input', calcCost);
    document.getElementById('inputHourlyRate')?.addEventListener('input', calcCost);

    // ─── Fase 2: Auto-calcular horas (HourCalculatorService) ─────
    const btnCalcHours = document.getElementById('btnCalcHours');
    if (btnCalcHours) {
        btnCalcHours.addEventListener('click', async () => {
            const serviceId  = document.getElementById('selectServiceId')?.value;
            const clientSize = document.getElementById('selectClientSize')?.value;
            const hourlyRate = parseFloat(document.getElementById('inputHourlyRate')?.value) || 25;

            if (!serviceId || !clientSize) {
                showFeedback('error', 'Seleccione un servicio y el tamaño del cliente para calcular.');
                return;
            }

            btnCalcHours.disabled = true;
            btnCalcHours.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Calculando…';

            try {
                const res  = await fetch(`/admin/services/${serviceId}/calculate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ client_size: clientSize, hourly_rate: hourlyRate, margin_percent: 20 }),
                });
                const data = await res.json().catch(() => ({}));

                if (!res.ok) { showFeedback('error', data.message || 'Error al calcular horas.'); return; }

                // Auto-fill hours
                const hoursEl = document.getElementById('inputEstimatedHours');
                if (hoursEl) { hoursEl.value = data.total_hours; hoursEl.dispatchEvent(new Event('input')); }

                // Render breakdown panel
                const panel = document.getElementById('hoursBreakdownPanel');
                const body  = document.getElementById('hoursBreakdownBody');
                if (panel && body) {
                    let html = `<p class="mb-1"><strong>Total: ${data.total_hours}h</strong> · Personas: ${data.target_persons}</p><ul class="mb-0 ps-3">`;
                    (data.breakdown || []).forEach(p => {
                        html += `<li><strong>${p.process_label || p.process}</strong>: ${p.hours}h`;
                        if (p.methods?.length) {
                            html += '<ul class="ps-3">';
                            p.methods.forEach(m => { html += `<li>${m.method_label || m.method}: ${m.subtotal_hours}h (${m.standard_hours}h×${m.persons} pers.)</li>`; });
                            html += '</ul>';
                        }
                        html += '</li>';
                    });
                    html += '</ul>';
                    body.innerHTML = html;
                    panel.classList.remove('d-none');
                }

                showFeedback('success', `Horas calculadas: ${data.total_hours}h para ${data.target_persons} personas.`);
            } catch { showFeedback('error', 'Error de conexión al calcular horas.'); }
            finally {
                btnCalcHours.disabled = false;
                btnCalcHours.innerHTML = '<i class="ti ti-calculator me-1"></i>Calcular horas automáticamente';
            }
        });
    }

    // Fase 2 form via AJAX
    const formAnalyze = document.getElementById('formAnalyze');
    if (formAnalyze) {
        formAnalyze.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!formAnalyze.checkValidity()) { formAnalyze.classList.add('was-validated'); return; }
            const btn = document.getElementById('btnAnalyze');
            btn.disabled = true;
            try {
                const res  = await fetch(formAnalyze.action, { method: 'POST', body: new FormData(formAnalyze), headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
                const data = await res.json().catch(() => ({}));
                if (!res.ok) { showFeedback('error', data.message || 'Error al guardar el análisis.'); btn.disabled = false; return; }
                showFeedback('success', data.message || 'Análisis guardado.');
                setTimeout(() => location.reload(), 1200);
            } catch { showFeedback('error', 'Error de conexión.'); btn.disabled = false; }
        });
    }

    // Fase 3 approve via AJAX
    const formApprove = document.getElementById('formApprove');
    if (formApprove) {
        formApprove.addEventListener('submit', async (e) => {
            e.preventDefault();
            const res  = await fetch(formApprove.action, { method: 'POST', body: new FormData(formApprove), headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) { showFeedback('error', data.message || 'Error al aprobar.'); return; }
            showFeedback('success', data.message || 'Proyecto aprobado.');
            setTimeout(() => location.reload(), 1200);
        });
    }

    // Fase 3 start execution via AJAX
    const formStartExecution = document.getElementById('formStartExecution');
    if (formStartExecution) {
        formStartExecution.addEventListener('submit', async (e) => {
            e.preventDefault();
            const res  = await fetch(formStartExecution.action, { method: 'POST', body: new FormData(formStartExecution), headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) { showFeedback('error', data.message || 'Error.'); return; }
            showFeedback('success', data.message || 'Ejecución iniciada.');
            setTimeout(() => location.reload(), 1200);
        });
    }

    // Fase 4 progress range slider
    const progressRangeF4 = document.getElementById('progressRangeF4');
    const progressValueF4 = document.getElementById('progressValueF4');
    if (progressRangeF4 && progressValueF4) {
        progressRangeF4.addEventListener('input', () => { progressValueF4.textContent = progressRangeF4.value + '%'; });
    }

    // Fase 4 log progress via AJAX
    const formProgress = document.getElementById('formProgress');
    if (formProgress) {
        formProgress.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!formProgress.checkValidity()) { formProgress.classList.add('was-validated'); return; }
            const spinner = document.getElementById('progressSpinner');
            const btn     = document.getElementById('btnLogProgress');
            btn.disabled  = true;
            spinner.classList.remove('d-none');

            try {
                const fd  = new FormData(formProgress);
                const res = await fetch(progressRoute, { method: 'POST', body: fd, headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
                const data = await res.json().catch(() => ({}));

                if (!res.ok) { showFeedback('error', data.message || 'Error al registrar avance.'); return; }
                showFeedback('success', `Avance registrado. Horas reales: ${data.actual_hours}h — Desvío: ${data.deviation_percent?.toFixed(1) ?? '—'}%`);
                formProgress.reset();
                formProgress.classList.remove('was-validated');
                if (progressValueF4) progressValueF4.textContent = '0%';
                setTimeout(() => location.reload(), 1500);
            } catch { showFeedback('error', 'Error de conexión.'); }
            finally { btn.disabled = false; spinner.classList.add('d-none'); }
        });
    }

    // Fase 1 edit form via AJAX
    const formEditF1 = document.getElementById('formEditF1');
    if (formEditF1) {
        formEditF1.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!formEditF1.checkValidity()) { formEditF1.classList.add('was-validated'); return; }
            const res  = await fetch(formEditF1.action, { method: 'POST', body: new FormData(formEditF1), headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) { showFeedback('error', data.message || 'Error al guardar.'); return; }
            showFeedback('success', data.message || 'Cambios guardados.');
            setTimeout(() => location.reload(), 1200);
        });
    }
})();
</script>
@endpush
