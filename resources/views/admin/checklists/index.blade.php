@extends('layouts.vuexy')

@section('title', 'Listas de Levantamiento')

@section('content')

<div class="row g-4" x-data="checklistManager()">

    <div class="col-12">
        <div class="card ethos-crm-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="ti ti-list-check fs-5"></i>
                <h5 class="mb-0">Listas de Levantamiento</h5>
            </div>

            {{-- Filters --}}
            <div class="card-body pb-0">
                <form method="GET" action="{{ route('checklists.index') }}"
                      class="d-flex flex-wrap gap-2 align-items-end">
                    <div>
                        <label class="form-label mb-1 small">Proyecto</label>
                        <select name="project_id" class="form-select form-select-sm" style="min-width:200px">
                            <option value="">Todos los proyectos</option>
                            @foreach(\App\Models\Project::orderBy('title')->get(['id','title']) as $p)
                                <option value="{{ $p->id }}" @selected(request('project_id') == $p->id)>{{ $p->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-secondary">
                            <i class="ti ti-filter me-1"></i>Filtrar
                        </button>
                        <a href="{{ route('checklists.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="ti ti-x"></i>
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-body">
                @forelse($checklists as $checklist)
                <div class="card mb-3 border">
                    <div class="card-header d-flex justify-content-between align-items-center py-2">
                        <div>
                            <span class="fw-semibold">{{ $checklist->title }}</span>
                            <span class="badge {{ $checklist->status === 'completed' ? 'bg-success' : 'bg-info' }} ms-2">
                                {{ $checklist->status === 'completed' ? 'Completada' : 'Activa' }}
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="text-muted small">
                                {{ $checklist->project?->title ?? 'Proyecto eliminado' }}
                                @if($checklist->project?->client)
                                — {{ $checklist->project->client->name }}
                                @endif
                            </div>
                            <button class="btn btn-sm btn-outline-primary"
                                @click="loadChecklist({{ $checklist->id }})">
                                <i class="ti ti-eye me-1"></i>Ver
                            </button>
                        </div>
                    </div>
                    {{-- Progress bar --}}
                    @php
                        $total    = $checklist->items->count();
                        $done     = $checklist->items->where('is_completed', true)->count();
                        $pct      = $total > 0 ? round($done / $total * 100) : 0;
                    @endphp
                    <div class="card-body py-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:8px">
                                <div class="progress-bar bg-{{ $pct === 100 ? 'success' : 'primary' }}"
                                    style="width:{{ $pct }}%"></div>
                            </div>
                            <small class="text-muted">{{ $done }}/{{ $total }} ({{ $pct }}%)</small>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-5">
                    <i class="ti ti-list-off fs-3 d-block mb-2"></i>
                    No hay listas de levantamiento aún.
                    Las listas se generan automáticamente al aprobar una propuesta.
                </div>
                @endforelse

                @if($checklists->hasPages())
                    {{ $checklists->withQueryString()->links() }}
                @endif
            </div>
        </div>
    </div>

    {{-- Detail modal --}}
    <div class="modal fade" id="checklistModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti ti-list-check me-2"></i>
                        <span x-text="activeChecklist?.title || 'Lista de levantamiento'"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <template x-if="activeChecklist">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="progress flex-grow-1" style="height:10px">
                                    <div class="progress-bar"
                                        :style="`width:${activeChecklist.completion_percent}%`"
                                        :class="activeChecklist.completion_percent === 100 ? 'bg-success' : 'bg-primary'">
                                    </div>
                                </div>
                                <small class="text-muted" x-text="`${activeChecklist.completion_percent}% completado`"></small>
                            </div>

                            {{-- Group items by phase --}}
                            <template x-for="phase in getPhasesFromItems(activeChecklist.items)" :key="phase">
                                <div class="mb-3">
                                    <div class="small fw-semibold text-uppercase text-muted mb-2 border-bottom pb-1"
                                        x-text="phaseLabel(phase)"></div>
                                    <template x-for="item in activeChecklist.items.filter(i => i.phase === phase)" :key="item.id">
                                        <div class="d-flex align-items-start gap-2 mb-2">
                                            <input type="checkbox"
                                                class="form-check-input mt-1"
                                                :checked="item.is_completed"
                                                @change="toggleItem(item)">
                                            <div class="flex-grow-1">
                                                <div :class="item.is_completed ? 'text-decoration-line-through text-muted' : ''"
                                                    x-text="item.title"></div>
                                                <div class="text-muted small" x-text="item.description"></div>
                                            </div>
                                            <span x-show="item.is_completed"
                                                class="badge bg-success-subtle text-success">
                                                <i class="ti ti-check"></i>
                                            </span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                    <div x-show="!activeChecklist && modalLoading" class="text-center py-4">
                        <div class="spinner-border text-primary"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function checklistManager() {
    return {
        activeChecklist: null,
        modalLoading: false,

        loadChecklist(id) {
            this.activeChecklist = null;
            this.modalLoading = true;
            const modal = new bootstrap.Modal(document.getElementById('checklistModal'));
            modal.show();

            fetch(`/admin/checklists/${id}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                this.activeChecklist = data;
                this.modalLoading = false;
            });
        },

        getPhasesFromItems(items) {
            const order = ['levantamiento','diagnostico','propuesta','implementacion','seguimiento'];
            const present = [...new Set(items.map(i => i.phase))];
            return order.filter(p => present.includes(p));
        },

        phaseLabel(phase) {
            const map = {
                levantamiento: 'Levantamiento',
                diagnostico: 'Diagnóstico',
                propuesta: 'Propuesta',
                implementacion: 'Implementación',
                seguimiento: 'Seguimiento',
            };
            return map[phase] || phase;
        },

        toggleItem(item) {
            const token = document.querySelector('meta[name="csrf-token"]').content;
            fetch(`/admin/checklist-items/${item.id}/complete`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                }
            })
            .then(r => r.json())
            .then(data => {
                item.is_completed = data.is_completed;
                if (this.activeChecklist) {
                    this.activeChecklist.completion_percent = data.completion_percent;
                }
            });
        }
    };
}
</script>
@endpush
