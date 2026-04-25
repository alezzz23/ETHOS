@extends('layouts.vuexy')
@section('title', 'Mi Perfil | ETHOS')
@section('meta_description', 'Edita tu perfil de usuario en ETHOS. Actualiza tus datos personales y preferencias de cuenta.')
@section('meta_keywords', 'perfil, usuario, editar, ETHOS, consultoría')
@section('canonical', url()->current())
@section('og_title', 'Editar Perfil | ETHOS')
@section('og_description', 'Edita tu perfil de usuario en ETHOS. Actualiza tus datos personales y preferencias de cuenta.')
@section('og_image', asset('images/ethos-og.jpg'))
@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Person",
    "name": "{{ Auth::user()->name ?? 'Usuario ETHOS' }}",
    "email": "{{ Auth::user()->email ?? '' }}"
}
</script>
@endsection

@push('styles')
<style>
/* ── Profile Page ── */
.profile-hero {
    position: relative;
    border-radius: 1.2rem;
    overflow: hidden;
    background: linear-gradient(135deg,
        rgba(var(--vz-primary-rgb), 0.18) 0%,
        rgba(var(--vz-info-rgb), 0.12) 50%,
        rgba(var(--vz-primary-rgb), 0.06) 100%);
    border: 1px solid rgba(var(--vz-primary-rgb), 0.18);
    box-shadow: 0 20px 50px -20px rgba(var(--vz-primary-rgb), 0.4);
    padding: 2.5rem 2rem 2rem;
    margin-bottom: 1.75rem;
}

.profile-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse 80% 60% at 10% -10%, rgba(var(--vz-primary-rgb), 0.22) 0%, transparent 65%),
        radial-gradient(ellipse 60% 45% at 90% 110%, rgba(var(--vz-info-rgb), 0.18) 0%, transparent 60%);
    pointer-events: none;
}

.profile-avatar-wrap {
    position: relative;
    display: inline-block;
}

.profile-avatar {
    width: 96px;
    height: 96px;
    border-radius: 1.2rem;
    background: rgba(var(--vz-primary-rgb), 0.18);
    border: 3px solid rgba(var(--vz-primary-rgb), 0.3);
    color: var(--vz-primary);
    font-size: 2.2rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    overflow: hidden;
    transition: border-color 0.25s, box-shadow 0.25s;
    box-shadow: 0 8px 24px -8px rgba(var(--vz-primary-rgb), 0.4);
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-avatar:hover {
    border-color: var(--vz-primary);
    box-shadow: 0 0 0 4px rgba(var(--vz-primary-rgb), 0.18), 0 8px 24px -8px rgba(var(--vz-primary-rgb), 0.5);
}

.profile-avatar-overlay {
    position: absolute;
    inset: 0;
    border-radius: 1.2rem;
    background: rgba(0,0,0,0.42);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.25s;
    cursor: pointer;
    color: #fff;
    font-size: 1.4rem;
}

.profile-avatar-wrap:hover .profile-avatar-overlay { opacity: 1; }

.profile-avatar-spinner {
    display: none;
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.55);
    border-radius: 1.2rem;
    align-items: center;
    justify-content: center;
    color: #fff;
}
.profile-avatar-spinner.show { display: flex; }

.profile-name {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--vz-heading-color);
    line-height: 1.2;
    margin-bottom: 0.15rem;
}

.profile-position {
    font-size: 0.9rem;
    color: var(--vz-primary);
    font-weight: 600;
}

.profile-email {
    font-size: 0.82rem;
    color: var(--vz-body-color);
    margin-top: 0.2rem;
}

.profile-stats {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.profile-stat {
    text-align: center;
    padding: 0.5rem 1rem;
    border-radius: 0.7rem;
    background: rgba(var(--vz-primary-rgb), 0.07);
    border: 1px solid rgba(var(--vz-primary-rgb), 0.12);
}

.profile-stat-value {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--vz-heading-color);
    line-height: 1;
}

.profile-stat-label {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--vz-body-color);
    font-weight: 600;
    margin-top: 0.2rem;
}

