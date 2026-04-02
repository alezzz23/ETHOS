@extends('layouts.vuexy')

@section('title', 'Servicios')

@section('content')
@php
    $canCreate     = auth()->user()?->can('services.create');
    $canEdit       = auth()->user()?->can('services.edit');
    $canDeactivate = auth()->user()?->can('services.deactivate');
@endphp

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
                    @click="openCreate()" x-data>
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
                                        title="Ver detalle"
                                        data-service-id="{{ $service->id }}">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                    @if($canEdit)
                                    <button type="button"
                                        class="btn btn-sm btn-icon btn-text-secondary rounded-pill js-edit-service"
                                        title="Editar"
                                        data-service='{{ json_encode(["id"=>$service->id,"short_name"=>$service->short_name,"description"=>$service->description,"functional_areas"=>$service->functional_areas,"client_types"=>$service->client_types,"documents"=>$service->documents->map(fn($d)=>["name"=>$d->name,"type"=>$d->type,"description"=>$d->description])->values(),"requirements"=>$service->requirements->map(fn($r)=>["description"=>$r->description])->values()]) }}'>
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
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewServiceModalLabel">
                    <i class="ti ti-briefcase me-2"></i>
                    <span id="viewServiceName">Detalle del Servicio</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewServiceBody">
                <div class="text-center py-4"><div class="spinner-border text-primary"></div></div>
            </div>
        </div>
    </div>
</div>

{{-- ─── Create / Edit Modal ────────────────────────────────────────── --}}
<div class="modal fade" id="serviceFormModal" tabindex="-1" aria-labelledby="serviceFormModalLabel" aria-hidden="true"
     x-data="serviceForm()" x-init="init()">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="serviceFormModalLabel">
                    <i class="ti ti-tools me-2"></i>
                    <span x-text="editingId ? 'Editar Servicio' : 'Nuevo Servicio'"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="serviceFormFeedback" class="alert d-none" role="alert"></div>

                {{-- Basic fields --}}
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="form-label">Nombre corto <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" x-model="form.short_name"
                               placeholder="Ej: Auditoría Fiscal" maxlength="120">
                        <div class="invalid-feedback" x-show="errors.short_name" x-text="errors.short_name"></div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Descripción extendida <span class="text-danger">*</span></label>
                        <textarea class="form-control" x-model="form.description" rows="4"
                                  placeholder="Describe el alcance, metodología y valor del servicio..." maxlength="5000"></textarea>
                        <div class="invalid-feedback" x-show="errors.description" x-text="errors.description"></div>
                    </div>
                </div>

                {{-- Functional areas --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Áreas funcionales</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($functionalAreas as $area)
                        <label class="d-flex align-items-center gap-1 cursor-pointer">
                            <input type="checkbox" class="form-check-input mt-0"
                                   :value="'{{ $area }}'"
                                   x-model="form.functional_areas">
                            <span class="badge bg-label-info">{{ $area }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Client types --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Tipos de cliente</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($clientTypes as $key => $label)
                        <label class="d-flex align-items-center gap-1 cursor-pointer">
                            <input type="checkbox" class="form-check-input mt-0"
                                   :value="'{{ $key }}'"
                                   x-model="form.client_types">
                            <span class="badge bg-label-warning">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Documents --}}
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-semibold mb-0">Documentos de respaldo entregados</label>
                        <button type="button" class="btn btn-sm btn-outline-primary"
                                @click="form.documents.push({name:'',type:'otro',description:''})">
                            <i class="ti ti-plus"></i> Agregar
                        </button>
                    </div>
                    <template x-if="form.documents.length === 0">
                        <p class="text-muted small">Sin documentos. Agrega uno con el botón.</p>
                    </template>
                    <template x-for="(doc, i) in form.documents" :key="i">
                        <div class="card mb-2">
                            <div class="card-body p-2">
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
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-sm btn-icon btn-text-danger"
                                                @click="form.documents.splice(i,1)" title="Eliminar">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Requirements --}}
                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-semibold mb-0">Checklist de requisitos del cliente</label>
                        <button type="button" class="btn btn-sm btn-outline-primary"
                                @click="form.requirements.push({description:''})">
                            <i class="ti ti-plus"></i> Agregar
                        </button>
                    </div>
                    <template x-if="form.requirements.length === 0">
                        <p class="text-muted small">Sin requisitos. Agrega uno con el botón.</p>
                    </template>
                    <template x-for="(req, i) in form.requirements" :key="i">
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="ti ti-check"></i></span>
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" @click="save()" :disabled="saving">
                    <span x-show="saving" class="spinner-border spinner-border-sm me-1"></span>
                    <span x-text="saving ? 'Guardando...' : (editingId ? 'Actualizar' : 'Crear Servicio')"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

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
            this.form = { short_name:'', description:'', functional_areas:[], client_types:[], documents:[], requirements:[] };
            new bootstrap.Modal(document.getElementById('serviceFormModal')).show();
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
            };
            new bootstrap.Modal(document.getElementById('serviceFormModal')).show();
        },

        openView(serviceId) {
            const body = document.getElementById('viewServiceBody');
            body.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>';
            new bootstrap.Modal(document.getElementById('viewServiceModal')).show();

            fetch(`/admin/services/${serviceId}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                const s = data.service;
                document.getElementById('viewServiceName').textContent = s.short_name;
                const areas  = (s.functional_areas||[]).map(a => `<span class="badge bg-label-info me-1">${a}</span>`).join('');
                const types  = (s.client_types||[]).map(t => `<span class="badge bg-label-warning me-1">${t}</span>`).join('');
                const docs   = (s.documents||[]).map(d => `<li class="list-group-item"><strong>${d.name}</strong> <span class="badge bg-secondary ms-1">${d.type}</span><div class="text-muted small">${d.description||''}</div></li>`).join('') || '<li class="list-group-item text-muted">Sin documentos</li>';
                const reqs   = (s.requirements||[]).map(r => `<li class="list-group-item"><i class="ti ti-check text-success me-2"></i>${r.description}</li>`).join('') || '<li class="list-group-item text-muted">Sin requisitos</li>';
                const logs   = (s.audit_logs||[]).slice(0,5).map(l => `<li class="list-group-item d-flex justify-content-between"><span><strong>${l.action}</strong> por ${l.changed_by?.name||'Sistema'}</span><small class="text-muted">${new Date(l.created_at).toLocaleDateString('es')}</small></li>`).join('') || '<li class="list-group-item text-muted">Sin historial</li>';

                body.innerHTML = `
                    <p class="text-muted">${s.description}</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="fw-semibold mb-1">Áreas funcionales</div>
                            <div>${areas || '<span class="text-muted">No definidas</span>'}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="fw-semibold mb-1">Tipos de cliente</div>
                            <div>${types || '<span class="text-muted">No definidos</span>'}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="fw-semibold mb-2">Documentos entregables</div>
                            <ul class="list-group list-group-flush">${docs}</ul>
                        </div>
                        <div class="col-md-6">
                            <div class="fw-semibold mb-2">Requisitos del cliente</div>
                            <ul class="list-group list-group-flush">${reqs}</ul>
                        </div>
                        <div class="col-12">
                            <div class="fw-semibold mb-2"><i class="ti ti-history me-1"></i>Historial de auditoría</div>
                            <ul class="list-group list-group-flush">${logs}</ul>
                        </div>
                    </div>
                `;
            })
            .catch(() => { body.innerHTML = '<div class="alert alert-danger">Error al cargar el servicio.</div>'; });
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
