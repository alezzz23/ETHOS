{{-- ╔═══════════════════════════════════════════════════════════╗
    ║   ETHOS AI — Admin Dashboard Chatbot Widget              ║
    ║   Incluido en el layout vuexy.blade.php (@include)       ║
    ╚═══════════════════════════════════════════════════════════╝ --}}
@php
    $chatUser = auth()->user();
    $chatInitials = $chatUser?->initials ?? 'A';
    $chatAvatarUrl = $chatUser?->avatar_url ?? null;
@endphp

{{-- ── Floating Toggle Button ── --}}
<button class="ethos-chat-toggle" id="ethosChatToggle" aria-label="Abrir asistente IA" title="ETHOS AI">
    <i class="ti ti-robot ethos-chat-toggle-icon" id="ethosChatIcon"></i>
    <span class="ethos-chat-badge" id="ethosChatBadge" style="display:none;">!</span>
</button>

{{-- ── Chat Panel ── --}}
<div class="ethos-chat-panel" id="ethosChatPanel" aria-hidden="true" role="dialog" aria-label="Asistente IA ETHOS">
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
            <button class="ethos-chat-header-btn" id="ethosChatClear" title="Limpiar conversación" aria-label="Limpiar historial">
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
    </div>

    {{-- Input area --}}
    <div class="ethos-chat-input-area">
        <div class="ethos-chat-input-wrap">
            <textarea
                id="ethosChatInput"
                class="ethos-chat-input"
                placeholder="Escribe tu pregunta..."
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
            <span>Enter para enviar · Shift+Enter nueva línea</span>
        </div>
    </div>
</div>

{{-- ── Styles ── --}}
<style>
/* ── Variables ── */
:root {
    --chat-w: 400px;
    --chat-h: 580px;
    --chat-radius: 1.25rem;
    --chat-z: 1080;
    --chat-accent: var(--vz-primary);
    --chat-accent-rgb: var(--vz-primary-rgb);
}

/* ── Toggle Button ── */
.ethos-chat-toggle {
    position: fixed;
    bottom: 1.75rem;
    right: 1.75rem;
    z-index: var(--chat-z);
    width: 58px;
    height: 58px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(135deg, var(--vz-primary) 0%, #0051c3 100%);
    color: #fff;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 8px 28px -6px rgba(var(--chat-accent-rgb), 0.65),
                0 0 0 0 rgba(var(--chat-accent-rgb), 0.4);
    transition: transform 0.25s cubic-bezier(.2,.9,.2,1), box-shadow 0.25s;
    animation: chatPulse 2.8s infinite;
}

@keyframes chatPulse {
    0%, 100% { box-shadow: 0 8px 28px -6px rgba(var(--vz-primary-rgb), 0.65), 0 0 0 0 rgba(var(--vz-primary-rgb), 0.35); }
    50%       { box-shadow: 0 8px 28px -6px rgba(var(--vz-primary-rgb), 0.65), 0 0 0 10px rgba(var(--vz-primary-rgb), 0); }
}

.ethos-chat-toggle:hover {
    transform: scale(1.1);
    box-shadow: 0 12px 35px -6px rgba(var(--vz-primary-rgb), 0.8);
    animation: none;
}

.ethos-chat-toggle.open {
    animation: none;
    transform: rotate(0deg);
}

.ethos-chat-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: var(--vz-danger);
    color: #fff;
    font-size: 0.65rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid var(--vz-body-bg);
}

/* ── Chat Panel ── */
.ethos-chat-panel {
    position: fixed;
    bottom: calc(1.75rem + 68px);
    right: 1.75rem;
    z-index: var(--chat-z);
    width: var(--chat-w);
    height: var(--chat-h);
    border-radius: var(--chat-radius);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid rgba(var(--vz-primary-rgb), 0.25);
    box-shadow:
        0 32px 80px -20px rgba(0, 0, 0, 0.5),
        0 0 0 1px rgba(var(--vz-primary-rgb), 0.08),
        inset 0 1px 0 rgba(255,255,255,0.06);
    background: var(--vz-card-bg);
    transform: translateY(20px) scale(0.96);
    opacity: 0;
    pointer-events: none;
    transition: transform 0.3s cubic-bezier(.2,.9,.2,1), opacity 0.3s ease;
}

.ethos-chat-panel.open {
    transform: translateY(0) scale(1);
    opacity: 1;
    pointer-events: all;
}

