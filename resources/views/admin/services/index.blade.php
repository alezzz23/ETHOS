@extends('layouts.vuexy')

@section('title', 'Servicios')

@section('content')
@php
    $canCreate     = auth()->user()?->can('services.create');
    $canEdit       = auth()->user()?->can('services.edit');
    $canDeactivate = auth()->user()?->can('services.deactivate');
@endphp

<div x-data="serviceForm()" x-init="init()">
<div class="row g-4">
    {{-- Header + filters --}}
    <div class="col-12">
        <div class="card ethos-crm-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0 d-flex align-items-center gap-2">
                    <i class="ti ti-tools"></i>
                    <span>Catálogo de Servicios</span>
                </h5>
                @if($canCreate)
                <button type="button" class="btn btn-primary ethos-create-btn"
                    data-bs-toggle="modal" data-bs-target="#serviceFormModal"
                    @click="openCreate()">
                    <i class="ti ti-plus"></i>
                    <span>Nuevo Servicio</span>
                </button>
                @endif
            </div>

            {{-- Filter bar --}}
            <div class="card-body pb-0">
                <form method="GET" action="{{ route('services.index') }}"
                      class="d-flex flex-wrap gap-2 align-items-end">
                    <div>
                        <label class="form-label mb-1 small">Área funcional</label>
                        <select name="area" class="form-select form-select-sm" style="min-width:160px">
                            <option value="">Todas las áreas</option>
                            @foreach($functionalAreas as $area)
                                <option value="{{ $area }}" @selected(request('area') === $area)>{{ $area }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label mb-1 small">Tipo de cliente</label>
                        <select name="type" class="form-select form-select-sm" style="min-width:160px">
                            <option value="">Todos los tipos</option>
                            @foreach($clientTypes as $key => $label)
                                <option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label mb-1 small">Estado</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="active"   @selected(request('status') === 'active')>Activo</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Inactivo</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        <i class="ti ti-filter"></i> Filtrar
                    </button>
                    @if(request()->hasAny(['area','type','status']))
                    <a href="{{ route('services.index') }}" class="btn btn-sm btn-label-secondary">
                        <i class="ti ti-x"></i> Limpiar
                    </a>
                    @endif
                </form>
            </div>

            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div id="servicesFeedback" class="alert d-none ethos-ajax-alert" role="alert" aria-live="polite"></div>

                <div class="table-responsive ethos-table-shell">
                    <table class="table table-hover align-middle ethos-data-table" id="servicesTable">
                        <thead>
                            <tr>
                                <th>Servicio</th>
                                <th>Áreas funcionales</th>
                                <th>Tipos de cliente</th>
                                <th>Docs / Requisitos</th>
                                <th>Versión</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="servicesTableBody">
                            @forelse($services as $service)
                            <tr data-service-id="{{ $service->id }}">
                                <td>
                                    <div class="ethos-primary-cell">
                                        <span class="ethos-cell-avatar"><i class="ti ti-briefcase"></i></span>
                                        <div>
                                            <span class="ethos-cell-text fw-semibold">{{ $service->short_name }}</span>
                                            <div class="text-muted small" style="max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                                {{ Str::limit($service->description, 80) }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($service->functional_areas ?? [] as $area)
                                        <span class="badge bg-label-info">{{ $area }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($service->client_types ?? [] as $type)
                                        <span class="badge bg-label-warning">{{ $clientTypes[$type] ?? $type }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-label-secondary me-1" title="Documentos">
                                        <i class="ti ti-file-text"></i> {{ $service->documents_count }}
                                    </span>
                                    <span class="badge bg-label-secondary" title="Requisitos">
                                        <i class="ti ti-checklist"></i> {{ $service->requirements_count }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-label-secondary">v{{ $service->version }}</span>
                                    <div class="text-muted" style="font-size:0.72rem">
                                        {{ $service->updated_at->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td>
                                    @if($service->status === 'active')
                                    <span class="badge bg-success">Activo</span>
                                    @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button"
                                        class="btn btn-sm btn-icon btn-text-secondary rounded-pill js-view-service"
                                        data-bs-toggle="modal" data-bs-target="#viewServiceModal"
                                        title="Ver detalle"
                                        data-service-id="{{ $service->id }}">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                    @if($canEdit)
                                    <button type="button"
                                        class="btn btn-sm btn-icon btn-text-secondary rounded-pill js-edit-service"
                                        data-bs-toggle="modal" data-bs-target="#serviceFormModal"
                                        title="Editar"
                                        data-service='{{ json_encode(["id"=>$service->id,"short_name"=>$service->short_name,"description"=>$service->description,"functional_areas"=>$service->functional_areas,"client_types"=>$service->client_types,"documents"=>$service->documents->map(fn($d)=>["name"=>$d->name,"type"=>$d->type,"description"=>$d->description])->values(),"requirements"=>$service->requirements->map(fn($r)=>["description"=>$r->description])->values(),"processes"=>$service->processes->map(fn($p)=>["name"=>$p->name,"methods"=>$p->methods->map(fn($m)=>["method"=>$m->method,"standard_hours"=>$m->standard_hours])->values()])->values()]) }}'>
                                        <i class="ti ti-pencil"></i>
                                    </button>
                                    @endif
                                    @if($canDeactivate)
                                    <button type="button"
                                        class="btn btn-sm btn-icon btn-text-{{ $service->status === 'active' ? 'danger' : 'success' }} rounded-pill js-toggle-service"
                                        title="{{ $service->status === 'active' ? 'Desactivar' : 'Activar' }}"
                                        data-service-id="{{ $service->id }}"
                                        data-current-status="{{ $service->status }}">
                                        <i class="ti ti-{{ $service->status === 'active' ? 'toggle-right' : 'toggle-left' }}"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="ti ti-tools" style="font-size:2rem;opacity:.3"></i>
                                    <div class="mt-2">No se encontraron servicios.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $services->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

{{-- ─── Detalle Modal ──────────────────────────────────────────────── --}}
<div class="modal fade" id="viewServiceModal" tabindex="-1" aria-labelledby="viewServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content vsm-content">

            {{-- Hero header --}}
            <div class="vsm-hero">
                <div class="vsm-hero-icon"><i class="ti ti-briefcase"></i></div>
                <div class="vsm-hero-info">
                    <h5 class="vsm-hero-title mb-0" id="viewServiceName">Detalle del Servicio</h5>
                    <div class="vsm-hero-meta" id="viewServiceMeta"></div>
                </div>
                <button type="button" class="btn-close vsm-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body vsm-body p-0" id="viewServiceBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                    <div class="text-muted small mt-2">Cargando...</div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ─── Create / Edit Modal ────────────────────────────────────────── --}}
<div class="modal fade" id="serviceFormModal" tabindex="-1" aria-labelledby="serviceFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content sfm-content">

            {{-- Header --}}
            <div class="modal-header sfm-header border-0 pb-0">
                <div class="sfm-header-icon">
                    <i class="ti ti-tools"></i>
                </div>
                <div>
                    <h5 class="modal-title mb-0" id="serviceFormModalLabel">
                        <span x-text="editingId ? 'Editar Servicio' : 'Nuevo Servicio'"></span>
                    </h5>
                    <p class="text-muted small mb-0" x-text="editingId ? 'Modifica los detalles de este servicio' : 'Completa la información para crear el servicio'"></p>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body sfm-body pt-3">
                <div id="serviceFormFeedback" class="alert d-none" role="alert"></div>

                {{-- Basic fields --}}
                <div class="sfm-section">
                    <div class="sfm-section-title">
                        <i class="ti ti-file-description"></i>
                        Información general
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label sfm-label">Nombre corto <span class="text-danger">*</span></label>
                            <input type="text" class="form-control sfm-input" x-model="form.short_name"
                                   placeholder="Ej: Auditoría Fiscal" maxlength="120">
                            <div class="invalid-feedback" x-show="errors.short_name" x-text="errors.short_name"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label sfm-label">Descripción extendida <span class="text-danger">*</span></label>
                            <textarea class="form-control sfm-input" x-model="form.description" rows="4"
                                      placeholder="Describe el alcance, metodología y valor del servicio..." maxlength="5000"></textarea>
                            <div class="form-text text-end" x-text="(form.description?.length || 0) + '/5000'"></div>
                            <div class="invalid-feedback" x-show="errors.description" x-text="errors.description"></div>
                        </div>
                    </div>
                </div>

                {{-- Functional areas --}}
                <div class="sfm-section">
                    <div class="sfm-section-title">
                        <i class="ti ti-layout-grid"></i>
                        Áreas funcionales
                        <span class="sfm-count-badge" x-text="form.functional_areas.length > 0 ? form.functional_areas.length + ' seleccionadas' : ''"></span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($functionalAreas as $area)
                        <label class="sfm-chip-label sfm-chip-info">
                            <input type="checkbox" class="sfm-chip-input"
                                   :value="'{{ $area }}'"
                                   x-model="form.functional_areas">
                            <span class="sfm-chip-pill"><i class="ti ti-check sfm-chip-check"></i>{{ $area }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Client types --}}
                <div class="sfm-section">
                    <div class="sfm-section-title">
                        <i class="ti ti-building"></i>
                        Tipos de cliente
                        <span class="sfm-count-badge" x-text="form.client_types.length > 0 ? form.client_types.length + ' seleccionados' : ''"></span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($clientTypes as $key => $label)
                        <label class="sfm-chip-label sfm-chip-warning">
                            <input type="checkbox" class="sfm-chip-input"
                                   :value="'{{ $key }}'"
                                   x-model="form.client_types">
                            <span class="sfm-chip-pill"><i class="ti ti-check sfm-chip-check"></i>{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Documents --}}
                <div class="sfm-section">
                    <div class="sfm-section-title d-flex justify-content-between align-items-center">
                        <span><i class="ti ti-paperclip"></i> Documentos de respaldo entregados</span>
                        <button type="button" class="btn btn-sm sfm-add-btn"
                                @click="form.documents.push({name:'',type:'otro',description:''})">
                            <i class="ti ti-plus"></i> Agregar
                        </button>
                    </div>
                    <template x-if="form.documents.length === 0">
                        <div class="sfm-empty-state">
                            <i class="ti ti-file-off"></i>
                            <span>Sin documentos. Agrega uno con el botón.</span>
                        </div>
                    </template>
                    <template x-for="(doc, i) in form.documents" :key="i">
                        <div class="sfm-item-card mb-2">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label small mb-1">Nombre</label>
                                    <input type="text" class="form-control form-control-sm"
                                           x-model="doc.name" placeholder="Manual de funciones">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small mb-1">Tipo</label>
                                    <select class="form-select form-select-sm" x-model="doc.type">
                                        @foreach($documentTypes as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small mb-1">Descripción</label>
                                    <input type="text" class="form-control form-control-sm"
                                           x-model="doc.description" placeholder="Detalle opcional">
                                </div>
                                <div class="col-md-1 d-flex align-items-end justify-content-center">
                                    <button type="button" class="btn btn-sm btn-icon btn-text-danger"
                                            @click="form.documents.splice(i,1)" title="Eliminar">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Requirements --}}
                <div class="sfm-section">
                    <div class="sfm-section-title d-flex justify-content-between align-items-center">
                        <span><i class="ti ti-checklist"></i> Checklist de requisitos del cliente</span>
                        <button type="button" class="btn btn-sm sfm-add-btn"
                                @click="form.requirements.push({description:''})">
                            <i class="ti ti-plus"></i> Agregar
                        </button>
                    </div>
                    <template x-if="form.requirements.length === 0">
                        <div class="sfm-empty-state">
                            <i class="ti ti-list-check"></i>
                            <span>Sin requisitos. Agrega uno con el botón.</span>
                        </div>
                    </template>
                    <template x-for="(req, i) in form.requirements" :key="i">
                        <div class="input-group sfm-req-group mb-2">
                            <span class="input-group-text sfm-req-icon"><i class="ti ti-check"></i></span>
                            <input type="text" class="form-control form-control-sm"
                                   x-model="req.description"
                                   placeholder="Ej: Acceso a estados financieros del último año">
                            <button type="button" class="btn btn-outline-danger btn-sm"
                                    @click="form.requirements.splice(i,1)">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Processes & Methods --}}
                <div class="sfm-section sfm-section-last">
                    <div class="sfm-section-title d-flex justify-content-between align-items-center">
                        <span><i class="ti ti-git-branch"></i> Procesos y métodos <small class="sfm-count-badge" x-text="form.processes.length > 0 ? form.processes.length + ' proceso(s)' : ''"></small></span>
                        <button type="button" class="btn btn-sm sfm-add-btn"
                                @click="form.processes.push({name:'levantamiento', methods:[{method:'entrevista', standard_hours:1}]})">
                            <i class="ti ti-plus"></i> Agregar proceso
                        </button>
                    </div>
                    <p class="text-muted small mb-2">Define los procesos que componen este servicio y las horas estándar por persona para calcular propuestas automáticamente.</p>
                    <template x-if="form.processes.length === 0">
                        <div class="sfm-empty-state">
                            <i class="ti ti-git-merge"></i>
                            <span>Sin procesos. El calculador de horas retornará 0. Agrega al menos uno.</span>
                        </div>
                    </template>
                    <template x-for="(proc, pi) in form.processes" :key="pi">
                        <div class="sfm-process-card mb-3">
                            <div class="sfm-process-header">
                                <div class="sfm-process-icon"><i class="ti ti-git-branch"></i></div>
                                <select class="form-select form-select-sm sfm-process-select" x-model="proc.name">
                                    <option value="levantamiento">Levantamiento</option>
                                    <option value="diagnostico">Diagnóstico</option>
                                    <option value="propuesta">Propuesta</option>
                                    <option value="implementacion">Implementación</option>
                                    <option value="seguimiento">Seguimiento</option>
                                </select>
                                <button type="button" class="btn btn-sm btn-icon btn-text-danger ms-auto"
                                        @click="form.processes.splice(pi,1)" title="Eliminar proceso">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                            <div class="sfm-methods-list">
                                <template x-for="(m, mi) in proc.methods" :key="mi">
                                    <div class="sfm-method-row">
                                        <select class="form-select form-select-sm sfm-method-select" x-model="m.method">
                                            <option value="encuesta">Encuesta</option>
                                            <option value="entrevista">Entrevista</option>
                                            <option value="observacion">Observación</option>
                                            <option value="documental">Documental</option>
                                        </select>
                                        <div class="sfm-hours-input-wrap">
                                            <input type="number" class="form-control form-control-sm" x-model="m.standard_hours"
                                                   min="0.1" max="999" step="0.5" style="width:80px">
                                            <span class="sfm-hours-label">h/persona</span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-icon btn-text-danger"
                                                @click="proc.methods.splice(mi,1)" title="Eliminar método">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                </template>
                                <button type="button" class="sfm-add-method-btn"
                                        @click="proc.methods.push({method:'entrevista', standard_hours:1})">
                                    <i class="ti ti-plus"></i> Método
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

            </div>

            <div class="modal-footer sfm-footer border-0">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4" @click="save()" :disabled="saving">
                    <span x-show="saving" class="spinner-border spinner-border-sm me-1"></span>
                    <i x-show="!saving" class="ti ti-device-floppy me-1"></i>
                    <span x-text="saving ? 'Guardando...' : (editingId ? 'Actualizar servicio' : 'Crear Servicio')"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* ═══════════════════════════════════════════
   Service Form Modal — Dark/Light mode polish
   ═══════════════════════════════════════════ */

/* Modal content base */
#serviceFormModal .sfm-content {
    border: 1px solid var(--bs-border-color);
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,.25);
}

