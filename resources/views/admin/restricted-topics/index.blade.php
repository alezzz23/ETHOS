@extends('layouts.vuexy')

@section('title', 'Tópicos Restringidos')

@section('content')

<div class="row g-4" x-data="restrictedTopics()" x-init="init()">
    <div class="col-12">
        <div class="card ethos-crm-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 d-flex align-items-center gap-2">
                    <i class="ti ti-shield-lock"></i>
                    <span>Tópicos Restringidos del Chatbot</span>
                </h5>
                <button type="button" class="btn btn-primary ethos-create-btn"
                        data-bs-toggle="modal" data-bs-target="#topicFormModal"
                        @click="openCreate()">
                    <i class="ti ti-plus"></i>
                    <span>Nuevo Tópico</span>
                </button>
            </div>
            <div class="card-body">
                <div class="alert alert-light border d-flex gap-2 mb-3">
                    <i class="ti ti-info-circle text-info mt-1"></i>
                    <div>
                        <strong>¿Cómo funciona?</strong> Cuando un usuario escribe algo que contiene
                        alguna de las <em>palabras clave</em> de un tópico activo, el chatbot responderá
                        automáticamente con el mensaje configurado, sin enviar la pregunta al modelo de IA.
                    </div>
                </div>

                <div id="topicsFeedback" class="alert d-none" role="alert" aria-live="polite"></div>

                <template x-if="topics.length === 0">
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-shield-off" style="font-size:2.5rem;opacity:.3"></i>
                        <div class="mt-2">No hay tópicos restringidos configurados.</div>
                    </div>
                </template>

                <div class="row g-3">
                    <template x-for="topic in topics" :key="topic.id">
                        <div class="col-md-6 col-xl-4">
                            <div class="rtm-card" :class="topic.is_active ? '' : 'rtm-card--inactive'">
                                <div class="rtm-card-header">
                                    <div class="rtm-card-icon"><i class="ti ti-shield-lock"></i></div>
                                    <div class="rtm-card-title" x-text="topic.topic"></div>
                                    <span class="rtm-status-badge"
                                          :class="topic.is_active ? 'rtm-status-active' : 'rtm-status-inactive'"
                                          x-text="topic.is_active ? 'Activo' : 'Inactivo'">
                                    </span>
                                </div>
                                <div class="rtm-keywords">
                                    <template x-for="kw in topic.keywords" :key="kw">
                                        <span class="rtm-keyword-pill" x-text="kw"></span>
                                    </template>
                                </div>
                                <p class="rtm-response" x-text="topic.response_message"></p>
                                <div class="rtm-card-footer">
                                    <button type="button" class="rtm-btn-edit"
                                            data-bs-toggle="modal" data-bs-target="#topicFormModal"
                                            @click="openEdit(topic)">
                                        <i class="ti ti-pencil"></i> Editar
                                    </button>
                                    <button type="button" class="rtm-btn-delete"
                                            @click="deleteTopic(topic.id)">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ─── Create / Edit Modal ────────────────────────────────────────── --}}
