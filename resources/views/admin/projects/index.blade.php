@extends('layouts.vuexy')

@section('title', 'Proyectos')

@section('content')
@php
    $canUpdateProjects = auth()->user()?->can('projects.edit');
    $canDeleteProjects = auth()->user()?->can('projects.delete');
    $pendingProject = collect($projects->items())->first(fn ($item) => in_array($item->status, ['capturado', 'en_analisis'], true));
@endphp
<div class="row">
    <div class="col-12">
        <div class="card ethos-crm-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 d-flex align-items-center gap-2">
                    <i class="ti ti-briefcase"></i>
                    <span>Proyectos</span>
                </h5>
                @can('projects.create')
                <button type="button" class="btn btn-primary ethos-create-btn" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                    <i class="ti ti-plus"></i>
                    <span>Nuevo Proyecto</span>
                </button>
                @endcan
            </div>
            <div class="card-body">
                <x-ethos.workflow-hint
                    class="mb-4"
                    storage-key="projects-index-flow"
                    eyebrow="Flujo del proyecto"
                    icon="ti-compass"
                    title="La captura es solo el inicio: el siguiente movimiento ocurre en la ficha del proyecto."
                    message="Usa este módulo para registrar la oportunidad con datos mínimos. Luego abre la ficha y continúa el flujo operativo sin perder el contexto."
                    :steps="[
                        'Captura el proyecto con cliente, título y contexto básico.',
                        'En la ficha completa el análisis: servicio, horas, tarifa y líder.',
                        'Desde la misma ficha crea la propuesta formal y sigue su aprobación.',
                    ]"
                    :cta-label="$pendingProject ? 'Abrir siguiente pendiente' : null"
                    :cta-href="$pendingProject ? route('projects.show', $pendingProject) . '#fase2' : null"
                />

                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div id="projectsGlobalFeedback" class="alert d-none ethos-ajax-alert" role="alert" aria-live="polite"></div>
                <div class="table-responsive ethos-table-shell">
                    <table class="table table-hover align-middle ethos-data-table ethos-data-table-projects" id="projectsTable">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Cliente</th>
                                <th>Estado</th>
                                <th>Fase</th>
                                <th>Inicio</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="projectsTableBody">
                            @forelse($projects as $project)
                            @php
                                $statusMap = [
                                    'capturado'      => ['class' => 'ethos-status-capturado',      'icon' => 'ti ti-flag',          'label' => 'Capturado'],
                                    'en_analisis'    => ['class' => 'ethos-status-en-analisis',    'icon' => 'ti ti-stethoscope',   'label' => 'En análisis'],
                                    'aprobado'       => ['class' => 'ethos-status-aprobado',       'icon' => 'ti ti-circle-check',  'label' => 'Aprobado'],
                                    'en_ejecucion'   => ['class' => 'ethos-status-en-ejecucion',   'icon' => 'ti ti-settings-cog', 'label' => 'En ejecución'],
                                    'cerrado'        => ['class' => 'ethos-status-cerrado',        'icon' => 'ti ti-archive',       'label' => 'Cerrado'],
                                ];
                                $statusConfig = $statusMap[$project->status] ?? ['class' => 'ethos-status-default', 'icon' => 'ti ti-info-circle', 'label' => ucfirst($project->status)];
                                $phaseMap = ['capturado'=>'1','en_analisis'=>'2','aprobado'=>'3','en_ejecucion'=>'4','cerrado'=>'4'];
                                $phase = $phaseMap[$project->status] ?? '?';
                            @endphp
                            <tr data-project-id="{{ $project->id }}">
                                <td data-label="Título">
                                    <div class="ethos-primary-cell">
                                        <span class="ethos-cell-avatar">
                                            <i class="ti ti-briefcase-2"></i>
                                        </span>
                                        <span class="ethos-cell-text">{{ $project->title }}</span>
                                    </div>
                                </td>
                                <td data-label="Cliente">
                                    <span class="ethos-muted-cell">
                                        <i class="ti ti-building-skyscraper"></i>
                                        <span>{{ $project->client->name ?? 'Sin cliente' }}</span>
                                    </span>
                                </td>
                                <td data-label="Estado">
                                    <span class="ethos-status-badge {{ $statusConfig['class'] }}">
                                        <i class="{{ $statusConfig['icon'] }}"></i>
                                        <span>{{ $statusConfig['label'] }}</span>
                                    </span>
                                </td>
                                <td data-label="Fase">
                                    <span class="ethos-phase-badge"><i class="ti ti-layers-subtract"></i> Fase {{ $phase }}</span>
                                </td>
                                <td data-label="Inicio">
                                    <span class="ethos-muted-cell">
                                        <i class="ti ti-calendar-event"></i>
                                        <span>{{ $project->starts_at ? \Carbon\Carbon::parse($project->starts_at)->format('d/m/Y') : 'Sin fecha' }}</span>
                                    </span>
                                </td>
                                <td data-label="Acciones">
                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-icon btn-text-secondary rounded-pill ethos-action-btn" title="Ficha completa del proyecto">
                                        <i class="ti ti-layout-dashboard"></i>
                                    </a>
                                    @can('projects.delete')
                                    <button type="button" class="btn btn-sm btn-icon btn-text-danger rounded-pill ethos-action-btn js-delete-project" title="Eliminar proyecto" data-project-id="{{ $project->id }}" data-project-title="{{ $project->title }}">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center ethos-empty-state">
                                    <i class="ti ti-briefcase-off"></i>
                                    <span>No hay proyectos registrados.</span>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $projects->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     MODAL FASE 1 — Captura Rápida (< 1 minuto)
     Solo los datos mínimos. La consultora completará el resto.