/* Force dark mode colours on the modal itself */
.dark-style #serviceFormModal .sfm-content,
.dark-style #serviceFormModal .sfm-header,
.dark-style #serviceFormModal .sfm-body,
.dark-style #serviceFormModal .sfm-footer,
.dark-style #serviceFormModal .modal-body {
    background-color: #2b2c40 !important;
    color: #d0d4e4;
}
.dark-style #serviceFormModal .sfm-section {
    border-bottom-color: rgba(255,255,255,.08);
}
.dark-style #serviceFormModal .sfm-footer {
    border-top-color: rgba(255,255,255,.08) !important;
}
.dark-style #serviceFormModal .sfm-header {
    border-bottom-color: rgba(255,255,255,.08) !important;
}
.dark-style #serviceFormModal .sfm-input,
.dark-style #serviceFormModal .form-control,
.dark-style #serviceFormModal .form-select,
.dark-style #serviceFormModal .input-group-text {
    background-color: #1e1e2d !important;
    color: #d0d4e4 !important;
    border-color: rgba(255,255,255,.12) !important;
}
.dark-style #serviceFormModal .sfm-input::placeholder,
.dark-style #serviceFormModal .form-control::placeholder {
    color: rgba(208,212,228,.4) !important;
}
.dark-style #serviceFormModal .sfm-item-card {
    background: #1e1e2d;
    border-color: rgba(255,255,255,.1);
}
.dark-style #serviceFormModal .sfm-empty-state {
    background: rgba(255,255,255,.04);
    border-color: rgba(255,255,255,.12);
    color: rgba(208,212,228,.6);
}
.dark-style #serviceFormModal .sfm-section-title {
    color: #a8b1cc;
}
.dark-style #serviceFormModal .sfm-label {
    color: #c8cee4;
}
.dark-style #serviceFormModal .sfm-count-badge {
    background: rgba(105,108,255,.2);
    color: #a8b1ff;
}