/* ── Tabs ── */
.profile-tabs {
    display: flex;
    gap: 0.35rem;
    border-bottom: 1.5px solid var(--vz-border-color);
    margin-bottom: 1.5rem;
    overflow-x: auto;
}

.profile-tab-btn {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.55rem 1rem;
    border: none;
    background: none;
    border-bottom: 2.5px solid transparent;
    margin-bottom: -1.5px;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--vz-body-color);
    cursor: pointer;
    border-radius: 0.4rem 0.4rem 0 0;
    transition: color 0.2s, border-color 0.2s, background 0.2s;
    white-space: nowrap;
}

.profile-tab-btn:hover {
    color: var(--vz-primary);
    background: rgba(var(--vz-primary-rgb), 0.06);
}

.profile-tab-btn.active {
    color: var(--vz-primary);
    border-bottom-color: var(--vz-primary);
}

.profile-tab-pane { display: none; }
.profile-tab-pane.active { display: block; animation: tabFadeIn 0.3s ease; }

@keyframes tabFadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ── Form sections ── */
.profile-card {
    border-radius: 1rem;
    border: 1px solid rgba(var(--vz-primary-rgb), 0.12);
    background: var(--vz-card-bg);
    box-shadow: 0 10px 32px -16px rgba(var(--vz-primary-rgb), 0.22);
    overflow: hidden;
}

.profile-card-header {
    padding: 1.1rem 1.5rem;
    border-bottom: 1px solid rgba(var(--vz-primary-rgb), 0.1);
    background: rgba(var(--vz-primary-rgb), 0.035);
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.profile-card-header h6 {
    font-size: 0.9rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--vz-heading-color);
    margin: 0;
}

.profile-card-header i { color: var(--vz-primary); font-size: 1.1rem; }
.profile-card-body { padding: 1.5rem; }

/* ── Password strength ── */
.password-strength-bar {
    height: 5px;
    border-radius: 99px;
    background: rgba(var(--vz-primary-rgb), 0.1);
    margin-top: 0.5rem;
    overflow: hidden;
}

.password-strength-fill {
    height: 100%;
    border-radius: 99px;
    transition: width 0.35s ease, background-color 0.35s ease;
    width: 0%;
}

.password-strength-text {
    font-size: 0.75rem;
    margin-top: 0.25rem;
    font-weight: 600;
}

/* ── Toggle switch ── */
.profile-toggle-item {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 0.85rem 0;
    border-bottom: 1px solid rgba(var(--vz-primary-rgb), 0.07);
    gap: 1rem;
}

.profile-toggle-item:last-child { border-bottom: 0; }

.profile-toggle-label { font-weight: 600; font-size: 0.875rem; color: var(--vz-heading-color); }
.profile-toggle-desc { font-size: 0.78rem; color: var(--vz-body-color); margin-top: 0.2rem; }

/* ── Feedback Alerts ── */
.profile-feedback {
    font-size: 0.85rem;
    border-radius: 0.6rem;
    border: 1px solid transparent;
    display: none;
    margin-bottom: 0.8rem;
}

.profile-feedback.show { display: block; animation: ethosAlertSlide 0.3s ease; }

/* ── Danger Zone ── */
.profile-danger-zone {
    border: 1px solid rgba(var(--vz-danger-rgb), 0.25);
    border-radius: 1rem;
    padding: 1.25rem 1.5rem;
    background: rgba(var(--vz-danger-rgb), 0.04);
}

/* ── Bio Counter ── */
.bio-counter {
    font-size: 0.72rem;
    color: var(--vz-body-color);
    text-align: right;
    margin-top: 0.25rem;
}

/* Dark mode adjustments */
.dark-style .profile-hero {
    background: linear-gradient(135deg,
        rgba(var(--vz-primary-rgb), 0.25) 0%,
        rgba(26, 31, 48, 0.9) 50%,
        rgba(var(--vz-primary-rgb), 0.12) 100%);
    border-color: rgba(var(--vz-primary-rgb), 0.3);
}

.dark-style .profile-stat {
    background: rgba(var(--vz-primary-rgb), 0.12);
    border-color: rgba(var(--vz-primary-rgb), 0.22);
}