/* ── Header ── */
.ethos-chat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.1rem;
    background: linear-gradient(135deg,
        rgba(var(--vz-primary-rgb), 0.22) 0%,
        rgba(var(--vz-info-rgb), 0.12) 100%);
    border-bottom: 1px solid rgba(var(--vz-primary-rgb), 0.18);
    flex-shrink: 0;
}

.ethos-chat-header-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.ethos-chat-avatar-ai {
    width: 38px;
    height: 38px;
    border-radius: 0.7rem;
    background: linear-gradient(135deg, var(--vz-primary) 0%, #0051c3 100%);
    color: #fff;
    font-size: 1.15rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 4px 14px -4px rgba(var(--vz-primary-rgb), 0.5);
}

.ethos-chat-avatar-lg {
    width: 56px;
    height: 56px;
    font-size: 1.6rem;
    border-radius: 1rem;
    margin: 0 auto 0.75rem;
}

.ethos-chat-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--vz-heading-color);
    letter-spacing: 0.01em;
}

.ethos-chat-subtitle {
    font-size: 0.75rem;
    color: var(--vz-body-color);
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.ethos-chat-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--vz-success);
    box-shadow: 0 0 0 2px rgba(40, 199, 111, 0.25);
    animation: dotPulse 2s ease-in-out infinite;
    display: inline-block;
}

@keyframes dotPulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}

.ethos-chat-header-btn {
    background: none;
    border: none;
    color: var(--vz-body-color);
    font-size: 1rem;
    width: 30px;
    height: 30px;
    border-radius: 0.4rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.15s, color 0.15s;
}

.ethos-chat-header-btn:hover {
    background: rgba(var(--vz-primary-rgb), 0.1);
    color: var(--vz-primary);
}

/* ── Messages ── */
.ethos-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    scroll-behavior: smooth;
}

.ethos-chat-messages::-webkit-scrollbar { width: 4px; }
.ethos-chat-messages::-webkit-scrollbar-track { background: transparent; }
.ethos-chat-messages::-webkit-scrollbar-thumb { background: rgba(var(--vz-primary-rgb), 0.2); border-radius: 9px; }

/* ── Welcome ── */
.ethos-chat-welcome {
    text-align: center;
    padding: 1.25rem 0.5rem;
    animation: fadeUpIn 0.4s ease;
}

.ethos-chat-welcome h6 {
    font-size: 1rem;
    font-weight: 700;
    color: var(--vz-heading-color);
    margin-bottom: 0.4rem;
}

.ethos-chat-welcome p {
    font-size: 0.82rem;
    color: var(--vz-body-color);
    margin-bottom: 1rem;
    line-height: 1.5;
}

.ethos-chat-suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    justify-content: center;
}

.ethos-chat-suggestion {
    background: rgba(var(--vz-primary-rgb), 0.07);
    border: 1px solid rgba(var(--vz-primary-rgb), 0.18);
    color: var(--vz-primary);
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 999px;
    padding: 0.3rem 0.75rem;
    cursor: pointer;
    transition: background 0.15s, transform 0.15s;
    white-space: nowrap;
    text-align: left;
}

.ethos-chat-suggestion:hover {
    background: rgba(var(--vz-primary-rgb), 0.14);
    transform: translateY(-1px);
}

/* ── Message bubbles ── */
.ethos-chat-msg {
    display: flex;
    gap: 0.6rem;
    align-items: flex-end;
    animation: fadeUpIn 0.3s ease;
    max-width: 100%;
}

.ethos-chat-msg.user-msg { flex-direction: row-reverse; }

.ethos-chat-msg-avatar {
    width: 28px;
    height: 28px;
    border-radius: 0.5rem;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    overflow: hidden;
}