/* ══════════════════════════════════════════════════════════════
   View Service Modal (vsm)
   ══════════════════════════════════════════════════════════════ */

/* Content wrapper */
#viewServiceModal .vsm-content {
    border-radius: .75rem;
    overflow: hidden;
    border: 1px solid var(--bs-border-color);
    box-shadow: 0 20px 60px rgba(0,0,0,.2);
}

/* Hero header */
#viewServiceModal .vsm-hero {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.25rem 1.5rem 1.125rem;
    background: linear-gradient(135deg, rgba(var(--bs-primary-rgb),.12) 0%, rgba(var(--bs-primary-rgb),.04) 100%);
    border-bottom: 1px solid var(--bs-border-color);
    position: relative;
}
#viewServiceModal .vsm-hero-icon {
    width: 3rem;
    height: 3rem;
    border-radius: .625rem;
    background: rgba(var(--bs-primary-rgb),.15);
    color: var(--bs-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
}
#viewServiceModal .vsm-hero-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--bs-body-color);
    line-height: 1.3;
}
#viewServiceModal .vsm-hero-meta {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-top: .35rem;
    flex-wrap: wrap;
}
#viewServiceModal .vsm-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
}

/* Status & version pills in hero */
#viewServiceModal .vsm-status-badge {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    font-size: .72rem;
    font-weight: 600;
    padding: .2rem .6rem;
    border-radius: 999px;
}
#viewServiceModal .vsm-status-active  { background: rgba(113,221,55,.15); color: #71dd37; }
#viewServiceModal .vsm-status-inactive { background: rgba(168,177,204,.15); color: #a8b1cc; }
#viewServiceModal .vsm-version-pill {
    display: inline-block;
    font-size: .7rem;
    font-weight: 700;
    padding: .2rem .55rem;
    border-radius: 999px;
    background: rgba(var(--bs-primary-rgb),.12);
    color: var(--bs-primary);
    letter-spacing: .04em;
}