.dark-style .profile-card {
    border-color: rgba(var(--vz-primary-rgb), 0.25);
    background: linear-gradient(180deg, rgba(38, 43, 66, 0.98) 0%, rgba(31, 36, 54, 1) 100%);
}

.dark-style .profile-card-header {
    background: rgba(var(--vz-primary-rgb), 0.12);
    border-bottom-color: rgba(var(--vz-primary-rgb), 0.2);
}

.dark-style .profile-toggle-item {
    border-bottom-color: rgba(var(--vz-primary-rgb), 0.12);
}

.dark-style .profile-danger-zone {
    background: rgba(var(--vz-danger-rgb), 0.08);
    border-color: rgba(var(--vz-danger-rgb), 0.3);
}
</style>
@endpush

@section('content')
@php
    $user = auth()->user();
    $initials = $user->initials;
@endphp

{{-- Hidden file input for avatar --}}
<input type="file" id="avatarFileInput" accept="image/*" class="d-none">

{{-- ── HERO ── --}}
<div class="profile-hero mb-4">
    <div class="row g-3 align-items-center">
        <div class="col-auto">
            <div class="profile-avatar-wrap">
                <div class="profile-avatar" id="profileAvatarCircle" title="Cambiar foto de perfil">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" id="profileAvatarImg" alt="Avatar">
                    @else
                        <span id="profileAvatarInitials">{{ $initials }}</span>
                        <img src="" id="profileAvatarImg" alt="Avatar" style="display:none;">
                    @endif
                </div>
                <div class="profile-avatar-overlay" id="avatarOverlay">
                    <i class="ti ti-camera"></i>
                </div>
                <div class="profile-avatar-spinner" id="avatarSpinner">
                    <div class="spinner-border spinner-border-sm text-white" role="status"></div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="profile-name" id="heroName">{{ $user->name }}</div>
            <div class="profile-position" id="heroPosition">{{ $user->position ?? 'Sin cargo' }}</div>
            <div class="profile-email"><i class="ti ti-mail me-1"></i>{{ $user->email }}</div>
        </div>
        <div class="col-12 col-md-auto mt-2 mt-md-0">
            <div class="profile-stats">
                <div class="profile-stat">
                    <div class="profile-stat-value">{{ \App\Models\Project::where('assigned_to', $user->id)->count() }}</div>
                    <div class="profile-stat-label">Proyectos</div>
                </div>
                <div class="profile-stat">
                    <div class="profile-stat-value">{{ $user->created_at->diffInDays() }}</div>
                    <div class="profile-stat-label">Días activo</div>
                </div>
                <div class="profile-stat">
                    <div class="profile-stat-value">{{ $user->getRoleNames()->first() ?? 'User' }}</div>
                    <div class="profile-stat-label">Rol</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── TABS ── --}}
<div class="profile-tabs" id="profileTabs" role="tablist">
    <button class="profile-tab-btn active" data-tab="info" role="tab">
        <i class="ti ti-user"></i> Información Personal
    </button>
    <button class="profile-tab-btn" data-tab="password" role="tab">
        <i class="ti ti-lock"></i> Contraseña
    </button>
    <button class="profile-tab-btn" data-tab="notifications" role="tab">
        <i class="ti ti-bell"></i> Notificaciones
    </button>
    <button class="profile-tab-btn" data-tab="privacy" role="tab">
        <i class="ti ti-shield"></i> Privacidad
    </button>
    <button class="profile-tab-btn" data-tab="danger" role="tab">
        <i class="ti ti-alert-triangle"></i> Cuenta
    </button>
</div>