════════════════════════════════════════════════════════ --}}
<div class="modal fade ethos-create-modal" id="createProjectModal" tabindex="-1" aria-labelledby="createProjectModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <div class="ethos-modal-title-wrap">
            <span class="ethos-modal-icon" style="background:var(--ethos-brand-alpha,rgba(115,103,240,.12))"><i class="ti ti-flag-3 text-primary" id="projectModalHeaderIcon"></i></span>
            <div>
                <h5 class="modal-title" id="createProjectModalLabel">Captura Rápida de Proyecto</h5>
                <p class="mb-0 ethos-modal-subtitle"><i class="ti ti-clock"></i> Fase 1 — Menos de 1 minuto. La consultora completará el análisis.</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="projectUnifiedForm" action="{{ route('projects.store') }}" method="POST" data-store-url="{{ route('projects.store') }}" novalidate>
          @csrf
          <div class="modal-body">
              <div id="projectFormFeedback" class="alert d-none ethos-ajax-alert" role="alert" aria-live="assertive"></div>

              {{-- Fila 1: Cliente + Título (obligatorios) --}}
              <div class="row g-3 mb-3">
                  <div class="col-md-5">
                      <label class="form-label"><i class="ti ti-building-skyscraper"></i> Cliente <span class="text-danger">*</span></label>
                      <select name="client_id" class="form-select" required>
                          <option value="">Seleccione un cliente</option>
                          @foreach($clients as $client)
                              <option value="{{ $client->id }}">{{ $client->name }}</option>
                          @endforeach
                      </select>
                      <div class="invalid-feedback">Selecciona un cliente.</div>
                  </div>
                  <div class="col-md-7">
                      <label class="form-label"><i class="ti ti-writing"></i> Título del Proyecto <span class="text-danger">*</span></label>
                      <input type="text" name="title" class="form-control" placeholder="Ej: Diagnóstico organizacional Q2" required>
                      <div class="invalid-feedback">El título es obligatorio.</div>
                  </div>
              </div>

              {{-- Fila 2: Descripción breve --}}
              <div class="row g-3 mb-3">
                  <div class="col-12">
                      <label class="form-label"><i class="ti ti-file-description"></i> Descripción <span class="text-muted small">(opcional)</span></label>
                      <textarea name="description" class="form-control" rows="2" placeholder="¿Qué necesita el cliente? ¿Cuál es el problema o la oportunidad?"></textarea>
                  </div>
              </div>

              {{-- Fila 3: Tipo / Urgencia / Complejidad --}}
              <div class="row g-3 mb-3">
                  <div class="col-md-4">
                      <label class="form-label"><i class="ti ti-stack-2"></i> Tipo de Proyecto</label>
                      <select name="type" class="form-select">
                          <option value="">Sin definir</option>
                          <option value="consultoria">Consultoría</option>
                          <option value="desarrollo_web">Desarrollo Web</option>
                          <option value="infraestructura">Infraestructura</option>
                          <option value="soporte">Soporte</option>
                          <option value="mobile">Mobile</option>
                          <option value="otro">Otro</option>
                      </select>
                  </div>
                  <div class="col-md-4">
                      <label class="form-label"><i class="ti ti-alert-triangle"></i> Urgencia</label>
                      <select name="urgency" class="form-select">
                          <option value="">Sin definir</option>
                          <option value="baja">Baja</option>
                          <option value="media">Media</option>
                          <option value="alta">Alta</option>
                      </select>
                  </div>
                  <div class="col-md-4">
                      <label class="form-label"><i class="ti ti-chart-dots"></i> Complejidad</label>
                      <select name="complexity" class="form-select">
                          <option value="">Sin definir</option>
                          <option value="baja">Baja</option>
                          <option value="media">Media</option>
                          <option value="alta">Alta</option>
                      </select>
                  </div>
              </div>

              {{-- Fila 4: Fecha tentativa + Presupuesto estimado --}}
              <div class="row g-3">
                  <div class="col-md-5">
                      <label class="form-label"><i class="ti ti-calendar-event"></i> Fecha de Inicio Tentativa</label>
                      <input type="date" name="starts_at" class="form-control">
                  </div>
                  <div class="col-md-4">
                      <label class="form-label"><i class="ti ti-currency-dollar"></i> Presupuesto Estimado <span class="text-muted small">(opcional)</span></label>
                      <input type="number" name="estimated_budget" class="form-control" step="0.01" min="0" placeholder="0.00">
                  </div>
                  <div class="col-md-3">
                      <label class="form-label"><i class="ti ti-coin"></i> Moneda</label>
                      <select name="currency" class="form-select">
                          <option value="USD">USD</option>
                          <option value="EUR">EUR</option>
                          <option value="VES">VES</option>
                      </select>
                  </div>
              </div>

              {{-- Info Fase --}}
              <div class="alert alert-info mt-3 mb-0 py-2 d-flex gap-2 align-items-center" role="note">
                  <i class="ti ti-bulb fs-5"></i>
                  <span class="small">Al guardar, el proyecto queda en estado <strong>Capturado</strong> y se notifica automáticamente a las consultoras para iniciar el análisis.</span>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                <i class="ti ti-x"></i> Cancelar
              </button>
              <button type="submit" class="btn btn-primary ethos-submit-btn" id="projectSubmitBtn">
                <span class="spinner-border spinner-border-sm d-none" id="projectSubmitSpinner" role="status" aria-hidden="true"></span>
                <i class="ti ti-flag-3" id="projectSubmitIcon"></i>
                <span id="projectSubmitText">Capturar Proyecto</span>
              </button>
          </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