/* Body */
#viewServiceModal .vsm-body { background: var(--bs-body-bg); }

/* Description */
#viewServiceModal .vsm-description {
    padding: 1.125rem 1.5rem;
    font-size: .9rem;
    color: var(--bs-secondary-color);
    border-bottom: 1px solid var(--bs-border-color);
    line-height: 1.7;
}

/* Grid sections (2 cols) */
#viewServiceModal .vsm-grid-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    border-bottom: 1px solid var(--bs-border-color);
}
#viewServiceModal .vsm-subsection {
    padding: 1.125rem 1.5rem;
}
#viewServiceModal .vsm-grid-section .vsm-subsection:first-child {
    border-right: 1px solid var(--bs-border-color);
}
#viewServiceModal .vsm-subsection-title {
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--bs-secondary-color);
    margin-bottom: .75rem;
    display: flex;
    align-items: center;
    gap: .35rem;
}
#viewServiceModal .vsm-subsection-title i {
    color: var(--bs-primary);
    font-size: .85rem;
}

/* Chips */
#viewServiceModal .vsm-chips { display: flex; flex-wrap: wrap; gap: .4rem; }
#viewServiceModal .vsm-chip {
    display: inline-block;
    font-size: .75rem;
    font-weight: 500;
    padding: .28rem .7rem;
    border-radius: 999px;
    border: 1.5px solid;
}
#viewServiceModal .vsm-chip-info    { background: rgba(var(--bs-info-rgb),.1);    color: var(--bs-info);    border-color: rgba(var(--bs-info-rgb),.3); }
#viewServiceModal .vsm-chip-warning { background: rgba(var(--bs-warning-rgb),.1); color: var(--bs-warning); border-color: rgba(var(--bs-warning-rgb),.3); }

/* Document items */
#viewServiceModal .vsm-doc-item {
    display: flex;
    gap: .75rem;
    padding: .625rem 0;
    border-bottom: 1px solid var(--bs-border-color);
}
#viewServiceModal .vsm-doc-item:last-child { border-bottom: none; }
#viewServiceModal .vsm-doc-icon {
    width: 2rem;
    height: 2rem;
    border-radius: .375rem;
    background: rgba(var(--bs-primary-rgb),.1);
    color: var(--bs-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: .9rem;
}
#viewServiceModal .vsm-doc-name {
    font-size: .82rem;
    font-weight: 600;
    color: var(--bs-body-color);
}
#viewServiceModal .vsm-doc-desc {
    font-size: .75rem;
    color: var(--bs-secondary-color);
    margin-top: .1rem;
}
#viewServiceModal .vsm-type-pill {
    display: inline-block;
    font-size: .65rem;
    font-weight: 600;
    padding: .1rem .45rem;
    border-radius: .25rem;
    background: rgba(var(--bs-secondary-rgb),.15);
    color: var(--bs-secondary-color);
    text-transform: uppercase;
    letter-spacing: .04em;
    vertical-align: middle;
    margin-left: .3rem;
}

