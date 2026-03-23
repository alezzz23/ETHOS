@extends('layouts.vuexy')

@section('title', 'Proyectos')

@section('content')
@php
    $canUpdateProjects = auth()->user()?->can('projects.update');
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
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="projectsTableBody">
                            @forelse($projects as $project)
                            @php
                                $statusMap = [
                                    'capturado' => ['class' => 'ethos-status-capturado', 'icon' => 'ti ti-flag'],
                                    'clasificacion_pendiente' => ['class' => 'ethos-status-clasificacion-pendiente', 'icon' => 'ti ti-hourglass-high'],
                                    'priorizado' => ['class' => 'ethos-status-priorizado', 'icon' => 'ti ti-stars'],
                                    'asignacion_lider_pendiente' => ['class' => 'ethos-status-asignacion-lider-pendiente', 'icon' => 'ti ti-user-question'],
                                    'en_diagnostico' => ['class' => 'ethos-status-en-diagnostico', 'icon' => 'ti ti-stethoscope'],
                                    'en_diseno' => ['class' => 'ethos-status-en-diseno', 'icon' => 'ti ti-pencil'],
                                    'en_implementacion' => ['class' => 'ethos-status-en-implementacion', 'icon' => 'ti ti-settings-cog'],
                                    'en_seguimiento' => ['class' => 'ethos-status-en-seguimiento', 'icon' => 'ti ti-chart-line'],
                                    'cerrado' => ['class' => 'ethos-status-cerrado', 'icon' => 'ti ti-circle-check'],
                                ];
                                $statusConfig = $statusMap[$project->status] ?? ['class' => 'ethos-status-default', 'icon' => 'ti ti-info-circle'];
                                $projectPayload = [
                                    'id' => $project->id,
                                    'client_id' => $project->client_id,
                                    'title' => $project->title,
                                    'description' => $project->description,
                                    'status' => $project->status,
                                    'starts_at_raw' => $project->starts_at ? \Carbon\Carbon::parse($project->starts_at)->format('Y-m-d') : null,
                                    'ends_at_raw' => $project->ends_at ? \Carbon\Carbon::parse($project->ends_at)->format('Y-m-d') : null,
                                ];
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
                                        <span>{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
                                    </span>
                                </td>
                                <td data-label="Inicio">
                                    <span class="ethos-muted-cell">
                                        <i class="ti ti-calendar-event"></i>
                                        <span>{{ $project->starts_at ? \Carbon\Carbon::parse($project->starts_at)->format('d/m/Y') : 'Sin fecha' }}</span>
                                    </span>
                                </td>
                                <td data-label="Fin">
                                    <span class="ethos-muted-cell">
                                        <i class="ti ti-calendar-check"></i>
                                        <span>{{ $project->ends_at ? \Carbon\Carbon::parse($project->ends_at)->format('d/m/Y') : 'Sin fecha' }}</span>
                                    </span>
                                </td>
                                <td data-label="Acciones">
                                    @can('projects.update')
                                    <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill ethos-action-btn js-edit-project" title="Editar proyecto" data-project='@json($projectPayload)'>
                                        <i class="ti ti-edit"></i>
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
                    {{ $projects->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade ethos-create-modal" id="createProjectModal" tabindex="-1" aria-labelledby="createProjectModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <div class="ethos-modal-title-wrap">
            <span class="ethos-modal-icon"><i class="ti ti-briefcase-2" id="projectModalHeaderIcon"></i></span>
            <div>
                <h5 class="modal-title" id="createProjectModalLabel">Crear Nuevo Proyecto</h5>
                <p class="mb-0 ethos-modal-subtitle" id="projectModalSubtitle">Define cliente, alcance y estado inicial del proyecto.</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="projectUnifiedForm" action="{{ route('projects.store') }}" method="POST" data-store-url="{{ route('projects.store') }}" data-update-url-template="{{ route('projects.update', '__id__') }}" novalidate>
          @csrf
          <input type="hidden" name="_method" id="projectFormMethod" value="POST">
          <input type="hidden" id="projectEditId" value="">
          <div class="modal-body">
              <div id="projectFormFeedback" class="alert d-none ethos-ajax-alert" role="alert" aria-live="assertive"></div>
              <div class="row g-3">
              <div class="col-md-6">
                  <label class="form-label"><i class="ti ti-building-skyscraper"></i> Cliente *</label>
                  <select name="client_id" class="form-select" required>
                      <option value="">Seleccione un cliente</option>
                      @foreach($clients as $client)
                          <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                      @endforeach
                  </select>
              </div>
              <div class="col-md-6">
                  <label class="form-label"><i class="ti ti-writing"></i> Título *</label>
                  <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
              </div>
              <div class="col-12">
                  <label class="form-label"><i class="ti ti-file-description"></i> Descripción</label>
                  <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
              </div>
              <div class="col-md-6">
                  <label class="form-label"><i class="ti ti-stack-2"></i> Estado *</label>
                  <select name="status" class="form-select" required>
                      <option value="capturado" {{ old('status') == 'capturado' ? 'selected' : '' }}>Capturado</option>
                      <option value="clasificacion_pendiente" {{ old('status') == 'clasificacion_pendiente' ? 'selected' : '' }}>Clasificación Pendiente</option>
                      <option value="priorizado" {{ old('status') == 'priorizado' ? 'selected' : '' }}>Priorizado</option>
                      <option value="asignacion_lider_pendiente" {{ old('status') == 'asignacion_lider_pendiente' ? 'selected' : '' }}>Asignación Líder Pendiente</option>
                      <option value="en_diagnostico" {{ old('status') == 'en_diagnostico' ? 'selected' : '' }}>En Diagnóstico</option>
                      <option value="en_diseno" {{ old('status') == 'en_diseno' ? 'selected' : '' }}>En Diseño</option>
                      <option value="en_implementacion" {{ old('status') == 'en_implementacion' ? 'selected' : '' }}>En Implementación</option>
                      <option value="en_seguimiento" {{ old('status') == 'en_seguimiento' ? 'selected' : '' }}>En Seguimiento</option>
                      <option value="cerrado" {{ old('status') == 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                  </select>
              </div>
              <div class="col-md-3">
                      <label class="form-label"><i class="ti ti-calendar-event"></i> Fecha de Inicio</label>
                      <input type="date" name="starts_at" class="form-control" value="{{ old('starts_at') }}">
                  </div>
              <div class="col-md-3">
                      <label class="form-label"><i class="ti ti-calendar-check"></i> Fecha de Fin</label>
                      <input type="date" name="ends_at" class="form-control" value="{{ old('ends_at') }}">
                  </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                <i class="ti ti-x"></i>
                <span>Cancelar</span>
              </button>
              <button type="submit" class="btn btn-primary ethos-submit-btn" id="projectSubmitBtn">
                <span class="spinner-border spinner-border-sm d-none" id="projectSubmitSpinner" role="status" aria-hidden="true"></span>
                <i class="ti ti-device-floppy" id="projectSubmitIcon"></i>
                <span id="projectSubmitText">Guardar Proyecto</span>
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
    const canUpdate = @json($canUpdateProjects);
    const modalElement = document.getElementById('createProjectModal');
    const modal = new bootstrap.Modal(modalElement);
    const form = document.getElementById('projectUnifiedForm');
    const methodInput = document.getElementById('projectFormMethod');
    const editIdInput = document.getElementById('projectEditId');
    const feedback = document.getElementById('projectFormFeedback');
    const globalFeedback = document.getElementById('projectsGlobalFeedback');
    const tableBody = document.getElementById('projectsTableBody');
    const submitBtn = document.getElementById('projectSubmitBtn');
    const submitSpinner = document.getElementById('projectSubmitSpinner');
    const submitText = document.getElementById('projectSubmitText');
    const submitIcon = document.getElementById('projectSubmitIcon');
    const modalTitle = document.getElementById('createProjectModalLabel');
    const modalSubtitle = document.getElementById('projectModalSubtitle');
    const modalIcon = document.getElementById('projectModalHeaderIcon');
    const createBtn = document.querySelector('[data-bs-target="#createProjectModal"]');
    const storeUrl = form.dataset.storeUrl;
    const updateUrlTemplate = form.dataset.updateUrlTemplate;

    const statusClassMap = {
        capturado: 'ethos-status-capturado',
        clasificacion_pendiente: 'ethos-status-clasificacion-pendiente',
        priorizado: 'ethos-status-priorizado',
        asignacion_lider_pendiente: 'ethos-status-asignacion-lider-pendiente',
        en_diagnostico: 'ethos-status-en-diagnostico',
        en_diseno: 'ethos-status-en-diseno',
        en_implementacion: 'ethos-status-en-implementacion',
        en_seguimiento: 'ethos-status-en-seguimiento',
        cerrado: 'ethos-status-cerrado'
    };

    const statusIconMap = {
        capturado: 'ti ti-flag',
        clasificacion_pendiente: 'ti ti-hourglass-high',
        priorizado: 'ti ti-stars',
        asignacion_lider_pendiente: 'ti ti-user-question',
        en_diagnostico: 'ti ti-stethoscope',
        en_diseno: 'ti ti-pencil',
        en_implementacion: 'ti ti-settings-cog',
        en_seguimiento: 'ti ti-chart-line',
        cerrado: 'ti ti-circle-check'
    };

    const openCreateMode = () => {
        form.reset();
        form.classList.remove('was-validated');
        methodInput.value = 'POST';
        editIdInput.value = '';
        form.action = storeUrl;
        modalTitle.textContent = 'Crear Nuevo Proyecto';
        modalSubtitle.textContent = 'Define cliente, alcance y estado inicial del proyecto.';
        modalIcon.className = 'ti ti-briefcase-2';
        submitText.textContent = 'Guardar Proyecto';
        clearFeedback(feedback);
    };

    const openEditMode = (project) => {
        form.classList.remove('was-validated');
        methodInput.value = 'PUT';
        editIdInput.value = String(project.id);
        form.action = updateUrlTemplate.replace('__id__', String(project.id));
        form.elements.client_id.value = project.client_id || '';
        form.elements.title.value = project.title || '';
        form.elements.description.value = project.description || '';
        form.elements.status.value = project.status || 'capturado';
        form.elements.starts_at.value = project.starts_at_raw || '';
        form.elements.ends_at.value = project.ends_at_raw || '';
        modalTitle.textContent = 'Editar Proyecto';
        modalSubtitle.textContent = 'Actualiza la información del proyecto seleccionado.';
        modalIcon.className = 'ti ti-edit';
        submitText.textContent = 'Actualizar Proyecto';
        clearFeedback(feedback);
        modal.show();
    };

    const escapeHtml = (value) => {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    };

    const normalizeProject = (project) => ({
        id: project.id,
        client_id: project.client_id,
        client_name: project.client_name || '',
        client_name_label: project.client_name_label || 'Sin cliente',
        title: project.title || '',
        description: project.description || '',
        status: project.status || 'capturado',
        status_label: project.status_label || 'Capturado',
        starts_at_raw: project.starts_at_raw || '',
        ends_at_raw: project.ends_at_raw || '',
        starts_at_label: project.starts_at_label || 'Sin fecha',
        ends_at_label: project.ends_at_label || 'Sin fecha'
    });

    const projectRowHtml = (project) => {
        const statusClass = statusClassMap[project.status] || 'ethos-status-default';
        const statusIcon = statusIconMap[project.status] || 'ti ti-info-circle';
        const payload = escapeHtml(JSON.stringify({
            id: project.id,
            client_id: project.client_id,
            title: project.title,
            description: project.description,
            status: project.status,
            starts_at_raw: project.starts_at_raw,
            ends_at_raw: project.ends_at_raw
        }));
        const actionCell = canUpdate
            ? `<button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill ethos-action-btn js-edit-project" title="Editar proyecto" data-project="${payload}"><i class="ti ti-edit"></i></button>`
            : '';

        return `<tr data-project-id="${project.id}">
            <td data-label="Título">
                <div class="ethos-primary-cell">
                    <span class="ethos-cell-avatar"><i class="ti ti-briefcase-2"></i></span>
                    <span class="ethos-cell-text">${escapeHtml(project.title)}</span>
                </div>
            </td>
            <td data-label="Cliente">
                <span class="ethos-muted-cell"><i class="ti ti-building-skyscraper"></i><span>${escapeHtml(project.client_name_label)}</span></span>
            </td>
            <td data-label="Estado">
                <span class="ethos-status-badge ${statusClass}"><i class="${statusIcon}"></i><span>${escapeHtml(project.status_label)}</span></span>
            </td>
            <td data-label="Inicio">
                <span class="ethos-muted-cell"><i class="ti ti-calendar-event"></i><span>${escapeHtml(project.starts_at_label)}</span></span>
            </td>
            <td data-label="Fin">
                <span class="ethos-muted-cell"><i class="ti ti-calendar-check"></i><span>${escapeHtml(project.ends_at_label)}</span></span>
            </td>
            <td data-label="Acciones">${actionCell}</td>
        </tr>`;
    };

    const clearFeedback = (target) => {
        target.classList.add('d-none');
        target.classList.remove('alert-success', 'alert-danger');
        target.innerHTML = '';
    };

    const showFeedback = (target, type, message) => {
        target.classList.remove('d-none', 'alert-success', 'alert-danger');
        target.classList.add(type === 'success' ? 'alert-success' : 'alert-danger');
        target.innerHTML = message;
    };

    const setLoading = (loading) => {
        submitBtn.disabled = loading;
        modalElement.setAttribute('aria-busy', loading ? 'true' : 'false');
        submitSpinner.classList.toggle('d-none', !loading);
        submitIcon.classList.toggle('d-none', loading);
    };

    const removeEmptyState = () => {
        const emptyRow = tableBody.querySelector('.ethos-empty-state');
        if (emptyRow) {
            emptyRow.closest('tr')?.remove();
        }
    };

    const upsertProjectRow = (project, mode) => {
        const existing = tableBody.querySelector(`tr[data-project-id="${project.id}"]`);
        if (existing) {
            existing.outerHTML = projectRowHtml(project);
            const row = tableBody.querySelector(`tr[data-project-id="${project.id}"]`);
            row?.classList.add('ethos-row-highlight');
            setTimeout(() => row?.classList.remove('ethos-row-highlight'), 1400);
            return;
        }

        if (mode === 'create') {
            removeEmptyState();
            tableBody.insertAdjacentHTML('afterbegin', projectRowHtml(project));
            const firstRow = tableBody.querySelector(`tr[data-project-id="${project.id}"]`);
            firstRow?.classList.add('ethos-row-highlight');
            setTimeout(() => firstRow?.classList.remove('ethos-row-highlight'), 1400);
        }
    };

    const bindEditButtons = () => {
        tableBody.querySelectorAll('.js-edit-project').forEach((button) => {
            if (button.dataset.bound === '1') {
                return;
            }
            button.dataset.bound = '1';
            button.addEventListener('click', () => {
                const payload = button.dataset.project ? JSON.parse(button.dataset.project) : null;
                if (!payload) {
                    return;
                }
                openEditMode(payload);
            });
        });
    };

    createBtn?.addEventListener('click', openCreateMode);
    modalElement.addEventListener('hidden.bs.modal', () => {
        setLoading(false);
        clearFeedback(feedback);
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearFeedback(feedback);
        clearFeedback(globalFeedback);

        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            showFeedback(feedback, 'error', 'Revisa los campos obligatorios antes de continuar.');
            return;
        }

        setLoading(true);

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: formData
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                if (response.status === 422 && data.errors) {
                    const errors = Object.values(data.errors).flat().map((error) => `<li>${escapeHtml(error)}</li>`).join('');
                    showFeedback(feedback, 'error', `<ul class="mb-0 ps-3">${errors}</ul>`);
                } else {
                    showFeedback(feedback, 'error', escapeHtml(data.message || 'No se pudo guardar el proyecto.'));
                }
                return;
            }

            const normalized = normalizeProject(data.project || {});
            const mode = methodInput.value === 'PUT' ? 'edit' : 'create';
            upsertProjectRow(normalized, mode);
            bindEditButtons();
            showFeedback(globalFeedback, 'success', escapeHtml(data.message || 'Operación completada.'));
            modal.hide();
        } catch (error) {
            showFeedback(feedback, 'error', 'Ocurrió un error de conexión. Intenta nuevamente.');
        } finally {
            setLoading(false);
        }
    });

    bindEditButtons();
    openCreateMode();
})();
</script>
@endpush
