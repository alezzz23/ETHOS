@extends('layouts.portal')

@section('title', $project->title)

@section('content')

{{-- Project header --}}
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                    <div>
                        <h4 class="fw-bold mb-1">{{ $project->title }}</h4>
                        <p class="text-muted mb-0">
                            <i class="ti ti-building me-1"></i>{{ $project->client?->name }}
                            @if($project->client?->industry)
                            &mdash; {{ $project->client->industry }}
                            @endif
                        </p>
                    </div>
                    <div class="text-end">
                        <span class="badge badge-status
                            @switch($project->status)
                                @case('active')      bg-success-subtle text-success @break
                                @case('completed')   bg-primary-subtle text-primary @break
                                @case('on_hold')     bg-warning-subtle text-warning @break
                                @case('cancelled')   bg-danger-subtle text-danger  @break
                                @default             bg-secondary-subtle text-secondary
                            @endswitch">
                            {{ ucfirst($project->status) }}
                        </span>
                        @if($project->estimated_budget)
                        <div class="text-muted small mt-1">
                            Presupuesto: ${{ number_format($project->estimated_budget, 0, ',', '.') }}
                        </div>
                        @endif
                    </div>
                </div>
                @if($project->description)
                <p class="mt-3 mb-0 text-muted small">{{ $project->description }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- Approved proposals --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="ti ti-file-description text-primary"></i>
                <span>Propuestas Aprobadas</span>
            </div>
            <div class="card-body">
                @forelse($proposals as $proposal)
                <div class="d-flex align-items-start justify-content-between mb-3 pb-3 border-bottom">
                    <div>
                        <div class="fw-semibold small">{{ $proposal->service?->short_name ?? 'Servicio' }}</div>
                        <div class="text-muted" style="font-size:.8rem">
                            {{ number_format($proposal->adjusted_hours, 1) }}h &mdash;
                            ${{ number_format($proposal->price_min, 0) }}–${{ number_format($proposal->price_max, 0) }}
                        </div>
                        <div class="text-muted" style="font-size:.75rem">
                            Aprobada: {{ $proposal->approved_at?->format('d M Y') ?? '—' }}
                        </div>
                    </div>
                    @if($proposal->pdf_path)
                    <a href="/admin/proposals/{{ $proposal->id }}/generate-pdf" target="_blank"
                       class="btn btn-sm btn-outline-danger" title="Ver PDF">
                        <i class="ti ti-file-type-pdf"></i>
                    </a>
                    @endif
                </div>
                @empty
                <div class="text-muted small text-center py-3">
                    <i class="ti ti-file-off d-block mb-1 fs-5"></i>
                    Sin propuestas aprobadas aún.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Checklists progress --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="ti ti-list-check text-success"></i>
                <span>Avance de Levantamiento</span>
            </div>
            <div class="card-body">
                @forelse($checklists as $checklist)
                @php
                    $total = $checklist->items->count();
                    $done  = $checklist->items->where('is_completed', true)->count();
                    $pct   = $total > 0 ? round($done / $total * 100) : 0;
                @endphp
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-semibold">{{ $checklist->title }}</span>
                        <span class="small text-muted">{{ $done }}/{{ $total }}</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 4px;">
                        <div class="progress-bar {{ $pct === 100 ? 'bg-success' : 'bg-primary' }}"
                             style="width: {{ $pct }}%;"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted">{{ $pct }}% completado</small>
                        @if($checklist->status === 'completed')
                        <small class="text-success"><i class="ti ti-check me-1"></i>Completada</small>
                        @endif
                    </div>

                    {{-- Phase breakdown --}}
                    @if($checklist->items->count() > 0)
                    <div class="mt-2">
                        @foreach(['levantamiento','diagnostico','propuesta','implementacion','seguimiento'] as $phase)
                        @php $phaseItems = $checklist->items->where('phase', $phase); @endphp
                        @if($phaseItems->count() > 0)
                        @php
                            $phaseDone  = $phaseItems->where('is_completed', true)->count();
                            $phaseTotal = $phaseItems->count();
                            $phasePct   = round($phaseDone / $phaseTotal * 100);
                        @endphp
                        <div class="d-flex align-items-center gap-2 mb-1" style="font-size:.75rem">
                            <span class="timeline-dot bg-{{ $phasePct === 100 ? 'success' : 'secondary' }}"></span>
                            <span class="text-muted" style="width:110px">
                                {{ ['levantamiento'=>'Levantamiento','diagnostico'=>'Diagnóstico','propuesta'=>'Propuesta','implementacion'=>'Implementación','seguimiento'=>'Seguimiento'][$phase] }}
                            </span>
                            <div class="progress flex-grow-1" style="height:4px">
                                <div class="progress-bar {{ $phasePct === 100 ? 'bg-success' : 'bg-info' }}"
                                    style="width:{{ $phasePct }}%"></div>
                            </div>
                            <span class="text-muted">{{ $phaseDone }}/{{ $phaseTotal }}</span>
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-muted small text-center py-3">
                    <i class="ti ti-clipboard-off d-block mb-1 fs-5"></i>
                    El levantamiento comenzará una vez aprobada la propuesta.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Contact / Help --}}
    <div class="col-12">
        <div class="card" style="background: linear-gradient(135deg, #1a3c5e 0%, #2980b9 100%); color: #fff;">
            <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h6 class="mb-1 fw-bold"><i class="ti ti-headset me-2"></i>¿Tienes preguntas?</h6>
                    <p class="mb-0" style="font-size:.85rem; opacity:.85;">
                        Contáctanos y uno de nuestros consultores te atenderá a la brevedad.
                    </p>
                </div>
                <a href="mailto:contacto@ethos.com" class="btn btn-light btn-sm">
                    <i class="ti ti-mail me-1"></i>Contactar
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