{{-- ── TAB: INFORMACIÓN PERSONAL ── --}}
<div class="profile-tab-pane active" id="pane-info">
    <div class="profile-card">
        <div class="profile-card-header">
            <i class="ti ti-id-badge"></i>
            <h6>Información Personal</h6>
        </div>
        <div class="profile-card-body">
            <div id="infoFeedback" class="profile-feedback alert" role="alert" aria-live="polite"></div>
            <form id="profileInfoForm" novalidate>
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label d-flex align-items-center gap-1">
                            <i class="ti ti-user text-primary"></i> Nombre completo <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="pf_name" name="name" class="form-control" value="{{ $user->name }}" required>
                        <div class="invalid-feedback" id="err_name"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label d-flex align-items-center gap-1">
                            <i class="ti ti-at text-primary"></i> Correo electrónico <span class="text-danger">*</span>
                        </label>
                        <input type="email" id="pf_email" name="email" class="form-control" value="{{ $user->email }}" required>
                        <div class="invalid-feedback" id="err_email"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label d-flex align-items-center gap-1">
                            <i class="ti ti-phone text-primary"></i> Teléfono
                        </label>
                        <input type="tel" id="pf_phone" name="phone" class="form-control" value="{{ $user->phone }}" placeholder="+58 412-1234567">
                        <div class="invalid-feedback" id="err_phone"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label d-flex align-items-center gap-1">
                            <i class="ti ti-calendar text-primary"></i> Fecha de nacimiento
                        </label>
                        <input type="date" id="pf_birth_date" name="birth_date" class="form-control" value="{{ $user->birth_date?->format('Y-m-d') }}">
                        <div class="invalid-feedback" id="err_birth_date"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label d-flex align-items-center gap-1">
                            <i class="ti ti-briefcase text-primary"></i> Cargo / Posición
                        </label>
                        <input type="text" id="pf_position" name="position" class="form-control" value="{{ $user->position }}" placeholder="Consultor Senior">
                        <div class="invalid-feedback" id="err_position"></div>
                    </div>
                    <div class="col-12">
                        <label class="form-label d-flex align-items-center gap-1">
                            <i class="ti ti-file-description text-primary"></i> Biografía
                        </label>
                        <textarea id="pf_bio" name="bio" class="form-control" rows="3" maxlength="500" placeholder="Cuéntanos sobre ti...">{{ $user->bio }}</textarea>
                        <div class="bio-counter"><span id="bioCount">{{ strlen($user->bio ?? '') }}</span>/500</div>
                        <div class="invalid-feedback" id="err_bio"></div>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary" id="infoSubmitBtn">
                        <span class="spinner-border spinner-border-sm d-none me-1" id="infoSpinner" role="status"></span>
                        <i class="ti ti-device-floppy" id="infoIcon"></i>
                        <span id="infoText">Guardar cambios</span>
                    </button>
                    <button type="reset" class="btn btn-label-secondary">
                        <i class="ti ti-refresh"></i> Restablecer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── TAB: CONTRASEÑA ── --}}
<div class="profile-tab-pane" id="pane-password">
    <div class="profile-card">
        <div class="profile-card-header">
            <i class="ti ti-lock"></i>
            <h6>Cambiar Contraseña</h6>
        </div>
        <div class="profile-card-body">
            <div id="passwordFeedback" class="profile-feedback alert" role="alert" aria-live="polite"></div>
            <form id="profilePasswordForm" novalidate autocomplete="off">
                @csrf
                <div class="row g-3" style="max-width:520px;">
                    <div class="col-12">
                        <label class="form-label d-flex align-items-center gap-1">
                            <i class="ti ti-key text-primary"></i> Contraseña actual <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" id="pf_current_password" name="current_password" class="form-control" autocomplete="current-password" required>
                            <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="pf_current_password">
                                <i class="ti ti-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback d-block" id="err_current_password"></div>
                    </div>
                    <div class="col-12">
                        <label class="form-label d-flex align-items-center gap-1">
                            <i class="ti ti-lock-plus text-primary"></i> Nueva contraseña <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" id="pf_password" name="password" class="form-control" autocomplete="new-password" required>
                            <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="pf_password">
                                <i class="ti ti-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength-bar"><div class="password-strength-fill" id="strengthFill"></div></div>
                        <div class="password-strength-text" id="strengthText"></div>
                        <div class="invalid-feedback d-block" id="err_password"></div>
                    </div>
                    <div class="col-12">
                        <label class="form-label d-flex align-items-center gap-1">
                            <i class="ti ti-lock-check text-primary"></i> Confirmar contraseña <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" id="pf_password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password" required>
                            <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="pf_password_confirmation">
                                <i class="ti ti-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback d-block" id="err_password_confirmation"></div>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary" id="pwSubmitBtn">
                        <span class="spinner-border spinner-border-sm d-none me-1" id="pwSpinner" role="status"></span>
                        <i class="ti ti-shield-check" id="pwIcon"></i>
                        <span id="pwText">Actualizar contraseña</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── TAB: NOTIFICACIONES ── --}}
