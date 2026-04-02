@extends('layouts.vuexy')

@section('title', 'Propuestas')

@section('content')
@php
    $canCreate  = auth()->user()?->can('proposals.create');
    $canEdit    = auth()->user()?->can('proposals.edit');
    $canApprove = auth()->user()?->can('proposals.approve');
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
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
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
                                    <div class="fw-semibold small">{{ $proposal->project->title }}</div>
                                    <div class="text-muted" style="font-size:.78rem">{{ $proposal->project->client?->name }}</div>
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
                                <td class="small">{{ $proposal->createdBy->name }}</td>
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
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="ti ti-file-off fs-3 d-block mb-2"></i>
                                    No hay propuestas que coincidan con los filtros.
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

        sendProposal(id) {
            if (!confirm('¿Marcar esta propuesta como enviada al cliente?')) return;
            this.loading = true;
            fetch(`/admin/proposals/${id}/send`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(r => r.json())
            .then(data => {
                alert(data.message);
                window.location.reload();
            })
            .finally(() => this.loading = false);
        },

        approveProposal(id) {
            if (!confirm('¿Aprobar esta propuesta? Se generará la lista de levantamiento automáticamente.')) return;
            this.loading = true;
            fetch(`/admin/proposals/${id}/approve`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(r => r.json())
            .then(data => {
                alert(data.message);
                window.location.reload();
            })
            .finally(() => this.loading = false);
        },

        openReject(id) {
            this.rejectId = id;
            this.rejectReason = '';
            this.rejectError = '';
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
        },

        confirmReject() {
            if (!this.rejectReason.trim()) {
                this.rejectError = 'El motivo es obligatorio.';
                return;
            }
            this.loading = true;
            fetch(`/admin/proposals/${this.rejectId}/reject`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ rejection_reason: this.rejectReason }),
            })
            .then(r => r.json())
            .then(data => {
                alert(data.message);
                window.location.reload();
            })
            .finally(() => this.loading = false);
        }
    };
}
</script>
@endpush