<div class="modal fade" id="topicFormModal" tabindex="-1" aria-hidden="true" x-data>
    <div class="modal-dialog modal-lg">
        <div class="modal-content rtm-modal-content">

            <div class="rtm-modal-header">
                <div class="rtm-modal-icon"><i class="ti ti-shield-lock"></i></div>
                <div>
                    <h5 class="rtm-modal-title mb-0" id="topicModalTitle">Nuevo Tópico Restringido</h5>
                    <p class="rtm-modal-subtitle mb-0">Configura palabras clave y la respuesta automática del chatbot</p>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body rtm-modal-body">
                <div id="topicFormFeedback" class="alert d-none" role="alert"></div>

                <div class="rtm-form-section">
                    <div class="rtm-form-section-title"><i class="ti ti-tag"></i> Identificación</div>
                    <label class="rtm-label">Nombre del tópico <span class="text-danger">*</span></label>
                    <input type="text" class="form-control rtm-input" id="topicName"
                           placeholder="Ej: Precios exactos" maxlength="255">
                    <div class="form-text rtm-hint">Descripción interna del tipo de pregunta a bloquear.</div>
                </div>

                <div class="rtm-form-section">
                    <div class="rtm-form-section-title"><i class="ti ti-key"></i> Palabras clave <span class="text-danger">*</span></div>
                    <div id="keywordsContainer" class="mb-2"></div>
                    <button type="button" class="rtm-add-keyword-btn" id="addKeywordBtn">
                        <i class="ti ti-plus"></i> Agregar palabra clave
                    </button>
                    <div class="form-text rtm-hint mt-1">Si el mensaje del usuario contiene alguna de estas palabras, se activará el bloqueo.</div>
                </div>

                <div class="rtm-form-section">
                    <div class="rtm-form-section-title"><i class="ti ti-message-reply"></i> Respuesta automática</div>
                    <label class="rtm-label">Mensaje de respuesta <span class="text-danger">*</span></label>
                    <textarea class="form-control rtm-input" id="topicResponse" rows="4" maxlength="1000"
                              placeholder="Lo sentimos, no podemos proporcionar esa información por política de confidencialidad. Tu ejecutivo de cuenta te contactará."></textarea>
                    <div class="form-text rtm-hint">El chatbot responderá exactamente esto cuando detecte el tópico.</div>
                </div>

                <div class="rtm-form-section rtm-form-section-last">
                    <label class="rtm-toggle-label">
                        <input class="form-check-input" type="checkbox" id="topicIsActive" checked>
                        <div>
                            <div class="rtm-label mb-0">Estado activo</div>
                            <div class="form-text rtm-hint mb-0">El tópico se aplica en tiempo real al chatbot.</div>
                        </div>
                    </label>
                </div>

            </div>
            <div class="modal-footer rtm-modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4" id="saveTopicBtn">
                    <i class="ti ti-device-floppy me-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* ═══════════════════════════════════════════════════
   Restricted Topics Module (rtm)
   ═══════════════════════════════════════════════════ */

/* ── Topic cards ─────────────────────────────────── */
.rtm-card {
    background: var(--bs-body-bg);
    border: 1px solid var(--bs-border-color);
    border-radius: .75rem;
    padding: 1.125rem;
    display: flex;
    flex-direction: column;
    gap: .75rem;
    height: 100%;
    transition: box-shadow .18s, border-color .18s;
}
.rtm-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.1); border-color: rgba(var(--bs-primary-rgb),.35); }
.rtm-card--inactive { opacity: .55; }

.rtm-card-header {
    display: flex;
    align-items: center;
    gap: .6rem;
}
.rtm-card-icon {
    width: 2.25rem;
    height: 2.25rem;
    border-radius: .5rem;
    background: rgba(var(--bs-danger-rgb),.12);
    color: var(--bs-danger);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}