<div class="profile-tab-pane" id="pane-notifications">
    <div class="profile-card">
        <div class="profile-card-header">
            <i class="ti ti-bell"></i>
            <h6>Preferencias de Notificaciones</h6>
        </div>
        <div class="profile-card-body">
            <div id="notifFeedback" class="profile-feedback alert" role="alert" aria-live="polite"></div>
            <form id="profileNotifForm">
                @csrf
                <div class="profile-toggle-item">
                    <div>
                        <div class="profile-toggle-label"><i class="ti ti-mail me-1 text-primary"></i> Notificaciones por Email</div>
                        <div class="profile-toggle-desc">Recibe alertas y resúmenes por correo electrónico.</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input pref-toggle" type="checkbox" role="switch" name="notif_email" id="notif_email" {{ $user->notif_email ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="profile-toggle-item">
                    <div>
                        <div class="profile-toggle-label"><i class="ti ti-bell me-1 text-primary"></i> Notificaciones del Navegador</div>
                        <div class="profile-toggle-desc">Recibe notificaciones push mientras usas la aplicación.</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input pref-toggle" type="checkbox" role="switch" name="notif_browser" id="notif_browser" {{ $user->notif_browser ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="profile-toggle-item">
                    <div>
                        <div class="profile-toggle-label"><i class="ti ti-briefcase me-1 text-primary"></i> Actualizaciones de Proyectos</div>
                        <div class="profile-toggle-desc">Alertas cuando se actualice el estado de un proyecto.</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input pref-toggle" type="checkbox" role="switch" name="notif_project_updates" id="notif_project_updates" {{ $user->notif_project_updates ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="profile-toggle-item">
                    <div>
                        <div class="profile-toggle-label"><i class="ti ti-users me-1 text-primary"></i> Actividad de Clientes</div>
                        <div class="profile-toggle-desc">Notificaciones cuando se registren nuevos clientes.</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input pref-toggle" type="checkbox" role="switch" name="notif_client_activity" id="notif_client_activity" {{ $user->notif_client_activity ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="button" class="btn btn-primary save-prefs-btn" data-form="profileNotifForm">
                        <i class="ti ti-device-floppy"></i> Guardar preferencias
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── TAB: PRIVACIDAD ── --}}
<div class="profile-tab-pane" id="pane-privacy">
    <div class="profile-card">
        <div class="profile-card-header">
            <i class="ti ti-shield"></i>
            <h6>Configuración de Privacidad</h6>
        </div>
        <div class="profile-card-body">
            <div id="privacyFeedback" class="profile-feedback alert" role="alert" aria-live="polite"></div>
            <form id="profilePrivacyForm">
                @csrf
                <div class="profile-toggle-item">
                    <div>
                        <div class="profile-toggle-label"><i class="ti ti-mail me-1 text-primary"></i> Mostrar Email en Perfil</div>
                        <div class="profile-toggle-desc">Otros usuarios podrán ver tu correo en tu perfil público.</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input pref-toggle" type="checkbox" role="switch" name="privacy_show_email" id="privacy_show_email" {{ $user->privacy_show_email ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="profile-toggle-item">
                    <div>
                        <div class="profile-toggle-label"><i class="ti ti-phone me-1 text-primary"></i> Mostrar Teléfono en Perfil</div>
                        <div class="profile-toggle-desc">Otros usuarios podrán ver tu número telefónico.</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input pref-toggle" type="checkbox" role="switch" name="privacy_show_phone" id="privacy_show_phone" {{ $user->privacy_show_phone ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="button" class="btn btn-primary save-prefs-btn" data-form="profilePrivacyForm">
                        <i class="ti ti-device-floppy"></i> Guardar privacidad
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── TAB: CUENTA / DANGER ZONE ── --}}
<div class="profile-tab-pane" id="pane-danger">
    <div class="profile-danger-zone">
        <h6 class="fw-bold text-danger mb-1"><i class="ti ti-alert-triangle me-1"></i>Zona de Danger</h6>
        <p class="text-muted small mb-3">Las siguientes acciones son irreversibles. Procede con precaución.</p>
        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
            <i class="ti ti-trash me-1"></i> Eliminar mi cuenta
        </button>
    </div>
