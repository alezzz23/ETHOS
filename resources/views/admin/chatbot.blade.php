{{-- ╔═══════════════════════════════════════════════════════════╗
    ║   ETHOS AI — Admin Dashboard Chatbot Widget              ║
    ║   Fase 2: bundle Vite (sin <style>/<script> inline)      ║
    ╚═══════════════════════════════════════════════════════════╝ --}}
@php
    $chatUser      = auth()->user();
    $chatInitials  = $chatUser?->initials ?? 'A';
    $chatAvatarUrl = $chatUser?->avatar_url ?? null;
@endphp

{{-- Config para el bundle JS --}}
<script>
    window.__ETHOS_CHAT__ = {
        streamUrl:        @json(route('admin.chat.stream')),
        formSchemaUrl:    @json(route('admin.chat.forms.schema')),
        chatUrl:          @json(route('admin.chat')),
        clearUrl:         @json(route('admin.chat.clear')),
        feedbackUrl:      @json(route('admin.chat.feedback')),
        conversationsUrl: @json(route('admin.chat.conversations.index')),
        userInitials:     @json($chatInitials),
        userAvatar:       @json($chatAvatarUrl),
    };
</script>

@vite(['resources/css/admin/chat.css', 'resources/js/admin/chat.js'])

{{-- ── Floating Toggle Button ── --}}
<button class="ethos-chat-toggle" id="ethosChatToggle" aria-label="Abrir asistente IA (Ctrl+K)" title="ETHOS AI — Ctrl+K">
    <i class="ti ti-robot ethos-chat-toggle-icon" id="ethosChatIcon"></i>
    <span class="ethos-chat-badge" id="ethosChatBadge" style="display:none;">!</span>
</button>

{{-- ── Chat Panel ── --}}
<div class="ethos-chat-panel" id="ethosChatPanel" aria-hidden="true" role="dialog" aria-label="Asistente IA ETHOS">

    {{-- ── Sidebar (conversaciones) ── --}}
    <aside class="ethos-chat-sidebar" id="ethosChatSidebar" aria-label="Historial de conversaciones">
        <div class="ethos-chat-sidebar-header">
            <span class="ethos-chat-sidebar-title">Conversaciones</span>
            <button class="ethos-chat-new-btn" id="ethosChatNewConv" title="Nueva conversación">
                <i class="ti ti-plus"></i> Nueva
            </button>
        </div>
        <div class="ethos-chat-conv-list" id="ethosChatConvList" role="list">
            <div class="ethos-chat-conv-empty">Cargando…</div>
        </div>
    </aside>

    {{-- ── Main column ── --}}
    <div class="ethos-chat-main">
        {{-- Header --}}
        <div class="ethos-chat-header">
            <div class="ethos-chat-header-info">
                <div class="ethos-chat-avatar-ai">
                    <i class="ti ti-robot"></i>
                </div>
                <div>
                    <div class="ethos-chat-title">ETHOS AI</div>
                    <div class="ethos-chat-subtitle" id="chatStatus">
                        <span class="ethos-chat-dot"></span> En línea
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-1">
                <button class="ethos-chat-header-btn" id="ethosChatSidebarToggle" title="Historial de conversaciones" aria-label="Mostrar historial">
                    <i class="ti ti-layout-sidebar"></i>
                </button>
                <button class="ethos-chat-header-btn" id="ethosChatExport" title="Exportar conversación (Markdown)" aria-label="Exportar conversación">
                    <i class="ti ti-download"></i>
                </button>
                <button class="ethos-chat-header-btn" id="ethosChatExpand" title="Expandir" aria-label="Expandir panel">
                    <i class="ti ti-arrows-diagonal"></i>
                </button>
                <button class="ethos-chat-header-btn" id="ethosChatStop" title="Detener respuesta" aria-label="Detener respuesta" style="display:none;">
                    <i class="ti ti-player-stop-filled"></i>
                </button>
                <button class="ethos-chat-header-btn" id="ethosChatClear" title="Limpiar conversación actual" aria-label="Limpiar conversación">
                    <i class="ti ti-trash"></i>
                </button>
                <button class="ethos-chat-header-btn" id="ethosChatClose" aria-label="Cerrar asistente">
                    <i class="ti ti-x"></i>
                </button>
            </div>
        </div>

        {{-- Messages area --}}
        <div class="ethos-chat-messages" id="ethosChatMessages" role="log" aria-live="polite">
            <div class="ethos-chat-welcome">
                <div class="ethos-chat-avatar-ai ethos-chat-avatar-lg">
                    <i class="ti ti-robot"></i>
                </div>
                <h6>¡Hola, {{ $chatUser?->name ?? 'Administrador' }}! 👋</h6>
                <p>Soy tu asistente IA. Puedo ayudarte con análisis de datos, gestión de usuarios, proyectos, clientes y más.</p>
                <div class="ethos-chat-suggestions">
                    <button class="ethos-chat-suggestion" data-msg="¿Cuántos clientes y proyectos hay actualmente?">📊 Ver estadísticas</button>
                    <button class="ethos-chat-suggestion" data-msg="¿Cuáles son los estados posibles de un proyecto y qué significa cada uno?">📁 Estados de proyectos</button>
                    <button class="ethos-chat-suggestion" data-msg="Dame un resumen ejecutivo del sistema ETHOS">📋 Resumen del sistema</button>
                    <button class="ethos-chat-suggestion" data-msg="¿Qué métricas son importantes para evaluar el desempeño de un consultor?">📈 Métricas de desempeño</button>
                </div>
            </div>

            <button class="ethos-chat-scroll-pill" id="ethosChatScrollPill" type="button" title="Ir al final">
                <i class="ti ti-arrow-down"></i> Nuevos mensajes
            </button>
        </div>

        {{-- Input area --}}
        <div class="ethos-chat-input-area">
            <div class="ethos-chat-input-wrap">
                <textarea
                    id="ethosChatInput"
                    class="ethos-chat-input"
                    placeholder="Escribe tu pregunta…"
                    rows="1"
                    maxlength="2000"
                    aria-label="Mensaje al asistente IA"
                ></textarea>
                <button class="ethos-chat-send" id="ethosChatSend" aria-label="Enviar mensaje" disabled>
                    <i class="ti ti-send"></i>
                </button>
            </div>
            <div class="ethos-chat-input-meta">
                <span id="chatCharCount">0/2000</span>
                <span>
                    <kbd>Enter</kbd> enviar · <kbd>Shift</kbd>+<kbd>Enter</kbd> nueva línea ·
                    <kbd>Ctrl</kbd>+<kbd>K</kbd> abrir
                </span>
            </div>
        </div>
    </div>
</div>