/* Requirement items */
#viewServiceModal .vsm-req-item {
    display: flex;
    align-items: flex-start;
    gap: .6rem;
    padding: .5rem 0;
    border-bottom: 1px solid var(--bs-border-color);
    font-size: .82rem;
    color: var(--bs-body-color);
}
#viewServiceModal .vsm-req-item:last-child { border-bottom: none; }
#viewServiceModal .vsm-req-check {
    width: 1.25rem;
    height: 1.25rem;
    border-radius: 50%;
    background: rgba(113,221,55,.15);
    color: #71dd37;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .7rem;
    flex-shrink: 0;
    margin-top: .05rem;
}

/* Empty states */
#viewServiceModal .vsm-empty {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: .75rem 1rem;
    border-radius: .5rem;
    background: rgba(var(--bs-secondary-rgb),.06);
    border: 1px dashed var(--bs-border-color);
    color: var(--bs-secondary-color);
    font-size: .8rem;
}

/* Audit log section */
#viewServiceModal .vsm-log-section {
    padding: 1.125rem 1.5rem;
    background: var(--bs-tertiary-bg, rgba(var(--bs-secondary-rgb),.03));
}
#viewServiceModal .vsm-log-item {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .6rem 0;
    font-size: .8rem;
    border-bottom: 1px solid var(--bs-border-color);
    color: var(--bs-body-color);
}
#viewServiceModal .vsm-log-item:last-child { border-bottom: none; }
#viewServiceModal .vsm-log-icon {
    width: 1.75rem;
    height: 1.75rem;
    border-radius: .375rem;
    background: rgba(var(--bs-primary-rgb),.1);
    color: var(--bs-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .8rem;
    flex-shrink: 0;
}
#viewServiceModal .vsm-log-label { font-weight: 600; }
#viewServiceModal .vsm-log-by    { color: var(--bs-secondary-color); flex: 1; }
#viewServiceModal .vsm-log-date  { font-size: .72rem; color: var(--bs-secondary-color); white-space: nowrap; }

/* Dark mode overrides */
.dark-style #viewServiceModal .vsm-content {
    background: #2b2c40;
    border-color: rgba(255,255,255,.08);
}
.dark-style #viewServiceModal .vsm-hero { background: linear-gradient(135deg, rgba(105,108,255,.15) 0%, rgba(105,108,255,.04) 100%); border-bottom-color: rgba(255,255,255,.08); }
.dark-style #viewServiceModal .vsm-hero-title { color: #d0d4e4; }
.dark-style #viewServiceModal .vsm-description,
.dark-style #viewServiceModal .vsm-body { background: #2b2c40; color: #a8b1cc; }
.dark-style #viewServiceModal .vsm-description { border-bottom-color: rgba(255,255,255,.08); }
.dark-style #viewServiceModal .vsm-grid-section { border-bottom-color: rgba(255,255,255,.08); }
.dark-style #viewServiceModal .vsm-grid-section .vsm-subsection:first-child { border-right-color: rgba(255,255,255,.08); }
.dark-style #viewServiceModal .vsm-doc-item,
.dark-style #viewServiceModal .vsm-req-item,
.dark-style #viewServiceModal .vsm-log-item { border-bottom-color: rgba(255,255,255,.06); }
.dark-style #viewServiceModal .vsm-doc-name { color: #c8cee4; }
.dark-style #viewServiceModal .vsm-req-item { color: #c8cee4; }
.dark-style #viewServiceModal .vsm-log-item { color: #c8cee4; }
.dark-style #viewServiceModal .vsm-empty {
    background: rgba(255,255,255,.04);
    border-color: rgba(255,255,255,.1);
    color: rgba(208,212,228,.5);
}
.dark-style #viewServiceModal .vsm-log-section { background: rgba(0,0,0,.15); }
.dark-style #viewServiceModal .vsm-type-pill {
    background: rgba(255,255,255,.1);
    color: #a8b1cc;
}

/* ── Services table: ambos temas ─────────────────────────────── */

/* Docs / Requisitos badges & versión badge — light mode */
#servicesTable .badge.bg-label-secondary {
    background: #e4e6ef !important;
    color: #555872 !important;
    border: 1px solid #c8cae0;
}

/* Version date text — light mode */
#servicesTable .text-muted {
    color: #696cae !important;
    font-size: .72rem;
}

/* ── Services table: dark-mode fixes ─────────────────────────── */

/* Docs / Requisitos badges & versión badge */
.dark-style #servicesTable .badge.bg-label-secondary {
    background: rgba(255,255,255,.1) !important;
    color: #c8cee4 !important;
    border-color: rgba(255,255,255,.15) !important;
}

/* Version date text */
.dark-style #servicesTable .text-muted {
    color: #8e98b8 !important;
}

/* Action icon buttons */
.dark-style #servicesTable .btn-text-secondary {
    color: #a8b1cc !important;
}
.dark-style #servicesTable .btn-text-secondary:hover {
    background: rgba(255,255,255,.08) !important;
    color: #d0d4e4 !important;
}
.dark-style #servicesTable .btn-text-danger {
    color: #ff6b78 !important;
}
.dark-style #servicesTable .btn-text-success {
    color: #71dd37 !important;
}

