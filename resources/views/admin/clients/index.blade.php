@extends('layouts.vuexy')

@section('title', 'Clientes')

@section('content')
@php
    $canUpdateClients = auth()->user()?->can('clients.update');
@endphp
<div class="row">
    <div class="col-12">
        <div class="card ethos-crm-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 d-flex align-items-center gap-2">
                    <i class="ti ti-users"></i>
                    <span>Clientes</span>
                </h5>
                @can('clients.create')
                <button type="button" class="btn btn-primary ethos-create-btn" data-bs-toggle="modal" data-bs-target="#createClientModal">
                    <i class="ti ti-plus"></i>
                    <span>Nuevo Cliente</span>
                </button>
                @endcan
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div id="clientsGlobalFeedback" class="alert d-none ethos-ajax-alert" role="alert" aria-live="polite"></div>
                <div class="table-responsive ethos-table-shell">
                    <table class="table table-hover align-middle ethos-data-table ethos-data-table-clients" id="clientsTable">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Industria</th>
                                <th>Contacto Principal</th>
                                <th>Email</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="clientsTableBody">
                            @forelse($clients as $client)
                            @php
                                $clientPayload = [
                                    'id' => $client->id,
                                    'name' => $client->name,
                                    'industry' => $client->industry,
                                    'primary_contact_name' => $client->primary_contact_name,
                                    'primary_contact_email' => $client->primary_contact_email,
                                    'notes' => $client->notes,
                                ];
                            @endphp
                            <tr data-client-id="{{ $client->id }}">
                                <td data-label="Nombre">
                                    <div class="ethos-primary-cell">
                                        <span class="ethos-cell-avatar">
                                            <i class="ti ti-building-skyscraper"></i>
                                        </span>
                                        <span class="ethos-cell-text">{{ $client->name }}</span>
                                    </div>
                                </td>
                                <td data-label="Industria">
                                    <span class="ethos-pill">{{ $client->industry ?? 'Sin industria' }}</span>
                                </td>
                                <td data-label="Contacto Principal">
                                    <span class="ethos-muted-cell">
                                        <i class="ti ti-user"></i>
                                        <span>{{ $client->primary_contact_name ?? 'Sin contacto' }}</span>
                                    </span>
                                </td>
                                <td data-label="Email">
                                    <span class="ethos-muted-cell">
                                        <i class="ti ti-mail"></i>
                                        <span>{{ $client->primary_contact_email ?? 'Sin email' }}</span>
                                    </span>
                                </td>
                                <td data-label="Acciones">
                                    <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill ethos-action-btn js-view-client" title="Ver detalles" data-client-id="{{ $client->id }}" aria-label="Ver detalles del cliente {{ $client->name }}">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                    @can('clients.update')
                                    <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill ethos-action-btn js-edit-client" title="Editar cliente" data-client='@json($clientPayload)'>
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center ethos-empty-state">
                                    <i class="ti ti-users-off"></i>
                                    <span>No hay clientes registrados.</span>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $clients->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade ethos-create-modal" id="createClientModal" tabindex="-1" aria-labelledby="createClientModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="ethos-modal-title-wrap">
            <span class="ethos-modal-icon"><i class="ti ti-user-plus" id="clientModalHeaderIcon"></i></span>
            <div>
                <h5 class="modal-title" id="createClientModalLabel">Registrar Nuevo Cliente</h5>
                <p class="mb-0 ethos-modal-subtitle" id="clientModalSubtitle">Completa los datos principales del cliente.</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="clientUnifiedForm" action="{{ route('clients.store') }}" method="POST" data-store-url="{{ route('clients.store') }}" data-update-url-template="{{ route('clients.update', '__id__') }}" novalidate>
          @csrf
          <input type="hidden" name="_method" id="clientFormMethod" value="POST">
          <input type="hidden" id="clientEditId" value="">
          <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
              <div id="clientFormFeedback" class="alert d-none ethos-ajax-alert" role="alert" aria-live="assertive"></div>
              <div class="row g-3">
              <div class="col-md-6">
                  <label class="form-label"><i class="ti ti-building"></i> Nombre <span class="text-danger">*</span></label>
                  <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                  <div class="invalid-feedback">El nombre del cliente es obligatorio.</div>
              </div>
              <div class="col-md-6">
                  <label class="form-label"><i class="ti ti-briefcase"></i> Industria</label>
                  <input type="text" name="industry" class="form-control" value="{{ old('industry') }}">
              </div>
              <div class="col-md-6">
                  <label class="form-label"><i class="ti ti-user"></i> Nombre Contacto Principal</label>
                  <input type="text" name="primary_contact_name" class="form-control" value="{{ old('primary_contact_name') }}">
              </div>
              <div class="col-md-6">
                  <label class="form-label"><i class="ti ti-at"></i> Email Contacto Principal</label>
                  <input type="email" name="primary_contact_email" class="form-control" value="{{ old('primary_contact_email') }}">
                  <div class="invalid-feedback">Por favor ingresa un email válido.</div>
              </div>
              <div class="col-md-6">
                  <label class="form-label"><i class="ti ti-phone"></i> Teléfono</label>
                  <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+58 412-1234567">
              </div>
              <div class="col-12">
                  <label class="form-label"><i class="ti ti-notes"></i> Notas</label>
                  <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
              </div>
              </div>

              {{-- Sección: Ubicación --}}
              <div class="row g-3 mt-0">
                  <div class="col-12">
                      <h6 class="ethos-form-section-title"><i class="ti ti-map-pin"></i> Ubicación</h6>
                  </div>
                  <div class="col-md-4">
                      <label class="form-label"><i class="ti ti-building"></i> Estado/Provincia *</label>
                      <select name="state" id="stateSelect" class="form-select" required>
                          <option value="">Selecciona un estado</option>
                          <option value="Distrito Capital">Distrito Capital</option>
                          <option value="Amazonas">Amazonas</option>
                          <option value="Anzoátegui">Anzoátegui</option>
                          <option value="Apure">Apure</option>
                          <option value="Aragua">Aragua</option>
                          <option value="Barinas">Barinas</option>
                          <option value="Bolívar">Bolívar</option>
                          <option value="Carabobo">Carabobo</option>
                          <option value="Cojedes">Cojedes</option>
                          <option value="Delta Amacuro">Delta Amacuro</option>
                          <option value="Falcón">Falcón</option>
                          <option value="Guárico">Guárico</option>
                          <option value="Lara">Lara</option>
                          <option value="Mérida">Mérida</option>
                          <option value="Miranda">Miranda</option>
                          <option value="Monagas">Monagas</option>
                          <option value="Nueva Esparta">Nueva Esparta</option>
                          <option value="Portuguesa">Portuguesa</option>
                          <option value="Sucre">Sucre</option>
                          <option value="Táchira">Táchira</option>
                          <option value="Trujillo">Trujillo</option>
                          <option value="Vargas">Vargas</option>
                          <option value="Yaracuy">Yaracuy</option>
                          <option value="Zulia">Zulia</option>
                      </select>
                  </div>
                  <div class="col-md-4">
                      <label class="form-label"><i class="ti ti-building-community"></i> Ciudad</label>
                      <select name="city" id="citySelect" class="form-select">
                          <option value="">Primero selecciona un estado</option>
                      </select>
                  </div>
                  <div class="col-md-4">
                      <label class="form-label"><i class="ti ti-map-2"></i> Municipio</label>
                      <select name="municipality" id="municipalitySelect" class="form-select">
                          <option value="">Primero selecciona un estado</option>
                      </select>
                  </div>
                  <div class="col-md-4">
                      <label class="form-label"><i class="ti ti-location"></i> Parroquia</label>
                      <select name="parish" id="parishSelect" class="form-select">
                          <option value="">Primero selecciona un municipio</option>
                      </select>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label"><i class="ti ti-map"></i> Dirección</label>
                      <input type="text" name="address" class="form-control" value="{{ old('address') }}" placeholder="Calle, número, zona">
                  </div>
                  <div class="col-md-2">
                      <label class="form-label"><i class="ti ti-globe"></i> País</label>
                      <input type="text" name="country" class="form-control" value="Venezuela" readonly>
                  </div>
              </div>

              {{-- Sección: Ubicación en Mapa --}}
              <div class="row g-3 mt-0">
                  <div class="col-12">
                      <h6 class="ethos-form-section-title"><i class="ti ti-map-2"></i> Ubicación en Mapa</h6>
                      <p class="text-muted small mb-2">Haz clic en el mapa para seleccionar la ubicación del cliente</p>
                  </div>
                  <div class="col-12">
                      <div id="clientMapPicker" style="height: 300px; width: 100%; border-radius: 8px; border: 1px solid #e7e5eb;"></div>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label"><i class="ti ti-compass"></i> Latitud</label>
                      <input type="text" name="latitude" id="latitudeDisplay" class="form-control" value="{{ old('latitude') }}" readonly placeholder="Haz clic en el mapa">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label"><i class="ti ti-compass"></i> Longitud</label>
                      <input type="text" name="longitude" id="longitudeDisplay" class="form-control" value="{{ old('longitude') }}" readonly placeholder="Haz clic en el mapa">
                  </div>
                  <div class="col-12">
                      <button type="button" class="btn btn-sm btn-outline-secondary" id="clearLocationBtn">
                          <i class="ti ti-trash"></i> Limpiar ubicación
                      </button>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                <i class="ti ti-x"></i>
                <span>Cancelar</span>
              </button>
              <button type="submit" class="btn btn-primary ethos-submit-btn" id="clientSubmitBtn">
                <span class="spinner-border spinner-border-sm d-none" id="clientSubmitSpinner" role="status" aria-hidden="true"></span>
                <i class="ti ti-device-floppy" id="clientSubmitIcon"></i>
                <span id="clientSubmitText">Guardar Cliente</span>
              </button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal de Detalles de Cliente -->