(() => {
    const modalElement = document.getElementById('createProjectModal');
    const modal        = new bootstrap.Modal(modalElement);
    const form         = document.getElementById('projectUnifiedForm');
    const feedback     = document.getElementById('projectFormFeedback');
    const globalFeedback = document.getElementById('projectsGlobalFeedback');
    const tableBody    = document.getElementById('projectsTableBody');
    const submitBtn    = document.getElementById('projectSubmitBtn');
    const submitSpinner = document.getElementById('projectSubmitSpinner');
    const submitIcon   = document.getElementById('projectSubmitIcon');
    const submitText   = document.getElementById('projectSubmitText');
    const storeUrl     = form.dataset.storeUrl;

    const statusLabelMap = {
        capturado:    'Capturado',
        en_analisis:  'En análisis',
        aprobado:     'Aprobado',
        en_ejecucion: 'En ejecución',
        cerrado:      'Cerrado',
    };
    const statusClassMap = {
        capturado:    'ethos-status-capturado',
        en_analisis:  'ethos-status-en-analisis',
        aprobado:     'ethos-status-aprobado',
        en_ejecucion: 'ethos-status-en-ejecucion',
        cerrado:      'ethos-status-cerrado',
    };
    const statusIconMap = {
        capturado:    'ti ti-flag',
        en_analisis:  'ti ti-stethoscope',
        aprobado:     'ti ti-circle-check',
        en_ejecucion: 'ti ti-settings-cog',
        cerrado:      'ti ti-archive',
    };
    const phaseMap = {
        capturado: '1', en_analisis: '2', aprobado: '3', en_ejecucion: '4', cerrado: '4',
    };

    const escapeHtml = (v) => String(v ?? '')
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;').replace(/'/g,'&#039;');

    const clearFeedback = (t) => { t.classList.add('d-none'); t.classList.remove('alert-success','alert-danger'); t.innerHTML=''; };
    const showFeedback  = (t, type, msg) => {
        t.classList.remove('d-none','alert-success','alert-danger');
        t.classList.add(type === 'success' ? 'alert-success' : 'alert-danger');
        t.innerHTML = msg;
    };
    const setLoading = (on) => {
        submitBtn.disabled = on;
        submitSpinner.classList.toggle('d-none', !on);
        submitIcon.classList.toggle('d-none', on);
    };

    const projectRowHtml = (p) => {
        const status = p.status || 'capturado';
        const cls    = statusClassMap[status] || 'ethos-status-default';
        const icon   = statusIconMap[status]  || 'ti ti-info-circle';
        const label  = statusLabelMap[status] || p.status_label || escapeHtml(status);
        const phase  = phaseMap[status] || '?';
        const showUrl = `/admin/projects/${p.id}`;
        const deleteBtn = @json($canDeleteProjects)
            ? `<button type="button" class="btn btn-sm btn-icon btn-text-danger rounded-pill ethos-action-btn js-delete-project" title="Eliminar" data-project-id="${p.id}" data-project-title="${escapeHtml(p.title)}"><i class="ti ti-trash"></i></button>`
            : '';

        return `<tr data-project-id="${p.id}">
            <td data-label="Título">
                <div class="ethos-primary-cell">
                    <span class="ethos-cell-avatar"><i class="ti ti-briefcase-2"></i></span>
                    <span class="ethos-cell-text">${escapeHtml(p.title)}</span>
                </div>
            </td>
            <td data-label="Cliente">
                <span class="ethos-muted-cell"><i class="ti ti-building-skyscraper"></i><span>${escapeHtml(p.client_name_label || 'Sin cliente')}</span></span>
            </td>
            <td data-label="Estado">
                <span class="ethos-status-badge ${cls}"><i class="${icon}"></i><span>${label}</span></span>
            </td>
            <td data-label="Fase">
                <span class="ethos-phase-badge"><i class="ti ti-layers-subtract"></i> Fase ${phase}</span>
            </td>
            <td data-label="Inicio">
                <span class="ethos-muted-cell"><i class="ti ti-calendar-event"></i><span>${escapeHtml(p.starts_at_label || 'Sin fecha')}</span></span>
            </td>
            <td data-label="Acciones">
                <a href="${showUrl}" class="btn btn-sm btn-icon btn-text-secondary rounded-pill ethos-action-btn" title="Ficha completa"><i class="ti ti-layout-dashboard"></i></a>
                ${deleteBtn}
            </td>
        </tr>`;
    };

    const removeEmptyState = () => {
        const e = tableBody.querySelector('.ethos-empty-state');
        if (e) e.closest('tr')?.remove();
    };

    const upsertRow = (p) => {
        const existing = tableBody.querySelector(`tr[data-project-id="${p.id}"]`);
        if (existing) {
            existing.outerHTML = projectRowHtml(p);
        } else {
            removeEmptyState();
            tableBody.insertAdjacentHTML('afterbegin', projectRowHtml(p));
        }
        const row = tableBody.querySelector(`tr[data-project-id="${p.id}"]`);
        row?.classList.add('ethos-row-highlight');
        setTimeout(() => row?.classList.remove('ethos-row-highlight'), 1400);
    };

    // Reset form on modal open
    document.querySelector('[data-bs-target="#createProjectModal"]')?.addEventListener('click', () => {
        form.reset();
        form.classList.remove('was-validated');
        clearFeedback(feedback);
    });
    modalElement.addEventListener('hidden.bs.modal', () => { setLoading(false); clearFeedback(feedback); });

    // Submit: quick capture
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearFeedback(feedback);
        clearFeedback(globalFeedback);

        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            showFeedback(feedback, 'error', 'Revisa los campos obligatorios.');
            return;
        }
        setLoading(true);

        try {
            const res  = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: new FormData(form),
            });
            const data = await res.json().catch(() => ({}));

            if (!res.ok) {
                if (res.status === 422 && data.errors) {
                    const msgs = Object.values(data.errors).flat().map(m => `<li>${escapeHtml(m)}</li>`).join('');
                    showFeedback(feedback, 'error', `<ul class="mb-0 ps-3">${msgs}</ul>`);
                } else {
                    showFeedback(feedback, 'error', escapeHtml(data.message || 'No se pudo capturar el proyecto.'));
                }
                return;
            }

            upsertRow(data.project || {});
            showFeedback(globalFeedback, 'success', escapeHtml(data.message || 'Proyecto capturado.'));
            modal.hide();

            if (data.project?.id) {
                setTimeout(() => {
                    window.EthosWorkflow.show({
                        title: 'Proyecto capturado',
                        description: 'El proyecto ya quedó registrado en Fase 1. El siguiente paso recomendado es abrir la ficha y completar el análisis técnico.',
                        steps: [
                            'Asigna el servicio y calcula las horas estimadas.',
                            'Define el líder del proyecto y la tarifa por hora.',
                            'Genera la propuesta formal cuando el análisis quede listo.',
                        ],
                        icon: 'success',
                        confirmButtonText: 'Abrir ficha del proyecto',
                        cancelButtonText: 'Quedarme aquí',
                        confirmUrl: `/admin/projects/${data.project.id}#fase2`,
                    });
                }, 220);
            }
        } catch {
            showFeedback(feedback, 'error', 'Error de conexión. Intenta nuevamente.');
        } finally {
            setLoading(false);
        }
    });

    // Delete project
    tableBody?.addEventListener('click', async function(e) {
        const btn = e.target.closest('.js-delete-project');
        if (!btn) return;
        const id    = btn.dataset.projectId;
        const title = btn.dataset.projectTitle;
        const isConfirmed = await window.EthosAlerts.confirm({
            title: 'Eliminar proyecto',
            text: `Se eliminará el proyecto "${title}" y esta acción no se puede deshacer.`,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            danger: true,
        });
        if (!isConfirmed) return;

        const token = document.querySelector('meta[name="csrf-token"]')?.content;

        try {
            const response = await fetch(`/admin/projects/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            });
            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                window.EthosAlerts.error(data.message || 'Error al eliminar el proyecto.');
                return;
            }

            btn.closest('tr')?.remove();
            window.EthosAlerts.success(data.message || 'Proyecto eliminado.');
        } catch {
            window.EthosAlerts.error('Error al eliminar el proyecto.');
        }
    });
})();
</script>
@endpush

@push('styles')
<style>
/* ── Status badges ─────────────────────────────────────────── */
.ethos-status-badge {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    font-size: .75rem;
    font-weight: 600;
    padding: .3rem .75rem;
    border-radius: 999px;
    border: 1.5px solid rgba(var(--status-rgb), .35);
    background: rgba(var(--status-rgb), .12);
    color: rgb(var(--status-rgb));
    white-space: nowrap;
}
.ethos-status-badge i { font-size: .8rem; }

.ethos-status-capturado    { --status-rgb: 30, 136, 229; }   /* azul */
.ethos-status-en-analisis  { --status-rgb: 255, 159, 67; }   /* naranja */
.ethos-status-aprobado     { --status-rgb: 40, 199, 111; }   /* verde */
.ethos-status-en-ejecucion { --status-rgb: 115, 103, 240; }  /* morado */
.ethos-status-cerrado      { --status-rgb: 130, 134, 139; }  /* gris */
.ethos-status-default      { --status-rgb: 130, 134, 139; }

/* ── Phase badge ───────────────────────────────────────────── */
.ethos-phase-badge {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    font-size: .72rem;
    font-weight: 700;
    padding: .25rem .6rem;
    border-radius: .375rem;
    background: rgba(var(--bs-secondary-rgb), .1);
    color: var(--bs-body-color);
    border: 1px solid var(--bs-border-color);
    white-space: nowrap;
}
.dark-style .ethos-phase-badge {
    background: rgba(255,255,255,.08);
    color: #c8cee4;
    border-color: rgba(255,255,255,.12);
}
</style>
@endpush