.rtm-card-title {
    font-size: .88rem;
    font-weight: 700;
    color: var(--bs-body-color);
    flex: 1;
    line-height: 1.3;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.rtm-status-badge {
    display: inline-flex;
    align-items: center;
    gap: .2rem;
    font-size: .68rem;
    font-weight: 700;
    padding: .2rem .6rem;
    border-radius: 999px;
    flex-shrink: 0;
}
.rtm-status-active   { background: rgba(113,221,55,.15); color: #71dd37; }
.rtm-status-inactive { background: rgba(168,177,204,.15); color: #a8b1cc; }

.rtm-keywords { display: flex; flex-wrap: wrap; gap: .35rem; }
.rtm-keyword-pill {
    display: inline-block;
    font-size: .72rem;
    font-weight: 600;
    padding: .2rem .6rem;
    border-radius: 999px;
    background: rgba(var(--bs-danger-rgb),.1);
    color: var(--bs-danger);
    border: 1.5px solid rgba(var(--bs-danger-rgb),.3);
    text-transform: lowercase;
}

.rtm-response {
    font-size: .79rem;
    color: var(--bs-secondary-color);
    line-height: 1.6;
    margin: 0;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.rtm-card-footer {
    display: flex;
    gap: .5rem;
    padding-top: .75rem;
    border-top: 1px solid var(--bs-border-color);
    margin-top: auto;
}
.rtm-btn-edit {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .35rem;
    padding: .4rem .75rem;
    font-size: .8rem;
    font-weight: 600;
    border-radius: .5rem;
    border: 1.5px solid var(--bs-border-color);
    background: transparent;
    color: var(--bs-body-color);
    cursor: pointer;
    transition: all .15s;
}
.rtm-btn-edit:hover {
    background: rgba(var(--bs-primary-rgb),.08);
    border-color: var(--bs-primary);
    color: var(--bs-primary);
}
.rtm-btn-delete {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.25rem;
    height: 2.25rem;
    border-radius: .5rem;
    border: 1.5px solid rgba(var(--bs-danger-rgb),.3);
    background: rgba(var(--bs-danger-rgb),.06);
    color: var(--bs-danger);
    cursor: pointer;
    transition: all .15s;
    font-size: .9rem;
}
.rtm-btn-delete:hover { background: rgba(var(--bs-danger-rgb),.15); border-color: var(--bs-danger); }

/* ── Form modal ──────────────────────────────────── */
#topicFormModal .rtm-modal-content {
    border-radius: .75rem;
    overflow: hidden;
    border: 1px solid var(--bs-border-color);
    box-shadow: 0 20px 60px rgba(0,0,0,.2);
    background: var(--bs-body-bg);
}

#topicFormModal .rtm-modal-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.25rem 1.5rem 1.125rem;
    background: linear-gradient(135deg, rgba(var(--bs-danger-rgb),.1) 0%, rgba(var(--bs-danger-rgb),.03) 100%);
    border-bottom: 1px solid var(--bs-border-color);
}
#topicFormModal .rtm-modal-icon {
    width: 2.75rem;
    height: 2.75rem;
    border-radius: .625rem;
    background: rgba(var(--bs-danger-rgb),.15);
    color: var(--bs-danger);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}
#topicFormModal .rtm-modal-title { font-size: 1rem; font-weight: 700; color: var(--bs-body-color); }
#topicFormModal .rtm-modal-subtitle { font-size: .78rem; color: var(--bs-secondary-color); margin-top: .2rem; }

#topicFormModal .rtm-modal-body { padding: 0; }

#topicFormModal .rtm-form-section {
    padding: 1.125rem 1.5rem;
    border-bottom: 1px solid var(--bs-border-color);
}
#topicFormModal .rtm-form-section-last { border-bottom: none; }

#topicFormModal .rtm-form-section-title {
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--bs-secondary-color);
    margin-bottom: .75rem;
    display: flex;
    align-items: center;
    gap: .35rem;
}
#topicFormModal .rtm-form-section-title i { color: var(--bs-danger); font-size: .85rem; }

#topicFormModal .rtm-label {
    display: block;
    font-size: .8rem;
    font-weight: 600;
    color: var(--bs-body-color);
    margin-bottom: .375rem;
}
#topicFormModal .rtm-hint { font-size: .74rem; color: var(--bs-secondary-color); }

#topicFormModal .rtm-input {
    background: var(--bs-body-bg);
    color: var(--bs-body-color);
    border-color: var(--bs-border-color);
}
#topicFormModal .rtm-input:focus {
    border-color: var(--bs-danger);
    box-shadow: 0 0 0 .25rem rgba(var(--bs-danger-rgb),.15);
}

#topicFormModal .rtm-add-keyword-btn {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .35rem .85rem;
    font-size: .8rem;
    font-weight: 600;
    border-radius: .5rem;
    border: 1.5px dashed rgba(var(--bs-primary-rgb),.4);
    background: rgba(var(--bs-primary-rgb),.06);
    color: var(--bs-primary);
    cursor: pointer;
    transition: all .15s;
}
#topicFormModal .rtm-add-keyword-btn:hover {
    background: rgba(var(--bs-primary-rgb),.12);
    border-style: solid;
}

