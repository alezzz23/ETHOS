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
                <button type="button" class="btn btn-primary ethos-create-btn" @click="openCreate()">
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
                            <div class="card h-100 shadow-sm" :class="topic.is_active ? '' : 'opacity-50'">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-0" x-text="topic.topic"></h6>
                                        <span class="badge"
                                              :class="topic.is_active ? 'bg-success' : 'bg-secondary'"
                                              x-text="topic.is_active ? 'Activo' : 'Inactivo'">
                                        </span>
                                    </div>
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        <template x-for="kw in topic.keywords" :key="kw">
                                            <span class="badge bg-label-danger" x-text="kw"></span>
                                        </template>
                                    </div>
                                    <p class="text-muted small mb-0" x-text="topic.response_message"></p>
                                </div>
                                <div class="card-footer bg-transparent d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary flex-fill"
                                            @click="openEdit(topic)">
                                        <i class="ti ti-pencil"></i> Editar
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
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
        <div class="modal-content" x-data="restrictedTopics()" x-init="initModal()">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-shield-lock me-2"></i>
                    <span id="topicModalTitle">Nuevo Tópico Restringido</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="topicFormFeedback" class="alert d-none" role="alert"></div>

                <div class="mb-3">
                    <label class="form-label">Nombre del tópico <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="topicName"
                           placeholder="Ej: Precios exactos" maxlength="255">
                    <div class="form-text">Descripción interna del tipo de pregunta a bloquear.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Palabras clave <span class="text-danger">*</span></label>
                    <div id="keywordsContainer"></div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addKeywordBtn">
                        <i class="ti ti-plus"></i> Agregar palabra clave
                    </button>
                    <div class="form-text">Si el mensaje del usuario contiene alguna de estas palabras, se activará el bloqueo.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mensaje de respuesta <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="topicResponse" rows="3" maxlength="1000"
                              placeholder="Lo sentimos, no podemos proporcionar esa información por política de confidencialidad. Tu ejecutivo de cuenta te contactará."></textarea>
                    <div class="form-text">El chatbot responderá exactamente esto cuando detecte el tópico.</div>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="topicIsActive" checked>
                    <label class="form-check-label" for="topicIsActive">Activo</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveTopicBtn">
                    <i class="ti ti-device-floppy me-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

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
            new bootstrap.Modal(document.getElementById('topicFormModal')).show();
        },

        openEdit(topic) {
            window._topicEditingId = topic.id;
            document.getElementById('topicModalTitle').textContent = 'Editar Tópico Restringido';
            document.getElementById('topicName').value = topic.topic;
            document.getElementById('topicResponse').value = topic.response_message;
            document.getElementById('topicIsActive').checked = topic.is_active;
            renderKeywords(topic.keywords || []);
            new bootstrap.Modal(document.getElementById('topicFormModal')).show();
        },

        async deleteTopic(id) {
            if (!confirm('¿Eliminar este tópico?')) return;
            const res = await fetch(`/admin/restricted-topics/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });
            if (res.ok) {
                this.topics = this.topics.filter(t => t.id !== id);
            } else {
                alert('No se pudo eliminar el tópico.');
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

    const res  = await fetch(url, {
        method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(payload),
    });

    btn.disabled = false;
    btn.innerHTML = '<i class="ti ti-device-floppy me-1"></i> Guardar';

    if (res.ok) {
        bootstrap.Modal.getInstance(document.getElementById('topicFormModal'))?.hide();
        window.location.reload();
    } else {
        const data = await res.json();
        feedback.className = 'alert alert-danger';
        feedback.textContent = data.message || 'Error al guardar.';
    }
});
</script>
@endpush
