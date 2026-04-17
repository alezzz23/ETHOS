@extends('layouts.vuexy')
@section('title', 'Usuarios')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card ethos-crm-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 d-flex align-items-center gap-2">
                    <i class="ti ti-users-group"></i>
                    <span>Gestión de Usuarios</span>
                </h5>
                <button type="button" class="btn btn-primary ethos-create-btn" data-bs-toggle="modal" data-bs-target="#userModal" id="btnNewUser">
                    <i class="ti ti-user-plus"></i>
                    <span>Nuevo Usuario</span>
                </button>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <div id="usersFeedback" class="alert d-none" role="alert" aria-live="polite"></div>

                <div class="table-responsive ethos-table-shell">
                    <table class="table table-hover align-middle ethos-data-table" id="usersTable">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Registrado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            @forelse($users as $user)
                            <tr data-user-id="{{ $user->id }}">
                                <td>
                                    <div class="ethos-primary-cell">
                                        <span class="ethos-cell-avatar">{{ $user->initials }}</span>
                                        <span class="ethos-cell-text">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="ethos-muted-cell">
                                        <i class="ti ti-mail"></i>
                                        <span>{{ $user->email }}</span>
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $role = $user->getRoleNames()->first() ?? 'sin_rol';
                                        $roleColors = [
                                            'super_admin'         => 'bg-label-danger',
                                            'marketing'           => 'bg-label-primary',
                                            'consultor'           => 'bg-label-info',
                                            'lider_proyecto'      => 'bg-label-warning',
                                            'equipo_levantamiento'=> 'bg-label-secondary',
                                        ];
                                        $roleColor = $roleColors[$role] ?? 'bg-label-dark';
                                    @endphp
                                    <span class="badge {{ $roleColor }}">{{ str_replace('_', ' ', $role) }}</span>
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <button type="button"
                                        class="btn btn-sm btn-icon btn-text-secondary rounded-pill ethos-action-btn js-edit-user"
                                        title="Editar usuario"
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->name }}"
                                        data-user-email="{{ $user->email }}"
                                        data-user-role="{{ $role }}">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    @if($user->id !== auth()->id())
                                    <button type="button"
                                        class="btn btn-sm btn-icon btn-text-danger rounded-pill ethos-action-btn js-delete-user"
                                        title="Eliminar usuario"
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->name }}">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <x-ethos.empty-state
                                        icon="ti-users-off"
                                        title="No hay usuarios registrados"
                                        description="Creá el primer usuario para asignarle un rol y permisos."
                                        inline
                                    />
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- User Modal (Create / Edit) --}}
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="ethos-modal-title-wrap">
            <span class="ethos-modal-icon"><i class="ti ti-user" id="userModalIcon"></i></span>
            <div>
                <h5 class="modal-title" id="userModalLabel">Nuevo Usuario</h5>
                <p class="mb-0 ethos-modal-subtitle" id="userModalSubtitle">Completa los datos del nuevo usuario.</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="userForm" novalidate>
        @csrf
        <input type="hidden" id="userId" name="_user_id" value="">
        <div class="modal-body">
            <div id="userFormFeedback" class="alert d-none mb-3" role="alert"></div>
            <div class="mb-3">
                <label for="userName" class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                <input type="text" id="userName" name="name" class="form-control" required autocomplete="off">
            </div>
            <div class="mb-3">
                <label for="userEmail" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                <input type="email" id="userEmail" name="email" class="form-control" required autocomplete="off">
            </div>
            <div class="mb-3">
                <label for="userPassword" class="form-label fw-semibold">Contraseña <span id="passwordHint" class="text-muted fw-normal">(dejar en blanco para no cambiar)</span></label>
                <input type="password" id="userPassword" name="password" class="form-control" autocomplete="new-password" placeholder="Mínimo 8 caracteres">
            </div>
            <div class="mb-3">
                <label for="userRole" class="form-label fw-semibold">Rol <span class="text-danger">*</span></label>
                <select id="userRole" name="role" class="form-select" required>
                    <option value="">— Seleccionar rol —</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ ucwords(str_replace('_', ' ', $role->name)) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary" id="userSubmitBtn">
                <span id="userBtnText">Crear Usuario</span>
                <span id="userBtnSpinner" class="spinner-border spinner-border-sm d-none ms-1" role="status"></span>
            </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const storeUrl   = '{{ route("users.store") }}';
    const updateBase = '{{ url("admin/users") }}';
    const deleteBase = '{{ url("admin/users") }}';
    const token      = document.querySelector('meta[name="csrf-token"]')?.content;

    const modal        = new bootstrap.Modal(document.getElementById('userModal'));
    const form         = document.getElementById('userForm');
    const userIdInput  = document.getElementById('userId');
    const feedback     = document.getElementById('userFormFeedback');
    const tableFb      = document.getElementById('usersFeedback');
    const submitBtn    = document.getElementById('userSubmitBtn');
    const btnText      = document.getElementById('userBtnText');
    const spinner      = document.getElementById('userBtnSpinner');
    const passwordHint = document.getElementById('passwordHint');

    function showFeedback(el, type, msg) {
        el.className = `alert alert-${type}`;
        el.textContent = msg;
    }

    function setLoading(loading) {
        submitBtn.disabled = loading;
        spinner.classList.toggle('d-none', !loading);
    }

    // Pending edit data (null = create mode)
    let pendingEdit = null;

    // "New User" button: mark create mode (Bootstrap opens modal via data-bs-toggle)
    document.getElementById('btnNewUser').addEventListener('click', () => {
        pendingEdit = null;
    });

    // Edit button: store data, then show modal programmatically
    document.getElementById('usersTableBody').addEventListener('click', function(e) {
        const editBtn = e.target.closest('.js-edit-user');
        if (!editBtn) return;
        pendingEdit = {
            id:    editBtn.dataset.userId,
            name:  editBtn.dataset.userName,
            email: editBtn.dataset.userEmail,
            role:  editBtn.dataset.userRole,
        };
        modal.show();
    });

    // Populate form fields when modal is about to open (correct Bootstrap 5 pattern)
    document.getElementById('userModal').addEventListener('show.bs.modal', () => {
        form.reset();
        feedback.className = 'alert d-none';
        if (pendingEdit) {
            userIdInput.value = pendingEdit.id;
            document.getElementById('userName').value  = pendingEdit.name;
            document.getElementById('userEmail').value = pendingEdit.email;
            document.getElementById('userRole').value  = pendingEdit.role;
            document.getElementById('userModalLabel').textContent    = 'Editar Usuario';
            document.getElementById('userModalSubtitle').textContent = 'Modifica los datos del usuario.';
            document.getElementById('userModalIcon').className       = 'ti ti-user-edit';
            btnText.textContent = 'Guardar Cambios';
            passwordHint.textContent = '(dejar en blanco para no cambiar)';
            document.getElementById('userPassword').required = false;
        } else {
            userIdInput.value = '';
            document.getElementById('userModalLabel').textContent    = 'Nuevo Usuario';
            document.getElementById('userModalSubtitle').textContent = 'Completa los datos del nuevo usuario.';
            document.getElementById('userModalIcon').className       = 'ti ti-user-plus';
            btnText.textContent = 'Crear Usuario';
            passwordHint.textContent = '(requerida)';
            document.getElementById('userPassword').required = true;
        }
    });

    // Reset state after modal fully closes
    document.getElementById('userModal').addEventListener('hidden.bs.modal', () => {
        pendingEdit = null;
        form.reset();
    });

    // Submit
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        setLoading(true);
        feedback.className = 'alert d-none';

        const id      = userIdInput.value;
        const isEdit  = !!id;
        const url     = isEdit ? `${updateBase}/${id}` : storeUrl;
        const method  = isEdit ? 'PUT' : 'POST';

        const body = new URLSearchParams({
            _token:   token,
            name:     document.getElementById('userName').value,
            email:    document.getElementById('userEmail').value,
            role:     document.getElementById('userRole').value,
        });
        const pass = document.getElementById('userPassword').value;
        if (pass) body.set('password', pass);

        try {
            const res  = await fetch(url, { method, headers: { 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' }, body });
            const data = await res.json();
            if (!res.ok) {
                const errors = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Error inesperado');
                showFeedback(feedback, 'danger', errors);
            } else {
                modal.hide();
                showFeedback(tableFb, 'success', data.message);
                setTimeout(() => location.reload(), 1000);
            }
        } catch(err) {
            showFeedback(feedback, 'danger', 'Error de red al guardar el usuario.');
        }
        setLoading(false);
    });

    // Delete
    document.getElementById('usersTableBody').addEventListener('click', async function(e) {
        const btn = e.target.closest('.js-delete-user');
        if (!btn) return;
        const userId   = btn.dataset.userId;
        const userName = btn.dataset.userName;
        const isConfirmed = await window.EthosAlerts.confirm({
            title: 'Eliminar usuario',
            text: `Se eliminará a "${userName}" y esta acción no se puede deshacer.`,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            danger: true,
        });
        if (!isConfirmed) return;

        try {
            const response = await fetch(`${deleteBase}/${userId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            });
            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                window.EthosAlerts.error(data.message || 'Error al eliminar el usuario.');
                return;
            }

            btn.closest('tr')?.remove();
            window.EthosAlerts.success(data.message || 'Usuario eliminado.');
        } catch {
            window.EthosAlerts.error('Error al eliminar el usuario.');
        }
    });
})();
</script>
@endpush
