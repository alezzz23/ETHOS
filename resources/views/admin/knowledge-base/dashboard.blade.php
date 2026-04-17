@extends('layouts.vuexy')

@section('title', 'Base de Conocimiento & Métricas NPS')

@section('content')
@php $canManage = auth()->user()?->can('admin.access'); @endphp

<div class="row g-4" x-data="knowledgeBase()">

    {{-- NPS Metrics --}}
    <div class="col-12">
        <div class="card ethos-crm-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="ti ti-chart-bar fs-5"></i>
                <h5 class="mb-0">Métricas de Satisfacción</h5>
                <span class="badge bg-secondary ms-auto">{{ $pendingSurveys }} encuestas pendientes</span>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    {{-- NPS Score --}}
                    <div class="col-lg-3 col-sm-6">
                        <div class="text-center p-3 rounded" style="background:#f0f4ff">
                            <div class="fw-bold mb-1" style="font-size:2.5rem;color:#1a3c5e">
                                {{ $nps !== null ? ($nps > 0 ? '+' : '') . $nps : '—' }}
                            </div>
                            <div class="text-muted small">NPS Score</div>
                            <div class="mt-2 d-flex justify-content-center gap-3 small">
                                <span class="text-success">😊 {{ $promoters }} Promotores</span>
                                <span class="text-muted">😐 {{ $passives }} Pasivos</span>
                                <span class="text-danger">😞 {{ $detractors }} Detractores</span>
                            </div>
                            <div class="text-muted mt-1" style="font-size:.75rem">{{ $total }} respuestas</div>
                        </div>
                    </div>
                    {{-- CES --}}
                    <div class="col-lg-3 col-sm-6">
                        <div class="text-center p-3 rounded" style="background:#f0fff4">
                            <div class="fw-bold mb-1" style="font-size:2.5rem;color:#27ae60">
                                {{ $cesAvg !== null ? number_format($cesAvg, 1) : '—' }}
                            </div>
                            <div class="text-muted small">CES (Esfuerzo del cliente)</div>
                            <div class="text-muted mt-2" style="font-size:.75rem">Escala 1–7 (menor es mejor)</div>
                        </div>
                    </div>
                    {{-- CSAT --}}
                    <div class="col-lg-3 col-sm-6">
                        <div class="text-center p-3 rounded" style="background:#fff8f0">
                            <div class="fw-bold mb-1" style="font-size:2.5rem;color:#e67e22">
                                {{ $csatAvg !== null ? number_format($csatAvg, 1) : '—' }}
                            </div>
                            <div class="text-muted small">CSAT (Satisfacción general)</div>
                            <div class="text-muted mt-2" style="font-size:.75rem">Escala 1–5 (mayor es mejor)</div>
                        </div>
                    </div>
                    {{-- Recent responses --}}
                    <div class="col-lg-3 col-sm-6">
                        <div class="p-3 rounded" style="background:#fdf0ff">
                            <div class="fw-semibold mb-2 small text-muted">Últimas respuestas</div>
                            @foreach($responses->sortByDesc('created_at')->take(3) as $resp)
                            <div class="d-flex align-items-center gap-2 mb-1" style="font-size:.8rem">
                                <span class="fw-bold {{ $resp->nps_score >= 9 ? 'text-success' : ($resp->nps_score >= 7 ? 'text-warning' : 'text-danger') }}">
                                    NPS:{{ $resp->nps_score }}
                                </span>
                                <span class="text-muted">{{ $resp->survey->project->title ?? '—' }}</span>
                            </div>
                            @endforeach
                            @if($responses->isEmpty())
                            <div class="text-muted small">Sin respuestas aún</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Knowledge Base --}}
    <div class="col-12">
        <div class="card ethos-crm-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0 d-flex align-items-center gap-2">
                    <i class="ti ti-brain"></i>
                    <span>Base de Conocimiento del Chatbot</span>
                </h5>
                <button type="button" class="btn btn-primary ethos-create-btn"
                    @click="openCreate()">
                    <i class="ti ti-plus"></i>
                    <span>Nueva Entrada</span>
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Título</th>
                                <th>Categoría</th>
                                <th>Servicio</th>
                                <th>Estado</th>
                                <th>Creado por</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($entries as $entry)
                            <tr>
                                <td>
                                    <div class="fw-semibold small">{{ $entry->title }}</div>
                                    @if($entry->embedding_summary)
                                    <div class="text-muted" style="font-size:.75rem">{{ Str::limit($entry->embedding_summary, 60) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        {{ ['faq'=>'FAQ','process'=>'Proceso','case_study'=>'Caso','definition'=>'Definición'][$entry->category] ?? $entry->category }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $entry->service?->short_name ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $entry->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $entry->is_active ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $entry->createdBy->name }}</td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-sm btn-outline-primary"
                                            @click="editEntry({{ json_encode($entry) }})">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger"
                                            @click="deleteEntry({{ $entry->id }})">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="ti ti-brain-off fs-3 d-block mb-2"></i>
                                    No hay entradas en la base de conocimiento aún.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($entries->hasPages())
                <div class="card-footer d-flex justify-content-end">
                    {{ $entries->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    <div class="modal fade" id="kbModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" x-text="editingId ? 'Editar entrada' : 'Nueva entrada de conocimiento'"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Título <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" x-model="form.title"
                                placeholder="Ej. ¿Qué incluye Auditoría Fiscal?">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Categoría <span class="text-danger">*</span></label>
                            <select class="form-select" x-model="form.category">
                                <option value="faq">FAQ</option>
                                <option value="process">Proceso</option>
                                <option value="case_study">Caso de éxito</option>
                                <option value="definition">Definición</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Servicio (opcional)</label>
                            <select class="form-select" x-model="form.service_id">
                                <option value="">General</option>
                                @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->short_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Contenido completo <span class="text-danger">*</span></label>
                            <textarea class="form-control" x-model="form.content" rows="5"
                                placeholder="Texto completo de la entrada, en lenguaje natural..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Resumen para búsqueda (RAG)</label>
                            <textarea class="form-control" x-model="form.embedding_summary" rows="2"
                                placeholder="Versión corta (<500 chars) usada para matching rápido..."></textarea>
                            <div class="form-text">Si se deja vacío, se usarán los primeros 400 caracteres del contenido.</div>
                        </div>
                        <template x-if="editingId">
                            <div class="col-md-3">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" x-model="form.is_active" id="kbActive">
                                    <label class="form-check-label" for="kbActive">Activa</label>
                                </div>
                            </div>
                        </template>
                        <div x-show="formError" class="col-12">
                            <div class="alert alert-danger py-2" x-text="formError"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" @click="saveEntry()" :disabled="saving">
                        <span x-show="saving" class="spinner-border spinner-border-sm me-1"></span>
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function knowledgeBase() {
    return {
        editingId: null,
        saving: false,
        formError: '',
        form: {
            title: '',
            category: 'faq',
            service_id: '',
            content: '',
            embedding_summary: '',
            is_active: true,
        },

        openCreate() {
            this.editingId = null;
            this.form = { title:'', category:'faq', service_id:'', content:'', embedding_summary:'', is_active:true };
            this.formError = '';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('kbModal')).show();
        },

        editEntry(entry) {
            this.editingId = entry.id;
            this.form = {
                title:             entry.title,
                category:          entry.category,
                service_id:        entry.service_id ?? '',
                content:           entry.content,
                embedding_summary: entry.embedding_summary ?? '',
                is_active:         entry.is_active,
            };
            this.formError = '';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('kbModal')).show();
        },

        async saveEntry() {
            this.saving = true;
            this.formError = '';
            const token = document.querySelector('meta[name="csrf-token"]').content;
            const url    = this.editingId
                ? `/admin/knowledge-base/${this.editingId}`
                : '/admin/knowledge-base';
            const method = this.editingId ? 'PUT' : 'POST';

            const resp = await fetch(url, {
                method,
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(this.form),
            });
            const data = await resp.json();
            this.saving = false;

            if (!resp.ok) {
                this.formError = data.message || 'Error al guardar.';
                return;
            }
            window.location.reload();
        },

        async deleteEntry(id) {
            const isConfirmed = await window.EthosAlerts.confirm({
                title: 'Eliminar entrada',
                text: 'La entrada se eliminará de la base de conocimiento.',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                danger: true,
            });
            if (!isConfirmed) return;

            const token = document.querySelector('meta[name="csrf-token"]').content;
            try {
                const response = await fetch(`/admin/knowledge-base/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                });
                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    window.EthosAlerts.error(data.message || 'No se pudo eliminar la entrada.');
                    return;
                }

                await window.EthosAlerts.success(data.message || 'Entrada eliminada.', { timer: 1000 });
                window.location.reload();
            } catch {
                window.EthosAlerts.error('Error de conexión al eliminar la entrada.');
            }
        }
    };
}
</script>
@endpush