#topicFormModal .rtm-toggle-label {
    display: flex;
    align-items: flex-start;
    gap: .75rem;
    cursor: pointer;
}
#topicFormModal .rtm-toggle-label .form-check-input { margin-top: .2rem; flex-shrink: 0; }

#topicFormModal .rtm-modal-footer {
    background: var(--bs-body-bg);
    border-top: 1px solid var(--bs-border-color);
    padding: 1rem 1.5rem;
}

/* Keyword input groups inside modal */
#topicFormModal .input-group .form-control {
    background: var(--bs-body-bg);
    color: var(--bs-body-color);
    border-color: var(--bs-border-color);
}

/* ── Dark mode ───────────────────────────────────── */
.dark-style .rtm-card {
    background: #2b2c40;
    border-color: rgba(255,255,255,.08);
}
.dark-style .rtm-card:hover { border-color: rgba(105,108,255,.4); box-shadow: 0 4px 20px rgba(0,0,0,.3); }
.dark-style .rtm-card-title { color: #d0d4e4; }
.dark-style .rtm-card-footer { border-top-color: rgba(255,255,255,.08); }
.dark-style .rtm-btn-edit {
    border-color: rgba(255,255,255,.12);
    color: #c8cee4;
}
.dark-style .rtm-btn-edit:hover { border-color: #696cff; color: #696cff; }
.dark-style .rtm-response { color: #8e98b8; }

.dark-style #topicFormModal .rtm-modal-content,
.dark-style #topicFormModal .rtm-modal-body,
.dark-style #topicFormModal .rtm-modal-footer { background: #2b2c40 !important; }
.dark-style #topicFormModal .rtm-modal-header {
    background: linear-gradient(135deg, rgba(255,62,29,.12) 0%, rgba(255,62,29,.03) 100%);
    border-bottom-color: rgba(255,255,255,.08);
}
.dark-style #topicFormModal .rtm-modal-title { color: #d0d4e4; }
.dark-style #topicFormModal .rtm-modal-subtitle { color: #8e98b8; }
.dark-style #topicFormModal .rtm-form-section { border-bottom-color: rgba(255,255,255,.08); }
.dark-style #topicFormModal .rtm-modal-footer { border-top-color: rgba(255,255,255,.08); }
.dark-style #topicFormModal .rtm-label { color: #c8cee4; }
.dark-style #topicFormModal .rtm-hint { color: #8e98b8; }
.dark-style #topicFormModal .rtm-input,
.dark-style #topicFormModal .input-group .form-control {
    background: #1e1e2d !important;
    color: #d0d4e4 !important;
    border-color: rgba(255,255,255,.12) !important;
}
.dark-style #topicFormModal .rtm-input::placeholder,
.dark-style #topicFormModal .input-group .form-control::placeholder { color: rgba(208,212,228,.35) !important; }
.dark-style #topicFormModal .input-group .btn-outline-danger {
    border-color: rgba(255,62,29,.3);
    color: #ff6b78;
    background: transparent;
}
.dark-style #topicFormModal .input-group .btn-outline-danger:hover { background: rgba(255,62,29,.12); }
.dark-style #topicFormModal .rtm-add-keyword-btn {
    border-color: rgba(105,108,255,.3);
    background: rgba(105,108,255,.08);
    color: #a8b1ff;
}
</style>
@endpush