<div class="modal fade ethos-detail-modal" id="clientDetailModal" tabindex="-1" aria-labelledby="clientDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div class="ethos-modal-title-wrap">
            <span class="ethos-modal-icon"><i class="ti ti-building-skyscraper"></i></span>
            <div>
                <h5 class="modal-title" id="clientDetailModalLabel">Detalles del Cliente</h5>
                <p class="mb-0 ethos-modal-subtitle" id="clientDetailSubtitle">Información completa del cliente</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
          <div id="clientDetailContent">
              <div class="ethos-detail-loading">
                  <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Cargando...</span>
                  </div>
                  <p>Cargando detalles del cliente...</p>
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
    const canUpdate = @json($canUpdateClients);
    const modalElement = document.getElementById('createClientModal');
    const modal = new bootstrap.Modal(modalElement);
    const form = document.getElementById('clientUnifiedForm');
    const methodInput = document.getElementById('clientFormMethod');
    const editIdInput = document.getElementById('clientEditId');
    const feedback = document.getElementById('clientFormFeedback');
    const globalFeedback = document.getElementById('clientsGlobalFeedback');
    const tableBody = document.getElementById('clientsTableBody');
    const submitBtn = document.getElementById('clientSubmitBtn');
    const submitSpinner = document.getElementById('clientSubmitSpinner');
    const submitText = document.getElementById('clientSubmitText');
    const submitIcon = document.getElementById('clientSubmitIcon');
    const modalTitle = document.getElementById('createClientModalLabel');
    const modalSubtitle = document.getElementById('clientModalSubtitle');
    const modalIcon = document.getElementById('clientModalHeaderIcon');
    const createBtn = document.querySelector('[data-bs-target="#createClientModal"]');
    const storeUrl = form.dataset.storeUrl;
    const updateUrlTemplate = form.dataset.updateUrlTemplate;

    const openCreateMode = () => {
        form.reset();
        form.classList.remove('was-validated');
        methodInput.value = 'POST';
        editIdInput.value = '';
        form.action = storeUrl;
        modalTitle.textContent = 'Registrar Nuevo Cliente';
        modalSubtitle.textContent = 'Completa los datos principales del cliente.';
        modalIcon.className = 'ti ti-user-plus';
        submitText.textContent = 'Guardar Cliente';
        clearFeedback(feedback);
    };

    const openEditMode = (client) => {
        form.classList.remove('was-validated');
        methodInput.value = 'PUT';
        editIdInput.value = String(client.id);
        form.action = updateUrlTemplate.replace('__id__', String(client.id));
        // Básicos
        form.elements.name.value = client.name || '';
        form.elements.industry.value = client.industry || '';
        form.elements.primary_contact_name.value = client.primary_contact_name || '';
        form.elements.primary_contact_email.value = client.primary_contact_email || '';
        form.elements.phone.value = client.phone || '';
        form.elements.notes.value = client.notes || '';
        // Ubicación
        form.elements.address.value = client.address || '';
        // Set state first to trigger population of dependent selects
        const stateValue = client.state || '';
        form.elements.state.value = stateValue;
        if (stateValue && locationData[stateValue]) {
            // Trigger change to populate cities and municipalities
            const data = locationData[stateValue];
            populateSelect(citySelect, data.cities, 'Selecciona una ciudad');
            populateSelect(municipalitySelect, data.municipalities, 'Selecciona un municipio');
            populateSelect(parishSelect, [], 'Primero selecciona un municipio');
            // Now set the actual values
            citySelect.value = client.city || '';
            municipalitySelect.value = client.municipality || '';
            // Trigger municipality change to populate parishes
            if (client.municipality && data.parishes?.[client.municipality]) {
                const parishes = data.parishes[client.municipality];
                populateSelect(parishSelect, Array.isArray(parishes) ? parishes : [parishes], 'Selecciona una parroquia');
                parishSelect.value = client.parish || '';
            }
        }
        form.elements.country.value = client.country || 'Venezuela';
        // Coordenadas
        form.elements.latitude.value = client.latitude || '';
        form.elements.longitude.value = client.longitude || '';
        document.getElementById('latitudeDisplay').value = client.latitude || '';
        document.getElementById('longitudeDisplay').value = client.longitude || '';
        modalTitle.textContent = 'Editar Cliente';
        modalSubtitle.textContent = 'Actualiza la información del cliente seleccionado.';
        modalIcon.className = 'ti ti-edit';
        submitText.textContent = 'Actualizar Cliente';
        clearFeedback(feedback);
        modal.show();

        // Set map marker if coordinates exist
        setTimeout(() => {
            if (client.latitude && client.longitude && mapPicker) {
                const position = { lat: parseFloat(client.latitude), lng: parseFloat(client.longitude) };
                mapPicker.setCenter(position);
                if (mapMarker) {
                    mapMarker.setPosition(position);
                } else {
                    mapMarker = new google.maps.Marker({
                        position: position,
                        map: mapPicker,
                        draggable: true,
                        title: 'Ubicación del cliente',
                    });
                    mapMarker.addListener('dragend', () => {
                        const pos = mapMarker.getPosition();
                        document.getElementById('latitudeDisplay').value = pos.lat().toFixed(6);
                        document.getElementById('longitudeDisplay').value = pos.lng().toFixed(6);
                        form.elements.latitude.value = pos.lat().toFixed(6);
                        form.elements.longitude.value = pos.lng().toFixed(6);
                    });
                }
            } else if (mapMarker) {
                mapMarker.setMap(null);
                mapMarker = null;
            }
        }, 300);
    };

    const escapeHtml = (value) => {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    };

    const normalizeClient = (client) => ({
        id: client.id,
        name: client.name || '',
        industry: client.industry || '',
        industry_label: client.industry_label || 'Sin industria',
        primary_contact_name: client.primary_contact_name || '',
        primary_contact_name_label: client.primary_contact_name_label || 'Sin contacto',
        primary_contact_email: client.primary_contact_email || '',
        primary_contact_email_label: client.primary_contact_email_label || 'Sin email',
        phone: client.phone || '',
        notes: client.notes || '',
        // Ubicación
        address: client.address || '',
        city: client.city || '',
        state: client.state || '',
        country: client.country || '',
        municipality: client.municipality || '',
        parish: client.parish || '',
        // Coordenadas
        latitude: client.latitude || '',
        longitude: client.longitude || ''
    });

    const clientRowHtml = (client) => {
        const payload = escapeHtml(JSON.stringify(client));
        const viewBtn = `<button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill ethos-action-btn js-view-client" title="Ver detalles" data-client-id="${client.id}"><i class="ti ti-eye"></i></button>`;
        const editBtn = canUpdate
            ? `<button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill ethos-action-btn js-edit-client" title="Editar cliente" data-client="${payload}"><i class="ti ti-edit"></i></button>`
            : '';
        const actionCell = viewBtn + editBtn;

        return `<tr data-client-id="${client.id}">
            <td data-label="Nombre">
                <div class="ethos-primary-cell">
                    <span class="ethos-cell-avatar"><i class="ti ti-building-skyscraper"></i></span>
                    <span class="ethos-cell-text">${escapeHtml(client.name)}</span>
                </div>
            </td>
            <td data-label="Industria"><span class="ethos-pill">${escapeHtml(client.industry_label)}</span></td>
            <td data-label="Contacto Principal">
                <span class="ethos-muted-cell"><i class="ti ti-user"></i><span>${escapeHtml(client.primary_contact_name_label)}</span></span>
            </td>
            <td data-label="Email">
                <span class="ethos-muted-cell"><i class="ti ti-mail"></i><span>${escapeHtml(client.primary_contact_email_label)}</span></span>
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

    const upsertClientRow = (client, mode) => {
        const existing = tableBody.querySelector(`tr[data-client-id="${client.id}"]`);
        if (existing) {
            existing.outerHTML = clientRowHtml(client);
            const row = tableBody.querySelector(`tr[data-client-id="${client.id}"]`);
            row?.classList.add('ethos-row-highlight');
            setTimeout(() => row?.classList.remove('ethos-row-highlight'), 1400);
            return;
        }

        if (mode === 'create') {
            removeEmptyState();
            tableBody.insertAdjacentHTML('afterbegin', clientRowHtml(client));
            const firstRow = tableBody.querySelector(`tr[data-client-id="${client.id}"]`);
            firstRow?.classList.add('ethos-row-highlight');
            setTimeout(() => firstRow?.classList.remove('ethos-row-highlight'), 1400);
        }
    };

    const bindEditButtons = () => {
        tableBody.querySelectorAll('.js-edit-client').forEach((button) => {
            if (button.dataset.bound === '1') {
                return;
            }
            button.dataset.bound = '1';
            button.addEventListener('click', () => {
                const payload = button.dataset.client ? JSON.parse(button.dataset.client) : null;
                if (!payload) {
                    return;
                }
                openEditMode(payload);
            });
        });
    };

    // Modal de detalles
    const detailModalElement = document.getElementById('clientDetailModal');
    const detailModal = new bootstrap.Modal(detailModalElement);
    const detailContent = document.getElementById('clientDetailContent');
    const detailSubtitle = document.getElementById('clientDetailSubtitle');

    const renderClientDetails = (client) => {
        const projectsHtml = client.projects && client.projects.length > 0
            ? client.projects.map(p => `
                <div class="ethos-detail-item">
                    <div class="ethos-detail-item-main">
                        <i class="ti ti-briefcase-2"></i>
                        <span>${escapeHtml(p.title)}</span>
                    </div>
                    <span class="ethos-status-badge ethos-status-${p.status}">${escapeHtml(p.status_label)}</span>
                </div>
            `).join('')
            : '<p class="text-muted mb-0">Este cliente no tiene proyectos registrados.</p>';

        return `
            <div class="ethos-detail-sections">
                <div class="ethos-detail-section">
                    <h6 class="ethos-detail-section-title">
                        <i class="ti ti-building"></i>
                        <span>Información General</span>
                    </h6>
                    <div class="ethos-detail-grid">
                        <div class="ethos-detail-field">
                            <label>Nombre</label>
                            <span>${escapeHtml(client.name)}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Industria</label>
                            <span>${escapeHtml(client.industry_label)}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Tipo de Negocio</label>
                            <span>${escapeHtml(client.business_type_label)}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Sitio Web</label>
                            <span>${client.website ? `<a href="${escapeHtml(client.website)}" target="_blank" rel="noopener">${escapeHtml(client.website)}</a>` : 'No especificado'}</span>
                        </div>
                    </div>
                </div>

                <div class="ethos-detail-section">
                    <h6 class="ethos-detail-section-title">
                        <i class="ti ti-user"></i>
                        <span>Contacto Principal</span>
                    </h6>
                    <div class="ethos-detail-grid">
                        <div class="ethos-detail-field">
                            <label>Nombre</label>
                            <span>${escapeHtml(client.primary_contact_name_label)}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Email</label>
                            <span>${client.primary_contact_email ? `<a href="mailto:${escapeHtml(client.primary_contact_email)}">${escapeHtml(client.primary_contact_email)}</a>` : escapeHtml(client.primary_contact_email_label)}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Teléfono</label>
                            <span>${client.primary_contact_phone ? `<a href="tel:${escapeHtml(client.primary_contact_phone)}">${escapeHtml(client.primary_contact_phone)}</a>` : escapeHtml(client.primary_contact_phone_label)}</span>
                        </div>
                    </div>
                </div>

                ${client.secondary_contact_name ? `
                <div class="ethos-detail-section">
                    <h6 class="ethos-detail-section-title">
                        <i class="ti ti-users"></i>
                        <span>Contacto Secundario</span>
                    </h6>
                    <div class="ethos-detail-grid">
                        <div class="ethos-detail-field">
                            <label>Nombre</label>
                            <span>${escapeHtml(client.secondary_contact_name)}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Email</label>
                            <span>${client.secondary_contact_email ? `<a href="mailto:${escapeHtml(client.secondary_contact_email)}">${escapeHtml(client.secondary_contact_email)}</a>` : 'No especificado'}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Teléfono</label>
                            <span>${client.secondary_contact_phone ? `<a href="tel:${escapeHtml(client.secondary_contact_phone)}">${escapeHtml(client.secondary_contact_phone)}</a>` : 'No especificado'}</span>
                        </div>
                    </div>
                </div>
                ` : ''}

                <div class="ethos-detail-section">
                    <h6 class="ethos-detail-section-title">
                        <i class="ti ti-map-pin"></i>
                        <span>Ubicación</span>
                    </h6>
                    <div class="ethos-detail-grid">
                        <div class="ethos-detail-field">
                            <label>Dirección</label>
                            <span>${client.address ? escapeHtml(client.address) : 'No especificada'}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Ciudad</label>
                            <span>${client.city ? escapeHtml(client.city) : 'No especificada'}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Estado/Provincia</label>
                            <span>${client.state ? escapeHtml(client.state) : 'No especificado'}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>País</label>
                            <span>${client.country ? escapeHtml(client.country) : 'No especificado'}</span>
                        </div>
                        <div class="ethos-detail-field">
                            <label>Código Postal</label>
                            <span>${client.postal_code ? escapeHtml(client.postal_code) : 'No especificado'}</span>
                        </div>
                    </div>
                </div>

                ${client.notes ? `
                <div class="ethos-detail-section">
                    <h6 class="ethos-detail-section-title">
                        <i class="ti ti-notes"></i>
                        <span>Notas</span>
                    </h6>
                    <div class="ethos-detail-notes">
                        <p class="mb-0">${escapeHtml(client.notes)}</p>
                    </div>
                </div>
                ` : ''}

                <div class="ethos-detail-section">
                    <h6 class="ethos-detail-section-title">
                        <i class="ti ti-briefcase"></i>
                        <span>Proyectos (${client.projects_count || 0})</span>
                    </h6>
                    <div class="ethos-detail-projects">
                        ${projectsHtml}
                    </div>
                </div>

                <div class="ethos-detail-section ethos-detail-timestamps">
                    <div class="ethos-detail-timestamp">
                        <i class="ti ti-calendar-plus"></i>
                        <span>Creado: ${escapeHtml(client.created_at || 'N/A')}</span>
                    </div>
                    <div class="ethos-detail-timestamp">
                        <i class="ti ti-calendar-check"></i>
                        <span>Actualizado: ${escapeHtml(client.updated_at || 'N/A')}</span>
                    </div>
                </div>
            </div>
        `;
    };

    const loadClientDetails = async (clientId) => {
        detailContent.innerHTML = `
            <div class="ethos-detail-loading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p>Cargando detalles del cliente...</p>
            </div>
        `;

        try {
            const response = await fetch(`/admin/clients/${clientId}`, {
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
            detailSubtitle.textContent = data.client.name;
            detailContent.innerHTML = renderClientDetails(data.client);
        } catch (error) {
            detailContent.innerHTML = `
                <div class="ethos-detail-error">
                    <i class="ti ti-alert-triangle"></i>
                    <p>No se pudieron cargar los detalles del cliente.</p>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="this.closest('.modal').querySelector('.btn-close').click()">
                        Cerrar
                    </button>
                </div>
            `;
        }
    };

    const bindViewButtons = () => {
        tableBody.querySelectorAll('.js-view-client').forEach((button) => {
            if (button.dataset.bound === '1') {
                return;
            }
            button.dataset.bound = '1';
            button.addEventListener('click', () => {
                const clientId = button.dataset.clientId;
                if (!clientId) {
                    return;
                }
                loadClientDetails(clientId);
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
                    showFeedback(feedback, 'error', escapeHtml(data.message || 'No se pudo guardar el cliente.'));
                }
                return;
            }

            const normalized = normalizeClient(data.client || {});
            const mode = methodInput.value === 'PUT' ? 'edit' : 'create';
            upsertClientRow(normalized, mode);
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

    // Smart Location Selects (Venezuela)
    const locationData = {
        'Distrito Capital': {
            cities: ['Caracas', 'Baruta', 'Chacao', 'El Hatillo', 'Libertador'],
            municipalities: ['Baruta', 'Chacao', 'El Hatillo', 'Libertador'],
            parishes: {
                'Baruta': ['Baruta', 'El Cafetal', 'Las Minas'],
                'Chacao': ['Chacao'],
                'El Hatillo': ['El Hatillo'],
                'Libertador': ['Altagracia', 'Antímano', 'Caricuao', 'Catedral', 'Coche', 'El Junquito', 'El Paraíso', 'El Recreo', 'El Valle', 'La Candelaria', 'La Pastora', 'La Vega', 'Macarao', 'San Agustín', 'San Bernardino', 'San José', 'San Juan', 'San Pedro', 'Santa Rosalía', 'Santa Teresa', 'Sucre']
            }
        },
        'Miranda': {
            cities: ['Los Teques', 'San Antonio de Los Altos', 'Guarenas', 'Guatire', 'Petare', 'Baruta', 'Cúa', 'Santa Lucía'],
            municipalities: ['Acevedo', 'Andrés Bello', 'Baruta', 'Brión', 'Buroz', 'Carrizal', 'Chacao', 'Cristóbal Rojas', 'El Hatillo', 'Guaicaipuro', 'Independencia', 'Lander', 'Los Salias', 'Páez', 'Pedro Gual', 'Plaza', 'Simón Bolívar', 'Sucre', 'Urdaneta', 'Zamora'],
            parishes: {
                'Acevedo': ['Aragüita', 'Aragüita Abajo'],
                'Andrés Bello': ['Araira'],
                'Baruta': ['Baruta', 'El Cafetal', 'Las Minas'],
                'Brión': ['Caucagua', 'Mamporal'],
                'Buroz': ['Mamporal'],
                'Carrizal': ['Carrizal'],
                'Chacao': ['Chacao'],
                'Cristóbal Rojas': ['Charallave', 'Santa Rosa de Tucaco'],
                'El Hatillo': ['El Hatillo'],
                'Guaicaipuro': ['Los Teques', 'El Jarillo', 'San Pedro'],
                'Independencia': ['Santa Teresa del Tuy', 'Cartanal', 'Marizapa'],
                'Lander': ['Ocumare del Tuy', 'La Democracia'],
                'Los Salias': ['San Antonio de Los Altos'],
                'Páez': ['Río Chico'],
                'Pedro Gual': ['Cúpira', 'Tacarigua'],
                'Plaza': ['Guarenas'],
                'Simón Bolívar': ['San Francisco de Yare', 'Santa Bárbara'],
                'Sucre': ['Petare', 'Caucagüita', 'Fila de Mariches', 'La Dolorita', 'Leoncio Martínez'],
                'Urdaneta': ['Cúa', 'Nueva Cúa'],
                'Zamora': ['San Francisco de Yare']
            }
        },
        'Vargas': {
            cities: ['La Guaira', 'Maiquetía', 'Catia La Mar', 'Macuto'],
            municipalities: ['Vargas'],
            parishes: {
                'Vargas': ['Caraballeda', 'Carayaca', 'Caruao', 'Catia La Mar', 'El Junko', 'La Guaira', 'Macuto', 'Maiquetía', 'Naiguatá', 'Oricao', 'Puerto Cruz', 'Río Chico', 'Urimare']
            }
        },
        'Aragua': {
            cities: ['Maracay', 'Turmero', 'Cagua', 'El Limón', 'La Victoria', 'Villa de Cura', 'San Mateo'],
            municipalities: ['Bolívar', 'Camatagua', 'Francisco Linares Alcántara', 'Girardot', 'José Ángel Lamas', 'José Félix Ribas', 'José Rafael Revenga', 'Libertador', 'Mario Briceño Iragorry', 'Ocumare de La Costa de Oro', 'San Casimiro', 'San Sebastián', 'Santiago Mariño', 'Santos Michelena', 'Sucre', 'Tovar', 'Urdaneta', 'Zamora'],
            parishes: {
                'Bolívar': ['San Mateo', 'San Rafael'],
                'Camatagua': ['Camatagua', 'Carmen'], 
                'Francisco Linares Alcántara': ['Santa Rita'],
                'Girardot': ['Maracay', 'Andrés Eloy Blanco', 'Choroní', 'Joaquín Crespo', 'José Casanova Godoy', 'Las Delicias', 'Los Tamarindos', 'Madre María de San José', 'Pedro José Ovalles'],
                'José Ángel Lamas': ['Santa Cruz'],
                'José Félix Ribas': ['La Victoria', 'Augusto Mijares', 'San José'],
                'José Rafael Revenga': ['El Consejo'],
                'Libertador': ['Palo Negro', 'San Martín'],
                'Mario Briceño Iragorry': ['Caña de Azúcar', 'Las Acacias'],
                'Ocumare de La Costa de Oro': ['Ocumare de La Costa'],
                'San Casimiro': ['San Casimiro', 'Guiramaca'],
                'San Sebastián': ['San Sebastián'],
                'Santiago Mariño': ['Turmero', 'Arévalo Aponte', 'Chuao'],
                'Santos Michelena': ['Las Tejerías'],
                'Sucre': ['Cagua', 'Bella Vista'],
                'Tovar': ['Colonia Tovar'],
                'Urdaneta': 'Urdaneta',
                'Zamora': ['Villa de Cura', 'Magdaleno']
            }
        },
        'Carabobo': {
            cities: ['Valencia', 'Puerto Cabello', 'Guacara', 'Naguanagua', 'San Joaquín', 'Mariara', 'Los Guayos'],
            municipalities: ['Bejuma', 'Carlos Arvelo', 'Diego Ibarra', 'Guacara', 'Juan José Mora', 'Libertador', 'Los Guayos', 'Miranda', 'Montalbán', 'Naguanagua', 'Puerto Cabello', 'San Diego', 'San Joaquín', 'Valencia'],
            parishes: {
                'Bejuma': ['Bejuma', 'Canoabo', 'El Palito'],
                'Carlos Arvelo': ['Güigüe', 'Belén'],
                'Diego Ibarra': ['Mariara', 'Aguas Calientes'],
                'Guacara': ['Guacara', 'Yagua'],
                'Juan José Mora': ['Morón', 'Urama'],
                'Libertador': ['Tocuyito', 'Independencia'],
                'Los Guayos': ['Los Guayos'],
                'Miranda': ['Miranda'],
                'Montalbán': ['Montalbán'],
                'Naguanagua': ['Naguanagua'],
                'Puerto Cabello': ['Puerto Cabello', 'Borburata', 'Patanemo'],
                'San Diego': ['San Diego'],
                'San Joaquín': ['San Joaquín'],
                'Valencia': ['Valencia', 'Candelaria', 'Catedral', 'El Socorro', 'Miguel Peña', 'Rafael Urdaneta', 'San Blas', 'San José', 'Santa Rosa', 'Negro Primero']
            }
        }
    };

    const stateSelect = document.getElementById('stateSelect');
    const citySelect = document.getElementById('citySelect');
    const municipalitySelect = document.getElementById('municipalitySelect');
    const parishSelect = document.getElementById('parishSelect');

    function populateSelect(select, options, placeholder) {
        select.innerHTML = `<option value="">${placeholder}</option>`;
        if (Array.isArray(options)) {
            options.forEach(opt => {
                select.innerHTML += `<option value="${opt}">${opt}</option>`;
            });
        }
    }

    stateSelect?.addEventListener('change', function() {
        const state = this.value;
        if (!state || !locationData[state]) {
            populateSelect(citySelect, [], 'Primero selecciona un estado');
            populateSelect(municipalitySelect, [], 'Primero selecciona un estado');
            populateSelect(parishSelect, [], 'Primero selecciona un municipio');
            return;
        }

        const data = locationData[state];
        populateSelect(citySelect, data.cities, 'Selecciona una ciudad');
        populateSelect(municipalitySelect, data.municipalities, 'Selecciona un municipio');
        populateSelect(parishSelect, [], 'Primero selecciona un municipio');
    });

    municipalitySelect?.addEventListener('change', function() {
        const state = stateSelect.value;
        const municipality = this.value;
        if (!state || !municipality || !locationData[state]?.parishes?.[municipality]) {
            populateSelect(parishSelect, [], 'Primero selecciona un municipio');
            return;
        }
        const parishes = locationData[state].parishes[municipality];
        populateSelect(parishSelect, Array.isArray(parishes) ? parishes : [parishes], 'Selecciona una parroquia');
    });

    // Google Maps Picker
    let mapPicker = null;
    let mapMarker = null;
    const googleMapsApiKey = @json($googleMapsApiKey ?? '');

    const initMapPicker = () => {
        const mapDiv = document.getElementById('clientMapPicker');
        if (!mapDiv || !googleMapsApiKey) return;

        // Default center (Caracas, Venezuela)
        const defaultCenter = { lat: 10.4806, lng: -66.9036 };

        mapPicker = new google.maps.Map(mapDiv, {
            center: defaultCenter,
            zoom: 12,
            mapTypeId: 'roadmap',
            streetViewControl: false,
            mapTypeControl: false,
            fullscreenControl: false,
        });

        // Click event to place marker
        mapPicker.addListener('click', (e) => {
            const lat = e.latLng.lat();
            const lng = e.latLng.lng();

            // Update displays
            document.getElementById('latitudeDisplay').value = lat.toFixed(6);
            document.getElementById('longitudeDisplay').value = lng.toFixed(6);
            // Update hidden form inputs
            form.elements.latitude.value = lat.toFixed(6);
            form.elements.longitude.value = lng.toFixed(6);

            // Place or move marker
            if (mapMarker) {
                mapMarker.setPosition(e.latLng);
            } else {
                mapMarker = new google.maps.Marker({
                    position: e.latLng,
                    map: mapPicker,
                    draggable: true,
                    title: 'Ubicación del cliente',
                });

                // Update on drag
                mapMarker.addListener('dragend', () => {
                    const pos = mapMarker.getPosition();
                    document.getElementById('latitudeDisplay').value = pos.lat().toFixed(6);
                    document.getElementById('longitudeDisplay').value = pos.lng().toFixed(6);
                    form.elements.latitude.value = pos.lat().toFixed(6);
                    form.elements.longitude.value = pos.lng().toFixed(6);
                });
            }
        });
    };

    // Clear location button
    document.getElementById('clearLocationBtn')?.addEventListener('click', () => {
        document.getElementById('latitudeDisplay').value = '';
        document.getElementById('longitudeDisplay').value = '';
        form.elements.latitude.value = '';
        form.elements.longitude.value = '';
        if (mapMarker) {
            mapMarker.setMap(null);
            mapMarker = null;
        }
    });

    // Initialize map when modal opens
    modalElement.addEventListener('shown.bs.modal', () => {
        if (typeof google !== 'undefined' && google.maps) {
            if (!mapPicker) {
                initMapPicker();
            }
        } else if (googleMapsApiKey) {
            // Load Google Maps API dynamically
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${googleMapsApiKey}&callback=initMapPickerCallback`;
            script.async = true;
            script.defer = true;
            window.initMapPickerCallback = () => {
                initMapPicker();
                // Restore original if exists
                if (typeof window.originalInitMap === 'function') {
                    window.initMap = window.originalInitMap;
                }
            };
            document.head.appendChild(script);
        }
    });

    // Set marker position when editing client with existing coordinates
    const setMarkerFromCoordinates = (lat, lng) => {
        if (!mapPicker || !lat || !lng) return;

        const position = { lat: parseFloat(lat), lng: parseFloat(lng) };
        mapPicker.setCenter(position);

        if (mapMarker) {
            mapMarker.setPosition(position);
        } else {
            mapMarker = new google.maps.Marker({
                position: position,
                map: mapPicker,
                draggable: true,
                title: 'Ubicación del cliente',
            });
            mapMarker.addListener('dragend', () => {
                const pos = mapMarker.getPosition();
                document.getElementById('latitudeDisplay').value = pos.lat().toFixed(6);
                document.getElementById('longitudeDisplay').value = pos.lng().toFixed(6);
            });
        }
    };

    // Override openEditMode to set marker
    const originalOpenEditMode = openEditMode;
    window.openEditModeWithMap = (client) => {
        originalOpenEditMode(client);
        // Wait for modal to open and map to init
        setTimeout(() => {
            if (client.latitude && client.longitude) {
                setMarkerFromCoordinates(client.latitude, client.longitude);
            }
        }, 500);
    };
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
</style>
@endpush
