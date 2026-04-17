@extends('layouts.vuexy')

@section('title', 'Nueva Propuesta')

@section('content')
@php
    $projectContextUrl = $selectedProject ? route('projects.show', $selectedProject) . '#fase2' : null;
@endphp

<div class="row justify-content-center" x-data="proposalWizard()">

    <div class="col-xl-9 col-lg-10">
        <x-ethos.workflow-hint
            class="mb-4"
            storage-key="proposal-create-flow"
            eyebrow="Fase comercial"
            icon="ti-file-plus"
            title="Esta pantalla convierte el análisis del proyecto en una propuesta formal."
            message="Cuando guardes, la propuesta quedará en borrador. El siguiente paso no termina aquí: tendrás que enviarla desde la lista de propuestas para que pueda aprobarse o rechazarse."
            :steps="[
                'Selecciona el proyecto y el servicio asociado al análisis.',
                'Calcula horas, ajusta si hace falta y define el plan de pagos.',
                'Después de guardar, ve a la lista y marca la propuesta como enviada.',
            ]"
            :cta-label="$projectContextUrl ? 'Volver a la ficha del proyecto' : null"
            :cta-href="$projectContextUrl"
        >
            El ciclo correcto es: borrador, enviada, aprobada o rechazada.
        </x-ethos.workflow-hint>

        <div class="card ethos-crm-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="ti ti-file-description fs-5"></i>
                <h5 class="mb-0">Nueva Propuesta de Servicio</h5>
            </div>

            {{-- Step indicators --}}
            <div class="card-body border-bottom pb-3">
                <div class="d-flex align-items-center gap-0 justify-content-center">
                    @foreach([1=>'Proyecto',2=>'Cálculo',3=>'Ajuste',4=>'Plan de pago',5=>'Confirmar'] as $num => $label)
                    <div class="d-flex align-items-center" :class="{ 'opacity-50': step < {{ $num }} }">
                        <div class="d-flex flex-column align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                style="width:32px;height:32px;font-size:.8rem"
                                :class="step >= {{ $num }} ? 'bg-primary text-white' : 'bg-light text-muted border'">
                                {{ $num }}
                            </div>
                            <small class="mt-1" style="font-size:.7rem;white-space:nowrap">{{ $label }}</small>
                        </div>
                        @if($num < 5)
                        <div class="mx-1 mb-3" style="width:40px;height:2px;background:#dee2e6"
                            :style="step > {{ $num }} ? 'background:#0d6efd' : ''"></div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- STEP 1: Select project + service --}}
            <div class="card-body" x-show="step === 1">
                <h6 class="text-muted mb-3">1. Selecciona el proyecto y servicio</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Proyecto <span class="text-danger">*</span></label>
                        <select class="form-select" x-model="form.project_id"
                            @change="onProjectChange()">
                            <option value="">— Seleccionar proyecto —</option>
                            @foreach($projects as $project)
                            <option value="{{ $project->id }}"
                                data-client="{{ $project->client?->name }}">
                                {{ $project->title }} ({{ $project->client?->name }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Servicio <span class="text-danger">*</span></label>
                        <select class="form-select" x-model="form.service_id"
                            @change="onServiceChange()">
                            <option value="">— Seleccionar servicio —</option>
                            @foreach($services as $service)
                            <option value="{{ $service->id }}">{{ $service->short_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- STEP 2: Hour calculation --}}
            <div class="card-body" x-show="step === 2">
                <h6 class="text-muted mb-3">2. Parámetros de cálculo de horas</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tamaño de empresa <span class="text-danger">*</span></label>
                        <select class="form-select" x-model="form.client_size">
                            <option value="">— Seleccionar —</option>
                            @foreach($sizes as $size)
                            <option value="{{ $size->size_key }}">
                                {{ $size->label }} ({{ $size->min_employees }}–{{ $size->max_employees >= 65535 ? '+200' : $size->max_employees }} empleados)
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tasa por hora (USD)</label>
                        <input type="number" class="form-control" x-model="form.hourly_rate"
                            min="1" step="0.5">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Margen (%)</label>
                        <input type="number" class="form-control" x-model="form.margin_percent"
                            min="0" max="100" step="1">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Personas a entrevistar (opcional)</label>
                        <input type="number" class="form-control" x-model="form.target_persons"
                            min="1" placeholder="Auto según tamaño empresa">
                    </div>
                    <div class="col-12">
                        <button type="button" class="btn btn-secondary" @click="runCalculation()"
                            :disabled="calcLoading || !form.client_size">
                            <span x-show="calcLoading" class="spinner-border spinner-border-sm me-1"></span>
                            <i class="ti ti-calculator me-1" x-show="!calcLoading"></i>
                            Calcular
                        </button>
                    </div>

                    {{-- Calculation result --}}
                    <template x-if="calcResult">
                        <div class="col-12">
                            <div class="alert alert-primary">
                                <div class="row g-2 text-center">
                                    <div class="col-4">
                                        <div class="small text-muted">Total horas</div>
                                        <div class="fw-bold fs-5" x-text="calcResult.total_hours + 'h'"></div>
                                    </div>
                                    <div class="col-4">
                                        <div class="small text-muted">Precio mín.</div>
                                        <div class="fw-bold fs-5 text-success">$<span x-text="Number(calcResult.price_min).toLocaleString()"></span></div>
                                    </div>
                                    <div class="col-4">
                                        <div class="small text-muted">Precio máx.</div>
                                        <div class="fw-bold fs-5 text-success">$<span x-text="Number(calcResult.price_max).toLocaleString()"></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- STEP 3: Hour adjustment --}}
            <div class="card-body" x-show="step === 3">
                <h6 class="text-muted mb-3">3. Ajuste de horas (opcional)</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Horas calculadas</label>
                        <input type="text" class="form-control" readonly
                            :value="calcResult ? calcResult.total_hours + 'h' : '—'">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Horas ajustadas</label>
                        <input type="number" class="form-control" x-model="form.adjusted_hours"
                            min="0" step="0.5">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Motivo del ajuste</label>
                        <textarea class="form-control" x-model="form.adjustment_reason" rows="2"
                            placeholder="Explica por qué se ajustan las horas (requerido si hay ajuste)..."></textarea>
                    </div>
                </div>
            </div>

            {{-- STEP 4: Payment milestones --}}
            <div class="card-body" x-show="step === 4">
                <h6 class="text-muted mb-3">4. Plan de pagos</h6>
                <p class="text-muted small">Opcional. Divide el cobro en hitos de pago.</p>
                <template x-for="(milestone, idx) in form.payment_milestones" :key="idx">
                    <div class="row g-2 mb-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label small">Hito</label>
                            <input type="text" class="form-control form-control-sm"
                                x-model="milestone.label" placeholder="Ej. Inicio de proyecto">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">% del total</label>
                            <input type="number" class="form-control form-control-sm"
                                x-model="milestone.percentage" min="0" max="100">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Días plazo</label>
                            <input type="number" class="form-control form-control-sm"
                                x-model="milestone.due_days" min="0">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                @click="form.payment_milestones.splice(idx, 1)">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    </div>
                </template>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                    @click="form.payment_milestones.push({label:'',percentage:0,due_days:0})">
                    <i class="ti ti-plus me-1"></i>Agregar hito
                </button>
            </div>

            {{-- STEP 5: Confirm --}}
            <div class="card-body" x-show="step === 5">
                <h6 class="text-muted mb-3">5. Confirmar y guardar</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="small text-muted">Tamaño empresa</div>
                        <div x-text="form.client_size || '—'"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Horas ajustadas</div>
                        <div x-text="(form.adjusted_hours || calcResult?.total_hours || 0) + 'h'"></div>
                    </div>
                    <div class="col-12">
                        <div class="alert alert-success py-2">
                            Precio estimado: <strong>
                                $<span x-text="calcResult ? Number(calcResult.price_min).toLocaleString() : '—'"></span>
                                –
                                $<span x-text="calcResult ? Number(calcResult.price_max).toLocaleString() : '—'"></span>
                            </strong>
                        </div>
                    </div>
                    <div x-show="saveError" class="col-12">
                        <div class="alert alert-danger py-2" x-text="saveError"></div>
                    </div>
                </div>
            </div>

            {{-- Footer navigation --}}
            <div class="card-footer d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary"
                    @click="step--" x-show="step > 1" :disabled="loading">
                    <i class="ti ti-arrow-left me-1"></i>Anterior
                </button>
                <div x-show="step === 1"></div>

                <div class="d-flex gap-2">
                    <a href="{{ route('proposals.index') }}" class="btn btn-light">Cancelar</a>
                    <button type="button" class="btn btn-primary"
                        x-show="step < 5"
                        @click="nextStep()" :disabled="loading">
                        Siguiente <i class="ti ti-arrow-right ms-1"></i>
                    </button>
                    <button type="button" class="btn btn-success"
                        x-show="step === 5"
                        @click="saveProposal()" :disabled="loading">
                        <span x-show="loading" class="spinner-border spinner-border-sm me-1"></span>
                        <i class="ti ti-device-floppy me-1" x-show="!loading"></i>
                        Guardar propuesta
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function proposalWizard() {
    return {
        step: 1,
        loading: false,
        calcLoading: false,
        calcResult: null,
        saveError: '',
        form: {
            project_id: '{{ $selectedProject?->id ?? '' }}',
            service_id: '',
            client_size: '',
            hourly_rate: 25,
            margin_percent: 20,
            target_persons: '',
            adjusted_hours: '',
            adjustment_reason: '',
            payment_milestones: [],
        },

        onProjectChange() {},
        onServiceChange() {
            this.calcResult = null;
        },

        async runCalculation() {
            if (!this.form.service_id || !this.form.client_size) return;
            this.calcLoading = true;
            this.calcResult = null;
            try {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                const payload = {
                    client_size: this.form.client_size,
                    hourly_rate: this.form.hourly_rate,
                    margin_percent: this.form.margin_percent,
                };
                if (this.form.target_persons) payload.persons = this.form.target_persons;

                const resp = await fetch(`/admin/services/${this.form.service_id}/calculate`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                if (!resp.ok) {
                    const err = await resp.json().catch(() => ({}));
                    window.EthosAlerts.error(err.message || 'Error al calcular. Intenta nuevamente.');
                    return;
                }
                const data = await resp.json();
                this.calcResult = data;
                this.form.adjusted_hours = data.total_hours;
            } catch (e) {
                window.EthosAlerts.error('Error de conexión al calcular las horas.');
            } finally {
                this.calcLoading = false;
            }
        },

        async nextStep() {
            if (this.step === 1 && (!this.form.project_id || !this.form.service_id)) {
                await window.EthosAlerts.warning('Selecciona un proyecto y un servicio para continuar.');
                return;
            }
            if (this.step === 2 && !this.calcResult) {
                await window.EthosAlerts.warning('Calcula las horas antes de continuar.');
                return;
            }
            this.step++;
        },

        async saveProposal() {
            this.loading = true;
            this.saveError = '';
            const payload = { ...this.form };
            if (!payload.target_persons) delete payload.target_persons;
            if (!payload.adjusted_hours) delete payload.adjusted_hours;
            if (!payload.adjustment_reason) delete payload.adjustment_reason;

            try {
                const resp = await fetch('/admin/proposals', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const data = await resp.json().catch(() => ({}));
                if (!resp.ok) {
                    this.saveError = data.message || 'Error al guardar la propuesta.';
                    return;
                }
                window.EthosWorkflow.remember({
                    title: 'Propuesta creada',
                    description: 'La propuesta quedó en borrador. El siguiente paso recomendado es revisarla en la lista y marcarla como enviada al cliente.',
                    steps: [
                        'Verifica que horas, rango de precio y plan de pagos estén correctos.',
                        'Desde la lista de propuestas, márcala como enviada.',
                        'Una vez enviada, podrá aprobarse o rechazarse.',
                    ],
                    icon: 'success',
                });
                window.location.href = `/admin/proposals?status=draft&project_id=${this.form.project_id}`;
            } catch (e) {
                this.saveError = 'Error de conexión. Intenta nuevamente.';
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>
@endpush