</div>

{{-- ── MODAL: DELETE ACCOUNT ── --}}
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-danger" id="deleteAccountModalLabel">
                    <i class="ti ti-alert-triangle me-2"></i>Eliminar cuenta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Esta acción es <strong>permanente e irreversible</strong>. Se eliminarán todos tus datos, proyectos asignados y configuraciones.</p>
                <form method="POST" action="{{ route('profile.destroy') }}" id="deleteAccountForm">
                    @csrf
                    @method('DELETE')
                    <label class="form-label">Confirma tu contraseña para continuar:</label>
                    <input type="password" name="password" class="form-control" required placeholder="Tu contraseña actual">
                    @error('password', 'userDeletion')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </label>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="deleteAccountForm" class="btn btn-danger">
                    <i class="ti ti-trash me-1"></i>Sí, eliminar mi cuenta
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // ── TAB SWITCHING ──
    document.querySelectorAll('.profile-tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.profile-tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.profile-tab-pane').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            const pane = document.getElementById('pane-' + btn.dataset.tab);
            if (pane) pane.classList.add('active');
        });
    });

    // ── AVATAR UPLOAD ──
    const avatarInput = document.getElementById('avatarFileInput');
    const avatarCircle = document.getElementById('profileAvatarCircle');
    const avatarOverlay = document.getElementById('avatarOverlay');
    const avatarSpinner = document.getElementById('avatarSpinner');
    const avatarImg = document.getElementById('profileAvatarImg');
    const avatarInitials = document.getElementById('profileAvatarInitials');

    [avatarCircle, avatarOverlay].forEach(el => {
        el?.addEventListener('click', () => avatarInput?.click());
    });

    avatarInput?.addEventListener('change', async () => {
        const file = avatarInput.files[0];
        if (!file) return;

        // Preview locally first
        const reader = new FileReader();
        reader.onload = (e) => {
            if (avatarImg) { avatarImg.src = e.target.result; avatarImg.style.display = 'block'; }
            if (avatarInitials) avatarInitials.style.display = 'none';
        };
        reader.readAsDataURL(file);

        // Upload
        avatarSpinner.classList.add('show');
        const fd = new FormData();
        fd.append('avatar', file);
        fd.append('_token', csrf);

        try {
            const res = await fetch('{{ route("profile.avatar") }}', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) {
                showToast('✓ ' + data.message, 'success');
                // Update navbar avatar if present
                document.querySelectorAll('.navbar-user-avatar').forEach(el => {
                    el.innerHTML = `<img src="${data.avatar_url}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">`;
                });
            } else {
                showToast('Error al subir imagen.', 'error');
            }
        } catch {
            showToast('Error de red al subir imagen.', 'error');
        } finally {
            avatarSpinner.classList.remove('show');
            avatarInput.value = '';
        }
    });

    // ── INFO FORM ──
    const infoForm = document.getElementById('profileInfoForm');
    const infoFeedback = document.getElementById('infoFeedback');

    // Bio counter
    const bioArea = document.getElementById('pf_bio');
    const bioCount = document.getElementById('bioCount');
    bioArea?.addEventListener('input', () => {
        bioCount.textContent = bioArea.value.length;
    });

    // Real-time validation
    ['pf_name', 'pf_email'].forEach(id => {
        const el = document.getElementById(id);
        el?.addEventListener('blur', () => validateField(el));
        el?.addEventListener('input', () => clearFieldError(el));
    });

    infoForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearFeedback(infoFeedback);

        const btn = document.getElementById('infoSubmitBtn');
        const spinner = document.getElementById('infoSpinner');
        const icon = document.getElementById('infoIcon');
        const text = document.getElementById('infoText');

        setLoading(btn, spinner, icon, text, true, 'Guardando...');

        const fd = new FormData(infoForm);
        fd.append('_method', 'PATCH');

        try {
            const res = await apiFetch('{{ route("profile.update") }}', { method: 'POST', body: fd });
            const data = await res.json();

            if (res.ok && data.success) {
                showFeedback(infoFeedback, 'success', '✓ ' + data.message);
                // Update hero
                const heroName = document.getElementById('heroName');
                const heroPos = document.getElementById('heroPosition');
                if (heroName) heroName.textContent = data.user.name;
                if (heroPos) heroPos.textContent = data.user.position || 'Sin cargo';
                // Update navbar name
                document.querySelectorAll('.navbar-user-name').forEach(el => el.textContent = data.user.name);
                showToast('✓ Perfil actualizado', 'success');
            } else if (res.status === 422 && data.errors) {
                renderErrors(data.errors, {
                    name: 'err_name', email: 'err_email',
                    phone: 'err_phone', birth_date: 'err_birth_date',
                    position: 'err_position', bio: 'err_bio',
                });
                showFeedback(infoFeedback, 'danger', 'Revisa los errores del formulario.');
            } else {
                showFeedback(infoFeedback, 'danger', data.message || 'Error al guardar.');
            }
        } catch {
            showFeedback(infoFeedback, 'danger', 'Error de conexión. Intenta de nuevo.');
        } finally {
            setLoading(btn, spinner, icon, text, false, 'Guardar cambios');
        }
    });

    // ── PASSWORD FORM ──
    const pwForm = document.getElementById('profilePasswordForm');
    const pwFeedback = document.getElementById('passwordFeedback');

    // Toggle password visibility
    document.querySelectorAll('.toggle-pw').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            if (!input) return;
            const isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';
            btn.querySelector('i').className = isText ? 'ti ti-eye' : 'ti ti-eye-off';
        });
    });

    // Strength meter
    const pwInput = document.getElementById('pf_password');
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');

    pwInput?.addEventListener('input', () => {
        const val = pwInput.value;
        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const map = [
            { w: '0%', color: '#dc3545', label: '' },
            { w: '25%', color: '#dc3545', label: 'Muy débil' },
            { w: '50%', color: '#fd7e14', label: 'Débil' },
            { w: '75%', color: '#ffc107', label: 'Regular' },
            { w: '100%', color: '#28c76f', label: 'Fuerte' },
        ];
        strengthFill.style.width = map[score].w;
        strengthFill.style.backgroundColor = map[score].color;
        strengthText.textContent = map[score].label;
        strengthText.style.color = map[score].color;
    });

    pwForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearFeedback(pwFeedback);
        ['err_current_password', 'err_password', 'err_password_confirmation'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.textContent = '';
        });

        const btn = document.getElementById('pwSubmitBtn');
        const spinner = document.getElementById('pwSpinner');
        const icon = document.getElementById('pwIcon');
        const text = document.getElementById('pwText');
        setLoading(btn, spinner, icon, text, true, 'Actualizando...');

        const fd = new FormData(pwForm);
        try {
            const res = await apiFetch('{{ route("profile.password") }}', { method: 'POST', body: fd });
            const data = await res.json();

            if (res.ok && data.success) {
                showFeedback(pwFeedback, 'success', '✓ ' + data.message);
                pwForm.reset();
                strengthFill.style.width = '0%';
                strengthText.textContent = '';
                showToast('✓ Contraseña actualizada', 'success');
            } else if (res.status === 422 && data.errors) {
                renderErrors(data.errors, {
                    current_password: 'err_current_password',
                    password: 'err_password',
                    password_confirmation: 'err_password_confirmation',
                });
                showFeedback(pwFeedback, 'danger', 'Revisa los errores del formulario.');
            } else {
                showFeedback(pwFeedback, 'danger', data.message || 'Error al actualizar.');
            }
        } catch {
            showFeedback(pwFeedback, 'danger', 'Error de conexión.');
        } finally {
            setLoading(btn, spinner, icon, text, false, 'Actualizar contraseña');
        }
    });

    // ── PREFERENCES (Notif + Privacy) ──
    document.querySelectorAll('.save-prefs-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const formId = btn.dataset.form;
            const form = document.getElementById(formId);
            const feedbackId = formId === 'profileNotifForm' ? 'notifFeedback' : 'privacyFeedback';
            const feedback = document.getElementById(feedbackId);
            clearFeedback(feedback);

            const fd = new FormData(form);
            // Ensure unchecked checkboxes are sent as 0
            form.querySelectorAll('.pref-toggle').forEach(toggle => {
                if (!toggle.checked) fd.set(toggle.name, '0');
                else fd.set(toggle.name, '1');
            });

            btn.disabled = true;
            const origHtml = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Guardando...';

            try {
                const res = await apiFetch('{{ route("profile.preferences") }}', { method: 'POST', body: fd });
                const data = await res.json();
                if (data.success) {
                    showFeedback(feedback, 'success', '✓ ' + data.message);
                    showToast('✓ Preferencias guardadas', 'success');
                } else {
                    showFeedback(feedback, 'danger', 'Error al guardar preferencias.');
                }
            } catch {
                showFeedback(feedback, 'danger', 'Error de conexión.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = origHtml;
            }
        });
    });

    // ── AUTO-SAVE on toggle change ──
    document.querySelectorAll('.pref-toggle').forEach(toggle => {
        toggle.addEventListener('change', () => {
            const form = toggle.closest('form');
            const saveBtn = form?.querySelector('.save-prefs-btn');
            if (saveBtn) saveBtn.click();
        });
    });

    // ── HELPERS ──
    function apiFetch(url, options = {}) {
        return fetch(url, {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf,
                ...(options.headers || {}),
            },
            ...options,
        });
    }

    function showFeedback(el, type, message) {
        if (!el) return;
        el.className = `profile-feedback alert alert-${type} show`;
        el.textContent = message;
    }

    function clearFeedback(el) {
        if (!el) return;
        el.className = 'profile-feedback alert';
        el.textContent = '';
    }

    function setLoading(btn, spinner, icon, text, loading, label) {
        btn.disabled = loading;
        spinner.classList.toggle('d-none', !loading);
        icon.classList.toggle('d-none', loading);
        text.textContent = loading ? label : (label === 'Guardando...' ? 'Guardar cambios' : 'Actualizar contraseña');
    }

    function renderErrors(errors, fieldMap) {
        Object.entries(fieldMap).forEach(([field, errId]) => {
            const el = document.getElementById(errId);
            if (!el) return;
            el.textContent = errors[field]?.[0] || '';
        });
    }

    function validateField(el) {
        const err = document.getElementById('err_' + el.id.replace('pf_', ''));
        if (!el.validity.valid) {
            el.classList.add('is-invalid');
            if (err) err.textContent = el.validationMessage;
        } else {
            el.classList.remove('is-invalid');
            if (err) err.textContent = '';
        }
    }

    function clearFieldError(el) {
        el.classList.remove('is-invalid');
        const err = document.getElementById('err_' + el.id.replace('pf_', ''));
        if (err) err.textContent = '';
    }

    // ── TOAST NOTIFICATION ──
    function showToast(message, type = 'success') {
        const existing = document.getElementById('profileToast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.id = 'profileToast';
        toast.style.cssText = `
            position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999;
            background: ${type === 'success' ? 'rgba(40, 199, 111, 0.95)' : 'rgba(234, 84, 85, 0.95)'};
            color: #fff; padding: 0.8rem 1.25rem; border-radius: 0.7rem;
            font-size: 0.875rem; font-weight: 600;
            box-shadow: 0 8px 24px -8px rgba(0,0,0,0.4);
            backdrop-filter: blur(8px);
            animation: toastIn 0.35s ease; display: flex; align-items: center; gap: 0.5rem;
        `;
        toast.innerHTML = message;
        document.body.appendChild(toast);

        const style = document.createElement('style');
        style.textContent = '@keyframes toastIn { from { opacity:0; transform: translateY(20px); } to { opacity:1; transform: translateY(0); } }';
        document.head.appendChild(style);

        setTimeout(() => toast.remove(), 3500);
    }
})();
</script>
@endpush
