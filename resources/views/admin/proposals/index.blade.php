@extends('layouts.vuexy')

@section('title', 'Propuestas')

@section('content')
@php
    $canCreate  = auth()->user()?->can('proposals.create');
    $canEdit    = auth()->user()?->can('proposals.edit');
    $canApprove = auth()->user()?->can('proposals.approve');
    $canViewProjects = auth()->user()?->can('projects.view');
    $projectFilter = request('project_id');
    $proposalGuide = match (request('status')) {
        'draft' => [
            'eyebrow' => 'Borradores activos',
            'title' => 'Estas propuestas todavia no mueven el flujo: falta enviarlas.',
            'message' => 'Mientras una propuesta siga en borrador, el proyecto no puede avanzar comercialmente. El siguiente paso correcto es enviarla al cliente o al aprobador interno.',
            'icon' => 'ti-send',
            'steps' => [
                'Revisa horas, rango de precios y plan de pagos.',
                'Marca la propuesta como enviada desde esta misma tabla.',
                'Despues quedara lista para aprobacion o rechazo.',
            ],
        ],
        'sent' => [
            'eyebrow' => 'Pendiente de decision',
            'title' => 'Las propuestas enviadas ya estan en el punto de aprobacion.',
            'message' => 'Aqui el flujo depende de una decision: aprobar para mover el proyecto a aprobado o rechazar para reformular la oferta.',
            'icon' => 'ti-gavel',
            'steps' => [
                'Revisa la propuesta enviada y su contexto.',
                'Apruebala si ya es viable o rechazala con motivo.',
                'Al aprobar, el proyecto quedara listo para iniciar ejecucion.',
            ],
        ],
        'approved' => [
            'eyebrow' => 'Ciclo comercial completado',
            'title' => 'Las propuestas aprobadas ya empujaron el proyecto a la siguiente fase.',
            'message' => 'Desde aqui el siguiente trabajo relevante ocurre en la ficha del proyecto: revisar checklist, responsables e inicio de ejecucion.',
            'icon' => 'ti-circle-check',
            'steps' => [
                'Abre la ficha del proyecto asociado.',
                'Confirma que el proyecto este en aprobado y revisa su checklist.',
                'Cuando corresponda, inicia la ejecucion.',
            ],
        ],
        'rejected' => [
            'eyebrow' => 'Reformulacion pendiente',
            'title' => 'Las propuestas rechazadas requieren una nueva version o un ajuste.',
            'message' => 'El rechazo no cierra el proyecto: solo devuelve el flujo a una etapa de ajuste para preparar una mejor oferta.',
            'icon' => 'ti-refresh-alert',
            'steps' => [
                'Revisa el motivo del rechazo.',
                'Prepara una nueva propuesta o reajusta el alcance.',
                'Vuelve a enviarla para retomar el circuito comercial.',
            ],
        ],
        default => [
            'eyebrow' => 'Ciclo de propuestas',
            'title' => 'Este modulo controla el tramo comercial entre analisis y aprobacion.',
            'message' => 'Usalo para seguir el estado real de cada propuesta y no perder el siguiente paso del flujo del proyecto.',
            'icon' => 'ti-git-merge',
            'steps' => [
                'Crea la propuesta desde un proyecto en analisis.',
                'Enviala cuando el borrador quede listo.',
                'Apruebala o rechazala para que el proyecto avance correctamente.',
            ],
        ],
    };
@endphp