@push('scripts')
<script>
// ─── Topics manager (Alpine component) ──────────────────────────────
function restrictedTopics() {
    return {
        topics: @json($topics),
        editingId: null,

        init() {
            // shared instance on page
        },

        initModal() {
            // sub-component for the modal – skipped, handled below
        },

        openCreate() {
            window._topicEditingId = null;
            document.getElementById('topicModalTitle').textContent = 'Nuevo Tópico Restringido';
            document.getElementById('topicName').value = '';
            document.getElementById('topicResponse').value = '';
            document.getElementById('topicIsActive').checked = true;
            renderKeywords([]);
        },

        openEdit(topic) {
            window._topicEditingId = topic.id;
            document.getElementById('topicModalTitle').textContent = 'Editar Tópico Restringido';
            document.getElementById('topicName').value = topic.topic;
            document.getElementById('topicResponse').value = topic.response_message;
            document.getElementById('topicIsActive').checked = topic.is_active;
            renderKeywords(topic.keywords || []);
        },

        async deleteTopic(id) {
            const isConfirmed = await window.EthosAlerts.confirm({
                title: 'Eliminar tópico restringido',
                text: 'Esta acción eliminará el tópico y su respuesta asociada.',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                danger: true,
            });

            if (!isConfirmed) return;

            try {
                const res = await fetch(`/admin/restricted-topics/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                const data = await res.json().catch(() => ({}));

                if (res.ok) {
                    this.topics = this.topics.filter(t => t.id !== id);
                    window.EthosAlerts.success(data.message || 'Tópico eliminado.');
                } else {
                    window.EthosAlerts.error(data.message || 'No se pudo eliminar el tópico.');
                }
            } catch {
                window.EthosAlerts.error('Error de conexión al eliminar el tópico.');
            }
        },
    };
}

// ─── Keyword tag helpers ─────────────────────────────────────────────
function renderKeywords(keywords) {
    const container = document.getElementById('keywordsContainer');
    container.innerHTML = '';
    keywords.forEach(kw => addKeywordInput(kw));
}

function addKeywordInput(value = '') {
    const container = document.getElementById('keywordsContainer');
    const wrapper = document.createElement('div');
    wrapper.className = 'input-group mb-2';
    wrapper.innerHTML = `
        <input type="text" class="form-control form-control-sm keyword-input"
               value="${escapeHtml(value)}" placeholder="Ej: precio exacto" maxlength="120">
        <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.input-group').remove()">
            <i class="ti ti-x"></i>
        </button>
    `;
    container.appendChild(wrapper);
}

function escapeHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function getKeywords() {
    return Array.from(document.querySelectorAll('.keyword-input'))
        .map(i => i.value.trim())
        .filter(v => v !== '');
}

document.getElementById('addKeywordBtn')?.addEventListener('click', () => addKeywordInput());

// ─── Save handler ────────────────────────────────────────────────────
document.getElementById('saveTopicBtn')?.addEventListener('click', async () => {
    const btn       = document.getElementById('saveTopicBtn');
    const feedback  = document.getElementById('topicFormFeedback');
    const editingId = window._topicEditingId;

    const payload = {
        topic:            document.getElementById('topicName').value.trim(),
        keywords:         getKeywords(),
        response_message: document.getElementById('topicResponse').value.trim(),
        is_active:        document.getElementById('topicIsActive').checked,
    };

    if (!payload.topic || payload.keywords.length === 0 || !payload.response_message) {
        feedback.className = 'alert alert-danger';
        feedback.textContent = 'Nombre, al menos una palabra clave y el mensaje son obligatorios.';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Guardando...';

    const url    = editingId ? `/admin/restricted-topics/${editingId}` : '/admin/restricted-topics';
    const method = editingId ? 'PUT' : 'POST';

    try {
        const res  = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(payload),
        });

        if (res.ok) {
            bootstrap.Modal.getInstance(document.getElementById('topicFormModal'))?.hide();
            window.location.reload();
        } else {
            const data = await res.json().catch(() => ({}));
            feedback.className = 'alert alert-danger';
            feedback.textContent = data.message || 'Error al guardar.';
        }
    } catch (e) {
        feedback.className = 'alert alert-danger';
        feedback.textContent = 'Error de conexión. Intenta nuevamente.';
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-device-floppy me-1"></i> Guardar';
    }
});
</script>
@endpush