/* Header */
#serviceFormModal .sfm-header {
    padding: 1.25rem 1.5rem 1rem;
    background: var(--bs-body-bg);
    border-bottom: 1px solid var(--bs-border-color) !important;
    display: flex;
    align-items: center;
    gap: 1rem;
}
#serviceFormModal .sfm-header-icon {
    width: 2.75rem;
    height: 2.75rem;
    border-radius: 0.625rem;
    background: rgba(var(--bs-primary-rgb), 0.12);
    color: var(--bs-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

/* Body */
#serviceFormModal .sfm-body {
    padding: 0;
    background: var(--bs-body-bg);
}

/* Sections */
#serviceFormModal .sfm-section {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--bs-border-color);
}
#serviceFormModal .sfm-section-last {
    border-bottom: none;
}
#serviceFormModal .sfm-section-title {
    font-size: .75rem;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: var(--bs-secondary-color);
    margin-bottom: .875rem;
    display: flex;
    align-items: center;
    gap: .4rem;
}
#serviceFormModal .sfm-section-title i {
    color: var(--bs-primary);
    font-size: .9rem;
}

/* Section counter badge */
#serviceFormModal .sfm-count-badge {
    display: inline-block;
    background: rgba(var(--bs-primary-rgb), .12);
    color: var(--bs-primary);
    font-size: .65rem;
    padding: .15rem .5rem;
    border-radius: 999px;
    font-weight: 600;
    letter-spacing: .04em;
    text-transform: none;
    margin-left: .25rem;
}

/* Form labels */
#serviceFormModal .sfm-label {
    font-size: .8125rem;
    font-weight: 600;
    color: var(--bs-body-color);
    margin-bottom: .375rem;
}

/* Form inputs — inherit theme vars automatically */
#serviceFormModal .sfm-input {
    background-color: var(--bs-body-bg);
    color: var(--bs-body-color);
    border-color: var(--bs-border-color);
    transition: border-color .15s, box-shadow .15s;
}
#serviceFormModal .sfm-input:focus {
    box-shadow: 0 0 0 .25rem rgba(var(--bs-primary-rgb), .2);
    border-color: var(--bs-primary);
}

/* Toggle chip labels */
#serviceFormModal .sfm-chip-label {
    position: relative;
    cursor: pointer;
    user-select: none;
}
#serviceFormModal .sfm-chip-input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
    pointer-events: none;
}
#serviceFormModal .sfm-chip-pill {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    padding: .35rem .75rem;
    border-radius: 999px;
    font-size: .78rem;
    font-weight: 500;
    border: 1.5px solid transparent;
    transition: all .15s ease;
    line-height: 1.3;
}
/* Info chips (áreas) */
#serviceFormModal .sfm-chip-info .sfm-chip-pill {
    border-color: rgba(var(--bs-info-rgb), .35);
    background: rgba(var(--bs-info-rgb), .08);
    color: var(--bs-info);
}
#serviceFormModal .sfm-chip-info:hover .sfm-chip-pill {
    background: rgba(var(--bs-info-rgb), .16);
    border-color: var(--bs-info);
}
#serviceFormModal .sfm-chip-info .sfm-chip-input:checked + .sfm-chip-pill {
    background: var(--bs-info);
    border-color: var(--bs-info);
    color: #fff;
    box-shadow: 0 2px 8px rgba(var(--bs-info-rgb), .4);
}
/* Warning chips (tipos de cliente) */
#serviceFormModal .sfm-chip-warning .sfm-chip-pill {
    border-color: rgba(var(--bs-warning-rgb), .35);
    background: rgba(var(--bs-warning-rgb), .08);
    color: var(--bs-warning);
}
#serviceFormModal .sfm-chip-warning:hover .sfm-chip-pill {
    background: rgba(var(--bs-warning-rgb), .16);
    border-color: var(--bs-warning);
}
#serviceFormModal .sfm-chip-warning .sfm-chip-input:checked + .sfm-chip-pill {
    background: var(--bs-warning);
    border-color: var(--bs-warning);
    color: #fff;
    box-shadow: 0 2px 8px rgba(var(--bs-warning-rgb), .4);
}
/* Check icon inside chip — only visible when selected */
#serviceFormModal .sfm-chip-check {
    font-size: .75rem;
    display: none;
}
#serviceFormModal .sfm-chip-input:checked + .sfm-chip-pill .sfm-chip-check {
    display: inline-block;
}

/* Add button */
#serviceFormModal .sfm-add-btn {
    background: rgba(var(--bs-primary-rgb), .1);
    color: var(--bs-primary);
    border: 1px solid rgba(var(--bs-primary-rgb), .25);
    font-size: .78rem;
    font-weight: 600;
    border-radius: .5rem;
    padding: .3rem .75rem;
}
#serviceFormModal .sfm-add-btn:hover {
    background: rgba(var(--bs-primary-rgb), .18);
    border-color: var(--bs-primary);
}

/* Empty state */
#serviceFormModal .sfm-empty-state {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: .875rem 1rem;
    border-radius: .5rem;
    background: rgba(var(--bs-secondary-rgb), .06);
    border: 1px dashed var(--bs-border-color);
    color: var(--bs-secondary-color);
    font-size: .82rem;
}
#serviceFormModal .sfm-empty-state i {
    font-size: 1.1rem;
    opacity: .6;
}

/* Document item card */
#serviceFormModal .sfm-item-card {
    padding: .875rem 1rem;
    border-radius: .5rem;
    background: var(--bs-tertiary-bg, rgba(var(--bs-secondary-rgb), .06));
    border: 1px solid var(--bs-border-color);
}

/* Requirements input group */
#serviceFormModal .sfm-req-group .sfm-req-icon {
    background: rgba(var(--bs-success-rgb), .1);
    color: var(--bs-success);
    border-color: var(--bs-border-color);
    font-size: .85rem;
}