<div class="row g-4" x-data="proposalManager()">

    {{-- Header --}}
    <div class="col-12">
        <div class="card ethos-crm-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0 d-flex align-items-center gap-2">
                    <i class="ti ti-file-description"></i>
                    <span>Propuestas de Servicio</span>
                </h5>
                @if($canCreate)
                <a href="{{ route('proposals.create') }}" class="btn btn-primary ethos-create-btn">
                    <i class="ti ti-plus"></i>
                    <span>Nueva Propuesta</span>
                </a>
                @endif
            </div>

            {{-- Filters --}}
            <div class="card-body pb-0">
                <x-ethos.workflow-hint
                    class="mb-4"
                    :eyebrow="$proposalGuide['eyebrow']"
                    :title="$proposalGuide['title']"
                    :message="$proposalGuide['message']"
                    :icon="$proposalGuide['icon']"
                    :steps="$proposalGuide['steps']"
                    :cta-label="$canCreate ? 'Nueva propuesta' : null"
                    :cta-href="$canCreate ? route('proposals.create', $projectFilter ? ['project_id' => $projectFilter] : []) : null"
                    :secondary-label="$canViewProjects && $projectFilter ? 'Abrir ficha del proyecto' : null"
                    :secondary-href="$canViewProjects && $projectFilter ? route('projects.show', $projectFilter) . '#fase3' : null"
                    storage-key="proposals-index-flow-{{ request('status') ?: 'all' }}"
                />

                <form method="GET" action="{{ route('proposals.index') }}"
                      class="d-flex flex-wrap gap-2 align-items-end">
                    <div>
                        <label class="form-label mb-1 small">Estado</label>
                        <select name="status" class="form-select form-select-sm" style="min-width:140px">
                            <option value="">Todos</option>
                            @foreach(['draft'=>'Borrador','sent'=>'Enviada','approved'=>'Aprobada','rejected'=>'Rechazada','expired'=>'Expirada'] as $val => $lbl)
                                <option value="{{ $val }}" @selected(request('status') === $val)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label mb-1 small">Proyecto</label>
                        <select name="project_id" class="form-select form-select-sm" style="min-width:200px">
                            <option value="">Todos los proyectos</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" @selected(request('project_id') == $project->id)>{{ $project->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-secondary">
                            <i class="ti ti-filter me-1"></i>Filtrar
                        </button>
                        <a href="{{ route('proposals.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="ti ti-x"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="card-body p-0 mt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 ethos-data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Proyecto / Cliente</th>
                                <th>Servicio</th>
                                <th>Horas</th>
                                <th>Precio</th>
                                <th>Estado</th>
                                <th>Consultor</th>
                                <th>Fecha</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($proposals as $proposal)
                            <tr>
                                <td class="text-muted small">{{ str_pad($proposal->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    <div class="fw-semibold small">{{ $proposal->project?->title ?? 'Proyecto eliminado' }}</div>
                                    <div class="text-muted" style="font-size:.78rem">{{ $proposal->project?->client?->name ?? '—' }}</div>
                                </td>
                                <td class="small">{{ $proposal->service?->short_name ?? '—' }}</td>
                                <td class="small">
                                    @if($proposal->total_hours != $proposal->adjusted_hours)
                                        <span class="text-decoration-line-through text-muted">{{ number_format($proposal->total_hours,1) }}h</span>
                                        <span class="text-warning fw-semibold">{{ number_format($proposal->adjusted_hours,1) }}h</span>
                                    @else
                                        {{ number_format($proposal->total_hours,1) }}h
                                    @endif
                                </td>
                                <td class="small">
                                    <div class="fw-semibold">${{ number_format($proposal->price_min) }}–${{ number_format($proposal->price_max) }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $proposal->status_color }}">{{ $proposal->status_label }}</span>
                                </td>
                                <td class="small">{{ $proposal->createdBy?->name ?? '—' }}</td>
                                <td class="small text-muted">{{ $proposal->created_at->format('d/m/Y') }}</td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-sm btn-outline-secondary"
                                            @click="viewProposal({{ $proposal->id }})"
                                            title="Ver detalles">
                                            <i class="ti ti-eye"></i>
                                        </button>
                                        <a href="{{ route('proposals.generate-pdf', $proposal) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-danger" title="Generar PDF">
                                            <i class="ti ti-file-type-pdf"></i>
                                        </a>
                                        @if($canEdit && in_array($proposal->status, ['draft']))
                                        <button class="btn btn-sm btn-outline-info"
                                            @click="sendProposal({{ $proposal->id }})"
                                            title="Marcar como enviada">
                                            <i class="ti ti-send"></i>
                                        </button>
                                        @endif
                                        @if($canApprove && $proposal->status === 'sent')
                                        <button class="btn btn-sm btn-outline-success"
                                            @click="approveProposal({{ $proposal->id }})"
                                            title="Aprobar">
                                            <i class="ti ti-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger"
                                            @click="openReject({{ $proposal->id }})"
                                            title="Rechazar">
                                            <i class="ti ti-x"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="ti ti-file-off fs-3 d-block mb-2 text-muted"></i>
                                    <span class="text-muted">No hay propuestas que coincidan con los filtros.</span>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($proposals->hasPages())
                <div class="card-footer border-top-0 d-flex justify-content-end">
                    {{ $proposals->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Reject modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" x-ref="rejectModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ti ti-x me-2 text-danger"></i>Rechazar Propuesta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Motivo del rechazo <span class="text-danger">*</span></label>
                    <textarea class="form-control" x-model="rejectReason" rows="3"
                        placeholder="Describe el motivo..."></textarea>
                    <div class="text-danger small mt-1" x-show="rejectError" x-text="rejectError"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" @click="confirmReject()" :disabled="loading">
                        <span x-show="loading" class="spinner-border spinner-border-sm me-1"></span>
                        Rechazar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail modal --}}
    <div class="modal fade" id="proposalDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" x-show="selectedProposal">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti ti-file-description me-2"></i>
                        Propuesta <span x-text="selectedProposal?.id ? '#' + String(selectedProposal.id).padStart(5,'0') : ''"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" x-show="selectedProposal">
                    <template x-if="selectedProposal">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="small text-muted">Proyecto</div>
                                <div class="fw-semibold" x-text="selectedProposal.project?.title"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted">Cliente</div>
                                <div class="fw-semibold" x-text="selectedProposal.project?.client?.name"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted">Servicio</div>
                                <div x-text="selectedProposal.service?.short_name ?? '—'"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted">Tamaño empresa</div>
                                <div x-text="selectedProposal.client_size"></div>
                            </div>
                            <div class="col-12"><hr class="my-1"></div>
                            <div class="col-md-4">
                                <div class="small text-muted">Horas estimadas</div>
                                <div class="fw-semibold" x-text="selectedProposal.total_hours + 'h'"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="small text-muted">Horas ajustadas</div>
                                <div class="fw-semibold" x-text="selectedProposal.adjusted_hours + 'h'"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="small text-muted">Tasa / hora</div>
                                <div x-text="'$' + selectedProposal.hourly_rate"></div>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-primary py-2 mb-0">
                                    <strong>Precio estimado:</strong>
                                    $<span x-text="Number(selectedProposal.price_min).toLocaleString()"></span>
                                    —
                                    $<span x-text="Number(selectedProposal.price_max).toLocaleString()"></span>
                                </div>
                            </div>
                            <template x-if="selectedProposal.adjustment_reason">
                                <div class="col-12">
                                    <div class="small text-muted">Nota de ajuste</div>
                                    <div x-text="selectedProposal.adjustment_reason"></div>
                                </div>
                            </template>
                            <template x-if="selectedProposal.rejection_reason">
                                <div class="col-12">
                                    <div class="alert alert-danger py-2 mb-0">
                                        <strong>Motivo de rechazo:</strong>
                                        <span x-text="selectedProposal.rejection_reason"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function proposalManager() {
    return {
        loading: false,
        selectedProposal: null,
        rejectId: null,
        rejectReason: '',
        rejectError: '',

        viewProposal(id) {
            this.selectedProposal = null;
            fetch(`/admin/proposals/${id}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                this.selectedProposal = data;
                const modal = new bootstrap.Modal(document.getElementById('proposalDetailModal'));
                modal.show();
            });
        },

        async sendProposal(id) {
            const isConfirmed = await window.EthosAlerts.confirm({
                title: 'Enviar propuesta',
                text: 'La propuesta quedará marcada como enviada al cliente.',
                confirmButtonText: 'Sí, enviar',
                cancelButtonText: 'Cancelar',
            });
            if (!isConfirmed) return;

            this.loading = true;

            try {
                const response = await fetch(`/admin/proposals/${id}/send`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });
                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    window.EthosAlerts.error(data.message || 'No se pudo enviar la propuesta.');
                    return;
                }

                window.EthosWorkflow.remember({
                    title: 'Propuesta enviada',
                    description: 'La propuesta ya salio de borrador. El siguiente paso correcto es esperar o gestionar su aprobacion.',
                    steps: [
                        'Haz seguimiento al cliente o al aprobador interno.',
                        'Apruebala si ya esta validada.',
                        'Si se rechaza, prepara una nueva version.',
                    ],
                    icon: 'success',
                });
                window.location.reload();
            } catch {
                window.EthosAlerts.error('Error de conexión al enviar la propuesta.');
            } finally {
                this.loading = false;
            }
        },

        async approveProposal(id) {
            const isConfirmed = await window.EthosAlerts.confirm({
                title: 'Aprobar propuesta',
                text: 'Se generará automáticamente la lista de levantamiento del proyecto.',
                confirmButtonText: 'Sí, aprobar',
                cancelButtonText: 'Cancelar',
            });
            if (!isConfirmed) return;

            this.loading = true;

            try {
                const response = await fetch(`/admin/proposals/${id}/approve`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });
                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    window.EthosAlerts.error(data.message || 'No se pudo aprobar la propuesta.');
                    return;
                }

                window.EthosWorkflow.remember({
                    title: 'Propuesta aprobada',
                    description: 'La propuesta ya empujo el proyecto a aprobado. El siguiente paso practico ocurre en la ficha del proyecto.',
                    steps: [
                        'Abre la ficha del proyecto asociado.',
                        'Revisa checklist y responsables.',
                        'Inicia la ejecucion cuando el equipo este listo.',
                    ],
                    icon: 'success',
                    confirmButtonText: 'Abrir ficha del proyecto',
                    cancelButtonText: 'Quedarme en propuestas',
                    confirmUrl: `/admin/projects/${data.proposal.project_id}#fase3`,
                });
                window.location.reload();
            } catch {
                window.EthosAlerts.error('Error de conexión al aprobar la propuesta.');
            } finally {
                this.loading = false;
            }
        },

        openReject(id) {
            this.rejectId = id;
            this.rejectReason = '';
            this.rejectError = '';
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
        },

        async confirmReject() {
            if (!this.rejectReason.trim()) {
                this.rejectError = 'El motivo es obligatorio.';
                return;
            }

            this.loading = true;

            try {
                const response = await fetch(`/admin/proposals/${this.rejectId}/reject`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ rejection_reason: this.rejectReason }),
                });
                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    this.rejectError = data.message || 'No se pudo rechazar la propuesta.';
                    return;
                }

                bootstrap.Modal.getInstance(document.getElementById('rejectModal'))?.hide();
                window.EthosWorkflow.remember({
                    title: 'Propuesta rechazada',
                    description: 'El rechazo ya quedo documentado. El siguiente paso es preparar una nueva version o ajustar la oferta antes de volver a enviarla.',
                    steps: [
                        'Revisa el motivo del rechazo.',
                        'Genera una nueva propuesta con el proyecto como contexto.',
                        'Vuelve a enviarla para retomar el flujo comercial.',
                    ],
                    icon: 'warning',
                    confirmButtonText: 'Crear nueva propuesta',
                    cancelButtonText: 'Quedarme en propuestas',
                    confirmUrl: `/admin/proposals/create?project_id=${data.proposal.project_id}`,
                });
                window.location.reload();
            } catch {
                this.rejectError = 'Error de conexión al rechazar la propuesta.';
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>
@endpush