.ethos-chat-msg-avatar.ai-avatar {
    background: linear-gradient(135deg, var(--vz-primary) 0%, #0051c3 100%);
    color: #fff;
}

.ethos-chat-msg-avatar.user-avatar {
    background: rgba(var(--vz-primary-rgb), 0.12);
    color: var(--vz-primary);
    border: 1px solid rgba(var(--vz-primary-rgb), 0.2);
}

.ethos-chat-msg-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.ethos-chat-bubble {
    max-width: calc(100% - 44px);
    padding: 0.6rem 0.85rem;
    border-radius: 1rem;
    font-size: 0.85rem;
    line-height: 1.55;
    word-break: break-word;
    position: relative;
}

.ai-bubble {
    background: rgba(var(--vz-primary-rgb), 0.06);
    border: 1px solid rgba(var(--vz-primary-rgb), 0.12);
    color: var(--vz-body-color);
    border-bottom-left-radius: 0.3rem;
}

.user-bubble {
    background: var(--vz-primary);
    color: #fff;
    border-bottom-right-radius: 0.3rem;
    box-shadow: 0 4px 14px -4px rgba(var(--vz-primary-rgb), 0.45);
}

.ethos-chat-time {
    font-size: 0.68rem;
    color: var(--vz-body-color);
    opacity: 0.55;
    margin-top: 0.2rem;
    padding: 0 0.2rem;
}
.user-msg .ethos-chat-time { text-align: right; }

/* Markdown-like formatting in AI responses */
.ai-bubble strong { font-weight: 700; color: var(--vz-heading-color); }
.ai-bubble code {
    background: rgba(var(--vz-primary-rgb), 0.1);
    padding: 0.1em 0.3em;
    border-radius: 3px;
    font-size: 0.8rem;
}
.ai-bubble ul, .ai-bubble ol { padding-left: 1.2em; }
.ai-bubble li { margin-bottom: 0.2em; }
.ai-bubble p { margin-bottom: 0.4em; }
.ai-bubble p:last-child { margin-bottom: 0; }

/* ── Typing indicator ── */
.ethos-chat-typing {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    animation: fadeUpIn 0.3s ease;
}

.typing-dots {
    display: flex;
    gap: 4px;
    padding: 0.6rem 0.85rem;
    background: rgba(var(--vz-primary-rgb), 0.06);
    border: 1px solid rgba(var(--vz-primary-rgb), 0.12);
    border-radius: 1rem;
    border-bottom-left-radius: 0.3rem;
}

.typing-dots span {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--vz-primary);
    opacity: 0.4;
    animation: typingBounce 1s ease-in-out infinite;
}
.typing-dots span:nth-child(2) { animation-delay: 0.15s; }
.typing-dots span:nth-child(3) { animation-delay: 0.3s; }

@keyframes typingBounce {
    0%, 100% { transform: translateY(0); opacity: 0.4; }
    50%       { transform: translateY(-4px); opacity: 1; }
}

/* ── Input Area ── */
.ethos-chat-input-area {
    padding: 0.75rem;
    border-top: 1px solid var(--vz-border-color);
    background: rgba(var(--vz-primary-rgb), 0.018);
    flex-shrink: 0;
}

.ethos-chat-input-wrap {
    display: flex;
    align-items: flex-end;
    gap: 0.5rem;
    background: var(--vz-card-bg);
    border: 1px solid rgba(var(--vz-primary-rgb), 0.2);
    border-radius: 0.9rem;
    padding: 0.5rem 0.5rem 0.5rem 0.8rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.ethos-chat-input-wrap:focus-within {
    border-color: var(--vz-primary);
    box-shadow: 0 0 0 3px rgba(var(--vz-primary-rgb), 0.18);
}

.ethos-chat-input {
    flex: 1;
    background: none;
    border: none;
    outline: none;
    resize: none;
    color: var(--vz-heading-color);
    font-size: 0.875rem;
    font-family: var(--vz-font-family);
    line-height: 1.5;
    max-height: 120px;
    overflow-y: auto;
    min-height: 24px;
}

.ethos-chat-input::placeholder { color: var(--vz-body-color); opacity: 0.5; }

.ethos-chat-send {
    width: 34px;
    height: 34px;
    border-radius: 0.65rem;
    border: none;
    background: var(--vz-primary);
    color: #fff;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    transition: background 0.15s, transform 0.15s, opacity 0.15s;
}

.ethos-chat-send:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.ethos-chat-send:not(:disabled):hover {
    transform: scale(1.08);
    background: #0051c3;
}

.ethos-chat-input-meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.68rem;
    color: var(--vz-body-color);
    opacity: 0.5;
    margin-top: 0.3rem;
    padding: 0 0.2rem;
}