/* Footer */
#serviceFormModal .sfm-footer {
    padding: 1rem 1.5rem;
    background: var(--bs-body-bg);
    border-top: 1px solid var(--bs-border-color) !important;
}

/* ── Process & methods builder ──────────────────────────────── */
#serviceFormModal .sfm-process-card {
    border: 1px solid var(--bs-border-color);
    border-radius: .5rem;
    overflow: hidden;
    margin-bottom: .75rem;
}
#serviceFormModal .sfm-process-header {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .625rem .875rem;
    background: rgba(var(--bs-primary-rgb),.05);
    border-bottom: 1px solid var(--bs-border-color);
}
#serviceFormModal .sfm-process-icon {
    width: 1.75rem; height: 1.75rem;
    border-radius: .375rem;
    background: rgba(var(--bs-primary-rgb),.12);
    color: var(--bs-primary);
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; flex-shrink: 0;
}
#serviceFormModal .sfm-process-select {
    flex: 1; max-width: 220px;
    background: var(--bs-body-bg); color: var(--bs-body-color);
    border-color: var(--bs-border-color);
}
#serviceFormModal .sfm-methods-list {
    padding: .625rem .875rem;
    display: flex; flex-direction: column; gap: .4rem;
}
#serviceFormModal .sfm-method-row {
    display: flex; align-items: center; gap: .5rem;
}
#serviceFormModal .sfm-method-select {
    flex: 1;
    background: var(--bs-body-bg); color: var(--bs-body-color);
    border-color: var(--bs-border-color);
}
#serviceFormModal .sfm-hours-input-wrap {
    display: flex; align-items: center; gap: .35rem; flex-shrink: 0;
}
#serviceFormModal .sfm-hours-label {
    font-size: .72rem; color: var(--bs-secondary-color); white-space: nowrap;
}
#serviceFormModal .sfm-add-method-btn {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .25rem .65rem; font-size: .75rem; font-weight: 600;
    border-radius: .375rem;
    border: 1px dashed rgba(var(--bs-secondary-rgb),.4);
    background: transparent; color: var(--bs-secondary-color);
    cursor: pointer; margin-top: .25rem; align-self: flex-start;
    transition: all .15s;
}
#serviceFormModal .sfm-add-method-btn:hover { border-color: var(--bs-primary); color: var(--bs-primary); }

/* dark mode */
.dark-style #serviceFormModal .sfm-process-card { border-color: rgba(255,255,255,.08); }
.dark-style #serviceFormModal .sfm-process-header {
    background: rgba(105,108,255,.08); border-bottom-color: rgba(255,255,255,.08);
}
.dark-style #serviceFormModal .sfm-process-select,
.dark-style #serviceFormModal .sfm-method-select {
    background: #1e1e2d !important; color: #d0d4e4 !important;
    border-color: rgba(255,255,255,.12) !important;
}
.dark-style #serviceFormModal .sfm-method-row .form-control {
    background: #1e1e2d !important; color: #d0d4e4 !important;
    border-color: rgba(255,255,255,.12) !important;
}
</style>
@endpush

