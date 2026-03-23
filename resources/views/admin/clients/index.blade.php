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
                    {{ $clients->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade ethos-create-modal" id="createClientModal" tabindex="-1" aria-labelledby="createClientModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
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
          <div class="modal-body">
              <div id="clientFormFeedback" class="alert d-none ethos-ajax-alert" role="alert" aria-live="assertive"></div>
              <div class="row g-3">
              <div class="col-md-6">
                  <label class="form-label"><i class="ti ti-building"></i> Nombre *</label>
                  <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
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
              </div>
              <div class="col-12">
                  <label class="form-label"><i class="ti ti-notes"></i> Notas</label>
                  <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
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
        form.elements.name.value = client.name || '';
        form.elements.industry.value = client.industry || '';
        form.elements.primary_contact_name.value = client.primary_contact_name || '';
        form.elements.primary_contact_email.value = client.primary_contact_email || '';
        form.elements.notes.value = client.notes || '';
        modalTitle.textContent = 'Editar Cliente';
        modalSubtitle.textContent = 'Actualiza la información del cliente seleccionado.';
        modalIcon.className = 'ti ti-edit';
        submitText.textContent = 'Actualizar Cliente';
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

    const normalizeClient = (client) => ({
        id: client.id,
        name: client.name || '',
        industry: client.industry || '',
        industry_label: client.industry_label || 'Sin industria',
        primary_contact_name: client.primary_contact_name || '',
        primary_contact_name_label: client.primary_contact_name_label || 'Sin contacto',
        primary_contact_email: client.primary_contact_email || '',
        primary_contact_email_label: client.primary_contact_email_label || 'Sin email',
        notes: client.notes || ''
    });

    const clientRowHtml = (client) => {
        const payload = escapeHtml(JSON.stringify({
            id: client.id,
            name: client.name,
            industry: client.industry,
            primary_contact_name: client.primary_contact_name,
            primary_contact_email: client.primary_contact_email,
            notes: client.notes
        }));
        const actionCell = canUpdate
            ? `<button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill ethos-action-btn js-edit-client" title="Editar cliente" data-client="${payload}"><i class="ti ti-edit"></i></button>`
            : '';

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
    openCreateMode();
})();
</script>
@endpush
