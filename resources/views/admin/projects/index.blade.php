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
                                    <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill ethos-action-btn js-view-project" title="Ver detalles" data-project-id="{{ $project->id }}" aria-label="Ver detalles del proyecto {{ $project->title }}">
                                        <i class="ti ti-eye"></i>
                                    </button>
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
                    {{ $projects->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade ethos-create-modal" id="createProjectModal" tabindex="-1" aria-labelledby="createProjectModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
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
          <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
              <div id="projectFormFeedback" class="alert d-none ethos-ajax-alert" role="alert" aria-live="assertive"></div>
              <div class="row g-3">
              <div class="col-md-6">
                  <label class="form-label"><i class="ti ti-building-skyscraper"></i> Cliente <span class="text-danger">*</span></label>
                  <select name="client_id" class="form-select" required>
                      <option value="">Seleccione un cliente</option>
                      @foreach($clients as $client)
                          <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                      @endforeach
                  </select>
                  <div class="invalid-feedback">Debes seleccionar un cliente.</div>
              </div>
              <div class="col-md-6">
                  <label class="form-label"><i class="ti ti-writing"></i> Título <span class="text-danger">*</span></label>
                  <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                  <div class="invalid-feedback">El título del proyecto es obligatorio.</div>
              </div>
              <div class="col-12">
                  <label class="form-label"><i class="ti ti-file-description"></i> Descripción</label>
                  <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
              </div>
              <div class="col-md-6">
                  <label class="form-label"><i class="ti ti-stack-2"></i> Estado <span class="text-danger">*</span></label>
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
                  <div class="invalid-feedback">Debes seleccionar un estado.</div>
              </div>
              </div>

              {{-- Sección: Clasificación --}}
              <div class="row g-3 mt-0">
                  <div class="col-12">
                      <h6 class="ethos-form-section-title"><i class="ti ti-category"></i> Clasificación</h6>
                  </div>
                  <div class="col-md-4">
                      <label class="form-label"><i class="ti ti-stack-2"></i> Tipo de Proyecto</label>
                      <input type="text" name="type" class="form-control" value="{{ old('type') }}" placeholder="">
                  </div>
                  <div class="col-md-4">
                      <label class="form-label"><i class="ti ti-subtask"></i> Subtipo</label>
                      <input type="text" name="subtype" class="form-control" value="{{ old('subtype') }}" placeholder="">
                  </div>
                  <div class="col-md-2">
                      <label class="form-label"><i class="ti ti-chart-dots"></i> Complejidad</label>
                      <select name="complexity" class="form-select">
                          <option value="">Sin definir</option>
                          <option value="baja" {{ old('complexity') == 'baja' ? 'selected' : '' }}>Baja</option>
                          <option value="media" {{ old('complexity') == 'media' ? 'selected' : '' }}>Media</option>
                          <option value="alta" {{ old('complexity') == 'alta' ? 'selected' : '' }}>Alta</option>
                      </select>
                  </div>
                  <div class="col-md-2">
                      <label class="form-label"><i class="ti ti-alert-triangle"></i> Urgencia</label>
                      <select name="urgency" class="form-select">
                          <option value="">Sin definir</option>
                          <option value="baja" {{ old('urgency') == 'baja' ? 'selected' : '' }}>Baja</option>
                          <option value="media" {{ old('urgency') == 'media' ? 'selected' : '' }}>Media</option>
                          <option value="alta" {{ old('urgency') == 'alta' ? 'selected' : '' }}>Alta</option>
                      </select>
                  </div>
              </div>

              {{-- Sección: Presupuesto --}}
              <div class="row g-3 mt-0">
                  <div class="col-12">
                      <h6 class="ethos-form-section-title"><i class="ti ti-currency-dollar"></i> Presupuesto</h6>
                  </div>
                  <div class="col-md-3">
                      <label class="form-label"><i class="ti ti-currency-dollar"></i> Moneda</label>
                      <select name="currency" class="form-select">
                          <option value="USD" {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>USD</option>
                          <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                          <option value="VES" {{ old('currency') == 'VES' ? 'selected' : '' }}>VES</option>
                      </select>
                  </div>
                  <div class="col-md-4">
                      <label class="form-label"><i class="ti ti-calculator"></i> Presupuesto Estimado</label>
                      <input type="number" name="estimated_budget" class="form-control" value="{{ old('estimated_budget') }}" step="0.01" min="0" placeholder="0.00">
                  </div>
                  <div class="col-md-4">
                      <label class="form-label"><i class="ti ti-receipt"></i> Presupuesto Final</label>
                      <input type="number" name="final_budget" class="form-control" value="{{ old('final_budget') }}" step="0.01" min="0" placeholder="0.00">
                  </div>
                  <div class="col-md-1 d-flex align-items-end">
                      <span class="form-text text-muted" id="budgetDiffDisplay" style="display:none;">
                          <i class="ti ti-arrow-right"></i>
                      </span>
                  </div>
              </div>

              {{-- Sección: Prioridad y Progreso --}}
              <div class="row g-3 mt-0">
                  <div class="col-12">
                      <h6 class="ethos-form-section-title"><i class="ti ti-star"></i> Prioridad y Progreso</h6>
                  </div>
                  <div class="col-md-4">
                      <label class="form-label"><i class="ti ti-number"></i> Score de Prioridad (1-10)</label>
                      <input type="number" name="priority_score" class="form-control" value="{{ old('priority_score') }}" min="1" max="10" step="1" placeholder="1 = Urgente, 10 = Baja">
                  </div>
                  <div class="col-md-4">
                      <label class="form-label"><i class="ti ti-barometer"></i> Nivel de Prioridad</label>
                      <select name="priority_level" class="form-select">
                          <option value="">Sin definir</option>
                          <option value="baja" {{ old('priority_level') == 'baja' ? 'selected' : '' }}>Baja</option>
                          <option value="media" {{ old('priority_level') == 'media' ? 'selected' : '' }}>Media</option>
                          <option value="alta" {{ old('priority_level') == 'alta' ? 'selected' : '' }}>Alta</option>
                      </select>
                  </div>
                  <div class="col-md-4">
                      <label class="form-label"><i class="ti ti-progress"></i> Progreso (%)</label>
                      <input type="range" name="progress" class="form-range" value="{{ old('progress', 0) }}" min="0" max="100" id="progressRange">
                      <div class="d-flex justify-content-between">
                          <span class="form-text">0%</span>
                          <span class="form-text fw-bold" id="progressValue">{{ old('progress', 0) }}%</span>
                          <span class="form-text">100%</span>
                      </div>
                  </div>
              </div>

              {{-- Sección: Responsables --}}
              <div class="row g-3 mt-0">
                  <div class="col-12">
                      <h6 class="ethos-form-section-title"><i class="ti ti-users"></i> Responsables</h6>
                  </div>
                  <div class="col-md-4">
                      <label class="form-label"><i class="ti ti-user-plus"></i> Asignado a</label>
                      <select name="assigned_to" class="form-select">
                          <option value="">Sin asignar</option>
                          @foreach($users as $user)
                              <option value="{{ $user->id }}">{{ $user->name }}</option>
                          @endforeach
                      </select>
                  </div>
                  <div class="col-md-4">
                      <label class="form-label"><i class="ti ti-user-check"></i> Validado por</label>
                      <select name="validated_by" class="form-select">
                          <option value="">Sin validar</option>
                          @foreach($users as $user)
                              <option value="{{ $user->id }}">{{ $user->name }}</option>
                          @endforeach
                      </select>
                  </div>
              </div>

              {{-- Sección: Fechas --}}
              <div class="row g-3 mt-0">
                  <div class="col-12">
                      <h6 class="ethos-form-section-title"><i class="ti ti-calendar"></i> Fechas</h6>
                  </div>
                  <div class="col-md-4">
                      <label class="form-label"><i class="ti ti-calendar-event"></i> Fecha de Inicio</label>
                      <input type="date" name="starts_at" class="form-control" value="{{ old('starts_at') }}">
                  </div>
                  <div class="col-md-4">
                      <label class="form-label"><i class="ti ti-calendar-check"></i> Fecha de Fin</label>
                      <input type="date" name="ends_at" class="form-control" value="{{ old('ends_at') }}">
                  </div>
                  <div class="col-md-4">
                      <label class="form-label"><i class="ti ti-calendar-star"></i> Fecha de Cierre</label>
                      <input type="date" name="finished_at" class="form-control" value="{{ old('finished_at') }}">
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

<!-- Modal de Detalles de Proyecto -->
<div class="modal fade ethos-detail-modal" id="projectDetailModal" tabindex="-1" aria-labelledby="projectDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="ethos-modal-title-wrap">
            <span class="ethos-modal-icon"><i class="ti ti-briefcase-2"></i></span>
            <div>
                <h5 class="modal-title" id="projectDetailModalLabel">Detalles del Proyecto</h5>
                <p class="mb-0 ethos-modal-subtitle" id="projectDetailSubtitle">Información completa del proyecto</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
          <div id="projectDetailContent">
              <div class="ethos-detail-loading">
                  <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Cargando...</span>
                  </div>
                  <p>Cargando detalles del proyecto...</p>
              </div>
          </div>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
            <i class="ti ti-x"></i>
            <span>Cerrar</span>
          </button>
      </div>
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
        // Básicos
        form.elements.client_id.value = project.client_id || '';
        form.elements.title.value = project.title || '';
        form.elements.description.value = project.description || '';
        form.elements.status.value = project.status || 'capturado';
        // Clasificación
        form.elements.type.value = project.type || '';
        form.elements.subtype.value = project.subtype || '';
        form.elements.complexity.value = project.complexity || '';
        form.elements.urgency.value = project.urgency || '';
        // Presupuesto
        form.elements.currency.value = project.currency || 'USD';
        form.elements.estimated_budget.value = project.estimated_budget || '';
        form.elements.final_budget.value = project.final_budget || '';
        // Prioridad y Progreso
        form.elements.priority_score.value = project.priority_score || '';
        form.elements.priority_level.value = project.priority_level || '';
        form.elements.progress.value = project.progress || 0;
        document.getElementById('progressValue').textContent = (project.progress || 0) + '%';
        // Responsables
        form.elements.assigned_to.value = project.assigned_to_id || '';
        form.elements.validated_by.value = project.validated_by_id || '';
        // Fechas
        form.elements.starts_at.value = project.starts_at_raw || '';
        form.elements.ends_at.value = project.ends_at_raw || '';
        form.elements.finished_at.value = project.finished_at_raw || '';
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
        // Clasificación
        type: project.type || '',
        type_label: project.type_label || 'Sin tipo',
        subtype: project.subtype || '',
        complexity: project.complexity || '',
        complexity_label: project.complexity_label || 'Sin definir',
        urgency: project.urgency || '',
        urgency_label: project.urgency_label || 'Sin definir',
        // Presupuesto
        estimated_budget: project.estimated_budget || '',
        estimated_budget_label: project.estimated_budget_label || 'Sin definir',
        final_budget: project.final_budget || '',
        final_budget_label: project.final_budget_label || 'Sin definir',
        budget_difference: project.budget_difference,
        budget_difference_label: project.budget_difference_label || 'N/A',
        currency: project.currency || 'USD',
        // Prioridad y Progreso
        priority_score: project.priority_score || '',
        priority_label: project.priority_label || 'Sin prioridad',
        priority_level: project.priority_level || '',
        progress: project.progress || 0,
        progress_percent: project.progress_percent || 0,
        // Responsables
        captured_by_id: project.captured_by_id || '',
        captured_by_name_label: project.captured_by_name_label || 'No asignado',
        assigned_to_id: project.assigned_to_id || '',
        assigned_to_name_label: project.assigned_to_name_label || 'No asignado',
        validated_by_id: project.validated_by_id || '',
        validated_by_name_label: project.validated_by_name_label || 'No validado',
        // Fechas
        starts_at_raw: project.starts_at_raw || '',
        ends_at_raw: project.ends_at_raw || '',
        finished_at_raw: project.finished_at_raw || '',
        starts_at_label: project.starts_at_label || 'Sin fecha',
        ends_at_label: project.ends_at_label || 'Sin fecha',
        finished_at_label: project.finished_at_label || 'Sin fecha',
        created_at: project.created_at || '',
        updated_at: project.updated_at || ''
    });

    const projectRowHtml = (project) => {
        const statusClass = statusClassMap[project.status] || 'ethos-status-default';
        const statusIcon = statusIconMap[project.status] || 'ti ti-info-circle';
        const payload = escapeHtml(JSON.stringify(project));
        const viewBtn = `<button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill ethos-action-btn js-view-project" title="Ver detalles" data-project-id="${project.id}"><i class="ti ti-eye"></i></button>`;
        const editBtn = canUpdate
            ? `<button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill ethos-action-btn js-edit-project" title="Editar proyecto" data-project="${payload}"><i class="ti ti-edit"></i></button>`
            : '';
        const actionCell = viewBtn + editBtn;

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

    // Modal de detalles
    const detailModalElement = document.getElementById('projectDetailModal');
    const detailModal = new bootstrap.Modal(detailModalElement);
    const detailContent = document.getElementById('projectDetailContent');
    const detailSubtitle = document.getElementById('projectDetailSubtitle');

    const renderProjectDetails = (project) => {
        const progressColor = project.progress_percent >= 75 ? 'bg-success' : project.progress_percent >= 50 ? 'bg-info' : project.progress_percent >= 25 ? 'bg-warning' : 'bg-secondary';
        const urgencyBadge = project.urgency === 'alta' ? 'badge bg-danger' : project.urgency === 'media' ? 'badge bg-warning text-dark' : 'badge bg-secondary';
        const complexityBadge = project.complexity === 'alta' ? 'badge bg-danger' : project.complexity === 'media' ? 'badge bg-warning text-dark' : 'badge bg-secondary';
        
        return `
            <div class="ethos-detail-sections">
                <div class="ethos-detail-section">
                    <h6 class="ethos-detail-section-title">
                        <i class="ti ti-info-circle"></i>
                        <span>Información General</span>
                    </h6>
                    <div class="ethos-detail-grid">
                        <div class="ethos-detail-field">
                            <label>Título</label>
                            <span>${escapeHtml(project.title)}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Cliente</label>
                            <span>${escapeHtml(project.client_name_label)}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Estado</label>
                            <span class="ethos-status-badge ethos-status-${project.status}">${escapeHtml(project.status_label)}</span>
                        </div>
                    </div>
                </div>

                <div class="ethos-detail-section">
                    <h6 class="ethos-detail-section-title">
                        <i class="ti ti-category"></i>
                        <span>Clasificación</span>
                    </h6>
                    <div class="ethos-detail-grid">
                        <div class="ethos-detail-field">
                            <label>Tipo</label>
                            <span>${escapeHtml(project.type_label)}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Subtipo</label>
                            <span>${escapeHtml(project.subtype || 'Sin definir')}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Complejidad</label>
                            <span class="${complexityBadge}">${escapeHtml(project.complexity_label)}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Urgencia</label>
                            <span class="${urgencyBadge}">${escapeHtml(project.urgency_label)}</span>
                        </div>
                    </div>
                </div>

                <div class="ethos-detail-section">
                    <h6 class="ethos-detail-section-title">
                        <i class="ti ti-currency-dollar"></i>
                        <span>Presupuesto</span>
                    </h6>
                    <div class="ethos-detail-grid">
                        <div class="ethos-detail-field">
                            <label>Moneda</label>
                            <span>${escapeHtml(project.currency)}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Estimado</label>
                            <span>${escapeHtml(project.estimated_budget_label)}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Final</label>
                            <span>${escapeHtml(project.final_budget_label)}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Diferencia</label>
                            <span class="${project.budget_difference > 0 ? 'text-danger' : project.budget_difference < 0 ? 'text-success' : ''}">${escapeHtml(project.budget_difference_label)}</span>
                        </div>
                    </div>
                </div>

                <div class="ethos-detail-section">
                    <h6 class="ethos-detail-section-title">
                        <i class="ti ti-star"></i>
                        <span>Prioridad y Progreso</span>
                    </h6>
                    <div class="ethos-detail-grid">
                        <div class="ethos-detail-field">
                            <label>Score</label>
                            <span>${escapeHtml(project.priority_score || 'Sin definir')}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Prioridad</label>
                            <span>${escapeHtml(project.priority_label)}</span>
                        </div>
                        <div class="ethos-detail-field ethos-detail-field-full">
                            <label>Progreso</label>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar ${progressColor}" role="progressbar" style="width: ${project.progress_percent}%;" aria-valuenow="${project.progress_percent}" aria-valuemin="0" aria-valuemax="100">${project.progress_percent}%</div>
                            </div>
                        </div>
                    </div>
                </div>

                ${project.description ? `
                <div class="ethos-detail-section">
                    <h6 class="ethos-detail-section-title">
                        <i class="ti ti-file-description"></i>
                        <span>Descripción</span>
                    </h6>
                    <div class="ethos-detail-notes">
                        <p class="mb-0">${escapeHtml(project.description)}</p>
                    </div>
                </div>
                ` : ''}

                <div class="ethos-detail-section">
                    <h6 class="ethos-detail-section-title">
                        <i class="ti ti-users"></i>
                        <span>Equipo Responsable</span>
                    </h6>
                    <div class="ethos-detail-grid">
                        <div class="ethos-detail-field">
                            <label>Capturado por</label>
                            <span>${escapeHtml(project.captured_by_name_label)}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Asignado a</label>
                            <span>${escapeHtml(project.assigned_to_name_label)}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Validado por</label>
                            <span>${escapeHtml(project.validated_by_name_label)}</span>
                        </div>
                    </div>
                </div>

                <div class="ethos-detail-section">
                    <h6 class="ethos-detail-section-title">
                        <i class="ti ti-calendar"></i>
                        <span>Fechas</span>
                    </h6>
                    <div class="ethos-detail-grid">
                        <div class="ethos-detail-field">
                            <label>Fecha de Inicio</label>
                            <span>${escapeHtml(project.starts_at_label)}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Fecha de Fin</label>
                            <span>${escapeHtml(project.ends_at_label)}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Fecha de Cierre</label>
                            <span>${escapeHtml(project.finished_at_label)}</span>
                        </div>
                    </div>
                </div>

                <div class="ethos-detail-section ethos-detail-timestamps">
                    <div class="ethos-detail-timestamp">
                        <i class="ti ti-calendar-plus"></i>
                        <span>Creado: ${escapeHtml(project.created_at || 'N/A')}</span>
                    </div>
                    <div class="ethos-detail-timestamp">
                        <i class="ti ti-calendar-check"></i>
                        <span>Actualizado: ${escapeHtml(project.updated_at || 'N/A')}</span>
                    </div>
                </div>
            </div>
        `;
    };

    const loadProjectDetails = async (projectId) => {
        detailContent.innerHTML = `
            <div class="ethos-detail-loading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p>Cargando detalles del proyecto...</p>
            </div>
        `;

        try {
            const response = await fetch(`/admin/projects/${projectId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (!response.ok) {
                throw new Error('No se pudieron cargar los detalles');
            }

            const data = await response.json();
            detailSubtitle.textContent = data.project.title;
            detailContent.innerHTML = renderProjectDetails(data.project);
        } catch (error) {
            detailContent.innerHTML = `
                <div class="ethos-detail-error">
                    <i class="ti ti-alert-triangle"></i>
                    <p>No se pudieron cargar los detalles del proyecto.</p>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="this.closest('.modal').querySelector('.btn-close').click()">
                        Cerrar
                    </button>
                </div>
            `;
        }
    };

    const bindViewButtons = () => {
        tableBody.querySelectorAll('.js-view-project').forEach((button) => {
            if (button.dataset.bound === '1') {
                return;
            }
            button.dataset.bound = '1';
            button.addEventListener('click', () => {
                const projectId = button.dataset.projectId;
                if (!projectId) {
                    return;
                }
                loadProjectDetails(projectId);
                detailModal.show();
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
    bindViewButtons();
    openCreateMode();

    // Progress slider live update
    const progressRange = document.getElementById('progressRange');
    const progressValue = document.getElementById('progressValue');
    if (progressRange && progressValue) {
        progressRange.addEventListener('input', () => {
            progressValue.textContent = progressRange.value + '%';
        });
    }
})();
</script>
@endpush

@push('styles')
<style>
.ethos-form-section-title {
    font-size: 0.85rem;
    font-weight: 600;
    color: #5d596c;
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e7e5eb;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.ethos-form-section-title i {
    font-size: 1rem;
    color: #7c7a8d;
}
.ethos-detail-field-full {
    grid-column: 1 / -1;
}
</style>
@endpush