@push('scripts')
<script>
function serviceForm() {
    return {
        editingId: null,
        saving: false,
        errors: {},
        form: {
            short_name: '',
            description: '',
            functional_areas: [],
            client_types: [],
            documents: [],
            requirements: [],
            processes: [],
        },

        init() {
            // Wire edit buttons
            document.addEventListener('click', e => {
                const editBtn = e.target.closest('.js-edit-service');
                if (editBtn) {
                    const data = JSON.parse(editBtn.dataset.service);
                    this.openEdit(data);
                }
                const viewBtn = e.target.closest('.js-view-service');
                if (viewBtn) {
                    this.openView(viewBtn.dataset.serviceId);
                }
                const toggleBtn = e.target.closest('.js-toggle-service');
                if (toggleBtn) {
                    this.toggleStatus(toggleBtn.dataset.serviceId, toggleBtn.dataset.currentStatus, toggleBtn);
                }
            });
        },

        openCreate() {
            this.editingId = null;
            this.errors = {};
            this.form = { short_name:'', description:'', functional_areas:[], client_types:[], documents:[], requirements:[], processes:[] };
        },

        openEdit(data) {
            this.editingId = data.id;
            this.errors = {};
            this.form = {
                short_name:       data.short_name || '',
                description:      data.description || '',
                functional_areas: data.functional_areas || [],
                client_types:     data.client_types || [],
                documents:        (data.documents || []).map(d => ({name: d.name, type: d.type, description: d.description||''})),
                requirements:     (data.requirements || []).map(r => ({description: r.description})),
                processes:        (data.processes || []).map(p => ({
                    name: p.name,
                    methods: (p.methods || []).map(m => ({method: m.method, standard_hours: m.standard_hours}))
                })),
            };
        },

        openView(serviceId) {
            const body = document.getElementById('viewServiceBody');
            const meta = document.getElementById('viewServiceMeta');
            body.innerHTML = `<div class="text-center py-5"><div class="spinner-border text-primary"></div><div class="text-muted small mt-2">Cargando...</div></div>`;

            fetch(`/admin/services/${serviceId}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                const s = data.service;
                document.getElementById('viewServiceName').textContent = s.short_name;

                // Hero meta: status + version
                const statusBadge = s.status === 'active'
                    ? `<span class="vsm-status-badge vsm-status-active"><i class="ti ti-circle-check"></i> Activo</span>`
                    : `<span class="vsm-status-badge vsm-status-inactive"><i class="ti ti-circle-x"></i> Inactivo</span>`;
                meta.innerHTML = `${statusBadge} <span class="vsm-version-pill">v${s.version||1}</span>`;

                // Chips
                const areas = (s.functional_areas||[]).map(a =>
                    `<span class="vsm-chip vsm-chip-info">${a}</span>`).join('') ||
                    `<span class="text-muted small">No definidas</span>`;
                const types = (s.client_types||[]).map(t =>
                    `<span class="vsm-chip vsm-chip-warning">${t.replace(/_/g,' ')}</span>`).join('') ||
                    `<span class="text-muted small">No definidos</span>`;

                // Documents
                const docsHtml = (s.documents||[]).length
                    ? (s.documents||[]).map(d => `
                        <div class="vsm-doc-item">
                            <div class="vsm-doc-icon"><i class="ti ti-file-text"></i></div>
                            <div class="vsm-doc-body">
                                <div class="vsm-doc-name">${d.name} <span class="vsm-type-pill">${d.type}</span></div>
                                ${d.description ? `<div class="vsm-doc-desc">${d.description}</div>` : ''}
                            </div>
                        </div>`).join('')
                    : `<div class="vsm-empty"><i class="ti ti-file-off"></i> Sin documentos registrados</div>`;

                // Requirements
                const reqsHtml = (s.requirements||[]).length
                    ? (s.requirements||[]).map(r => `
                        <div class="vsm-req-item">
                            <span class="vsm-req-check"><i class="ti ti-check"></i></span>
                            <span>${r.description}</span>
                        </div>`).join('')
                    : `<div class="vsm-empty"><i class="ti ti-list-check"></i> Sin requisitos registrados</div>`;

                // Audit logs
                const actionLabels = { created:'Creado', updated:'Actualizado', activated:'Activado', deactivated:'Desactivado' };
                const actionIcons  = { created:'ti-plus', updated:'ti-pencil', activated:'ti-circle-check', deactivated:'ti-circle-x' };
                const logsHtml = (s.audit_logs||[]).length
                    ? (s.audit_logs||[]).slice(0,5).map(l => {
                        const label = actionLabels[l.action] || l.action;
                        const icon  = actionIcons[l.action]  || 'ti-activity';
                        const by    = l.changed_by?.name || 'Sistema';
                        const date  = new Date(l.created_at).toLocaleDateString('es-VE',{day:'2-digit',month:'short',year:'numeric'});
                        return `<div class="vsm-log-item">
                            <span class="vsm-log-icon"><i class="ti ${icon}"></i></span>
                            <span class="vsm-log-label">${label}</span>
                            <span class="vsm-log-by">por <strong>${by}</strong></span>
                            <span class="vsm-log-date">${date}</span>
                        </div>`;
                    }).join('')
                    : `<div class="vsm-empty"><i class="ti ti-history"></i> Sin historial</div>`;

                body.innerHTML = `
                    <div class="vsm-description">${s.description}</div>

                    <div class="vsm-grid-section">
                        <div class="vsm-subsection">
                            <div class="vsm-subsection-title"><i class="ti ti-layout-grid"></i> Áreas funcionales</div>
                            <div class="vsm-chips">${areas}</div>
                        </div>
                        <div class="vsm-subsection">
                            <div class="vsm-subsection-title"><i class="ti ti-building"></i> Tipos de cliente</div>
                            <div class="vsm-chips">${types}</div>
                        </div>
                    </div>

                    <div class="vsm-grid-section">
                        <div class="vsm-subsection">
                            <div class="vsm-subsection-title"><i class="ti ti-paperclip"></i> Documentos entregables</div>
                            ${docsHtml}
                        </div>
                        <div class="vsm-subsection">
                            <div class="vsm-subsection-title"><i class="ti ti-checklist"></i> Requisitos del cliente</div>
                            ${reqsHtml}
                        </div>
                    </div>

                    <div class="vsm-log-section">
                        <div class="vsm-subsection-title"><i class="ti ti-history"></i> Historial de auditoría</div>
                        ${logsHtml}
                    </div>
                `;
            })
            .catch(() => { body.innerHTML = '<div class="alert alert-danger m-3">Error al cargar el servicio.</div>'; });
        },

        async save() {
            this.saving = true;
            this.errors = {};
            const url     = this.editingId ? `/admin/services/${this.editingId}` : '/admin/services';
            const method  = this.editingId ? 'PUT' : 'POST';

            try {
                const res = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.form),
                });
                const data = await res.json();

                if (!res.ok) {
                    if (res.status === 422 && data.errors) {
                        this.errors = Object.fromEntries(Object.entries(data.errors).map(([k,v]) => [k, v[0]]));
                        this.showFeedback('Corrige los errores indicados.', 'danger');
                    } else {
                        this.showFeedback(data.message || 'Error al guardar.', 'danger');
                    }
                    return;
                }

                bootstrap.Modal.getInstance(document.getElementById('serviceFormModal'))?.hide();
                window.location.reload();
            } catch (e) {
                this.showFeedback('Error de conexión.', 'danger');
            } finally {
                this.saving = false;
            }
        },

        async toggleStatus(serviceId, currentStatus, btn) {
            if (!confirm(currentStatus === 'active' ? '¿Desactivar este servicio?' : '¿Activar este servicio?')) return;
            const res  = await fetch(`/admin/services/${serviceId}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });
            if (res.ok) { window.location.reload(); }
            else        { alert('No se pudo cambiar el estado.'); }
        },

        showFeedback(msg, type) {
            const el = document.getElementById('serviceFormFeedback');
            el.className = `alert alert-${type}`;
            el.textContent = msg;
        },
    };
}

// Init Alpine-independent open (for the header button)
document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('[\\@click="openCreate()"]')?.addEventListener('click', () => {
        // Alpine handles this
    });
});
</script>
@endpush