/* ── Dark mode specific ── */
.dark-style .ethos-chat-panel {
    background: linear-gradient(180deg, #0e1525 0%, #090f1d 100%);
    border-color: rgba(var(--vz-primary-rgb), 0.35);
}

.dark-style .ethos-chat-input-wrap {
    background: rgba(26, 31, 48, 0.95);
    border-color: rgba(var(--vz-primary-rgb), 0.3);
}

.dark-style .ethos-chat-input {
    color: #e5e8ff;
}

.dark-style .ai-bubble {
    background: rgba(var(--vz-primary-rgb), 0.1);
    border-color: rgba(var(--vz-primary-rgb), 0.2);
}

/* ── Animations ── */
@keyframes fadeUpIn {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Responsive ── */
@media (max-width: 480px) {
    .ethos-chat-panel {
        width: calc(100vw - 1.5rem);
        right: 0.75rem;
        bottom: calc(1rem + 68px);
    }
    .ethos-chat-toggle {
        right: 1rem;
        bottom: 1rem;
    }
}
</style>

{{-- ── Scripts ── --}}
<script>
function initEthosChat() {
    'use strict';

    const csrf        = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const CHAT_URL    = '{{ route("admin.chat") }}';
    const CLEAR_URL   = '{{ route("admin.chat.clear") }}';
    const USER_INIT   = @json($chatInitials);
    const USER_AVATAR = @json($chatAvatarUrl);

    let history      = [];
    let isLoading    = false;
    let isOpen       = false;

    // Elements
    const toggle    = document.getElementById('ethosChatToggle');
    const panel     = document.getElementById('ethosChatPanel');
    const closeBtn  = document.getElementById('ethosChatClose');
    const clearBtn  = document.getElementById('ethosChatClear');
    const input     = document.getElementById('ethosChatInput');
    const sendBtn   = document.getElementById('ethosChatSend');
    const messages  = document.getElementById('ethosChatMessages');
    const icon      = document.getElementById('ethosChatIcon');

    if (!toggle || !panel) return; // Prevent crashes if elements are missing

    // ── Open/Close ──────────────────────────────────────────────────
    function openChat() {
        isOpen = true;
        panel.classList.add('open');
        toggle.classList.add('open');
        panel.setAttribute('aria-hidden', 'false');
        icon.className = 'ti ti-x ethos-chat-toggle-icon';
        setTimeout(() => input?.focus(), 300);
    }

    function closeChat() {
        isOpen = false;
        panel.classList.remove('open');
        toggle.classList.remove('open');
        panel.setAttribute('aria-hidden', 'true');
        icon.className = 'ti ti-robot ethos-chat-toggle-icon';
    }

    toggle.addEventListener('click', () => isOpen ? closeChat() : openChat());
    closeBtn?.addEventListener('click', closeChat);

    // ── Input handling ───────────────────────────────────────────────
    function updateSendBtn() {
        const val = input?.value.trim() ?? '';
        if (sendBtn) sendBtn.disabled = val === '' || isLoading;
    }

    input?.addEventListener('input', function () {
        // Auto-resize
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        // Char count
        const charEl = document.getElementById('chatCharCount');
        if (charEl) charEl.textContent = `${this.value.length}/2000`;
        updateSendBtn();
    });

    input?.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (!sendBtn?.disabled) sendMessage();
        }
    });

    // ── Suggestions ──────────────────────────────────────────────────
    document.querySelectorAll('.ethos-chat-suggestion').forEach(btn => {
        btn.addEventListener('click', () => {
            if (isLoading) return;
            const msg = btn.dataset.msg;
            if (msg && input) {
                input.value = msg;
                sendMessage();
            }
        });
    });

    // ── Render helpers ───────────────────────────────────────────────
    function formatTime() {
        return new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
    }

    function markdownToHtml(text) {
        return text
            // Code blocks
            .replace(/```[\s\S]*?```/g, m => `<pre><code>${m.slice(3, -3).trim()}</code></pre>`)
            // Inline code
            .replace(/`([^`]+)`/g, '<code>$1</code>')
            // Bold
            .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
            // Italic
            .replace(/\*([^*]+)\*/g, '<em>$1</em>')
            // Unordered lists
            .replace(/^[\s]*[-*] (.+)$/gm, '<li>$1</li>')
            .replace(/(<li>[\s\S]*?<\/li>)/g, '<ul>$1</ul>')
            // Ordered lists
            .replace(/^\d+\. (.+)$/gm, '<li>$1</li>')
            // Paragraphs
            .replace(/\n\n/g, '</p><p>')
            .replace(/\n/g, '<br>')
            .replace(/^(.+)$/, '<p>$1</p>');
    }

    function appendMessage(role, content, time) {
        const isUser = role === 'user';
        const avatarHtml = isUser
            ? (USER_AVATAR
                ? `<img src="${USER_AVATAR}" alt="Avatar">`
                : USER_INIT)
            : '<i class="ti ti-robot"></i>';

        const avatarClass = isUser ? 'user-avatar' : 'ai-avatar';
        const bubbleClass = isUser ? 'user-bubble' : 'ai-bubble';
        const msgClass    = isUser ? 'user-msg' : '';

        const formattedContent = isUser
            ? content.replace(/\x26/g,'\x26amp;').replace(/\x3c/g,'\x26lt;').replace(/\x3e/g,'\x26gt;')
            : markdownToHtml(content);

        const div = document.createElement('div');
        div.className = `ethos-chat-msg ${msgClass}`;
        div.innerHTML = `
            <div class="ethos-chat-msg-avatar ${avatarClass}">${avatarHtml}</div>
            <div>
                <div class="ethos-chat-bubble ${bubbleClass}">${formattedContent}</div>
                <div class="ethos-chat-time">${time ?? formatTime()}</div>
            </div>
        `;
        messages.appendChild(div);
        scrollToBottom();
        return div;
    }

    function appendTyping() {
        const div = document.createElement('div');
        div.className = 'ethos-chat-msg ethos-chat-typing';
        div.id = 'chatTypingIndicator';
        div.innerHTML = `
            <div class="ethos-chat-msg-avatar ai-avatar"><i class="ti ti-robot"></i></div>
            <div class="typing-dots"><span></span><span></span><span></span></div>
        `;
        messages.appendChild(div);
        scrollToBottom();
        return div;
    }

    function removeTyping() {
        document.getElementById('chatTypingIndicator')?.remove();
    }

    function scrollToBottom() {
        messages.scrollTop = messages.scrollHeight;
    }

    function setLoading(val) {
        isLoading = val;
        if (sendBtn) sendBtn.disabled = val;
        if (input)   input.disabled  = val;
    }

    // ── Send message ─────────────────────────────────────────────────
    async function sendMessage() {
        const message = input?.value.trim() ?? '';
        if (!message || isLoading) return;

        // Clear welcome if exists
        messages.querySelector('.ethos-chat-welcome')?.remove();

        // Reset input
        if (input) { input.value = ''; input.style.height = 'auto'; }
        const charEl = document.getElementById('chatCharCount');
        if (charEl) charEl.textContent = '0/2000';

        appendMessage('user', message);
        const typing = appendTyping();
        setLoading(true);

        try {
            const res = await fetch(CHAT_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ message, history }),
            });

            removeTyping();

            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                appendMessage('assistant', `⚠️ ${err.message ?? 'Error de conexión con el asistente.'}`);
                return;
            }

            const data = await res.json();
            appendMessage('assistant', data.reply);

            // Update history
            history.push({ role: 'user', content: message });
            history.push({ role: 'assistant', content: data.reply });
            if (history.length > 30) history = history.slice(-30);

        } catch (err) {
            removeTyping();
            appendMessage('assistant', '⚠️ Error de red. Verifica tu conexión e inténtalo nuevamente.');
        } finally {
            setLoading(false);
            updateSendBtn();
            input?.focus();
        }
    }

    sendBtn?.addEventListener('click', sendMessage);

    // ── Clear conversation ───────────────────────────────────────────
    clearBtn?.addEventListener('click', async () => {
        if (!confirm('¿Borrar toda la conversación?')) return;

        history = [];
        messages.innerHTML = `
            <div class="ethos-chat-welcome" style="animation: fadeUpIn 0.4s ease;">
                <div class="ethos-chat-avatar-ai ethos-chat-avatar-lg"><i class="ti ti-robot"></i></div>
                <h6>Conversación limpiada ✓</h6>
                <p>Puedes comenzar una nueva consulta.</p>
            </div>
        `;

        await fetch(CLEAR_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        }).catch(() => {});
    });

    // ── ESC to close ─────────────────────────────────────────────────
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && isOpen) closeChat();
    });
}

// Call init when document is fully loaded or immediately if already loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initEthosChat);
} else {
    initEthosChat();
}
</script>
