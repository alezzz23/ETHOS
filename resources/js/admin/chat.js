/* ╔═════════════════════════════════════════════════════════════╗
   ║   ETHOS AI — Admin Chat widget (Fase 2)                    ║
   ║   Entry Vite: resources/js/admin/chat.js                   ║
   ╚═════════════════════════════════════════════════════════════╝ */

import { marked } from 'marked';
import DOMPurify from 'dompurify';

// ── Config inyectado por Blade en window.__ETHOS_CHAT__ ─────────
const cfg = window.__ETHOS_CHAT__ ?? {};

const csrf         = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const STREAM_URL   = cfg.streamUrl        ?? '/admin/chat/stream';
const CHAT_URL     = cfg.chatUrl          ?? '/admin/chat';
const CLEAR_URL    = cfg.clearUrl         ?? '/admin/chat/clear';
const FEEDBACK_URL = cfg.feedbackUrl      ?? '/admin/chat/feedback';
const CONV_INDEX   = cfg.conversationsUrl ?? '/admin/chat/conversations';
const CONV_SHOW    = (id) => `${CONV_INDEX}/${id}`;
const CONV_EXPORT  = (id) => `${CONV_INDEX}/${id}/export`;
const USER_INIT    = cfg.userInitials ?? 'A';
const USER_AVATAR  = cfg.userAvatar   ?? null;

// ── Config marked ───────────────────────────────────────────────
marked.setOptions({ breaks: true, gfm: true });

// ── Helpers ─────────────────────────────────────────────────────
const $ = (id) => document.getElementById(id);
const escapeHtml = (t) => { const d = document.createElement('div'); d.textContent = t; return d.innerHTML; };
const fmtTime = (d = new Date()) =>
    d.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });

function renderMarkdown(text) {
    const raw = marked.parse(text ?? '');
    return DOMPurify.sanitize(raw, {
        USE_PROFILES: { html: true },
        ALLOWED_TAGS: ['p','br','strong','em','code','pre','ul','ol','li','a','blockquote',
                       'h1','h2','h3','h4','h5','h6','hr','table','thead','tbody','tr','th','td','span'],
        ALLOWED_ATTR: ['href','title','target','rel','class'],
        ADD_ATTR: ['target'],
    });
}

// ── State ───────────────────────────────────────────────────────
const state = {
    history: [],
    conversationId: null,
    isLoading: false,
    isOpen: false,
    sidebarOpen: false,
    abortCtrl: null,
    conversations: [],       // lista de sidebar
    conversationsLoaded: false,
};

// ── DOM refs (resolved on init) ─────────────────────────────────
let el = {};

function resolveEl() {
    el = {
        toggle:    $('ethosChatToggle'),
        panel:     $('ethosChatPanel'),
        close:     $('ethosChatClose'),
        clear:     $('ethosChatClear'),
        stop:      $('ethosChatStop'),
        sidebar:   $('ethosChatSidebar'),
        sidebarToggle: $('ethosChatSidebarToggle'),
        convList:  $('ethosChatConvList'),
        newConv:   $('ethosChatNewConv'),
        input:     $('ethosChatInput'),
        send:      $('ethosChatSend'),
        messages:  $('ethosChatMessages'),
        icon:      $('ethosChatIcon'),
        charCount: $('chatCharCount'),
        scrollPill:$('ethosChatScrollPill'),
        exportBtn: $('ethosChatExport'),
        expandBtn: $('ethosChatExpand'),
    };
}

// ── Open/Close ──────────────────────────────────────────────────
function openChat() {
    state.isOpen = true;
    el.panel.classList.add('open');
    el.toggle.classList.add('open');
    el.panel.setAttribute('aria-hidden', 'false');
    if (el.icon) el.icon.className = 'ti ti-x ethos-chat-toggle-icon';
    setTimeout(() => el.input?.focus(), 250);
    // Lazy load conversations la primera vez que se abre
    if (!state.conversationsLoaded) loadConversations();
}
function closeChat() {
    state.isOpen = false;
    el.panel.classList.remove('open');
    el.toggle.classList.remove('open');
    el.panel.setAttribute('aria-hidden', 'true');
    if (el.icon) el.icon.className = 'ti ti-robot ethos-chat-toggle-icon';
}

// ── Sidebar ─────────────────────────────────────────────────────
function toggleSidebar() {
    state.sidebarOpen = !state.sidebarOpen;
    el.panel.classList.toggle('sidebar-open', state.sidebarOpen);
    el.panel.classList.toggle('expanded', state.sidebarOpen);
    el.sidebarToggle?.classList.toggle('is-active', state.sidebarOpen);
}

async function loadConversations() {
    try {
        const res = await fetch(CONV_INDEX, { headers: { Accept: 'application/json' } });
        if (!res.ok) return;
        const data = await res.json();
        state.conversations = data.conversations ?? [];
        state.conversationsLoaded = true;
        renderConversations();
    } catch { /* ignore */ }
}

function renderConversations() {
    if (!el.convList) return;
    el.convList.innerHTML = '';
    if (state.conversations.length === 0) {
        const empty = document.createElement('div');
        empty.className = 'ethos-chat-conv-empty';
        empty.textContent = 'Sin conversaciones previas.';
        el.convList.appendChild(empty);
        return;
    }
    for (const c of state.conversations) {
        const item = document.createElement('div');
        item.className = 'ethos-chat-conv-item';
        if (c.id === state.conversationId) item.classList.add('active');
        item.dataset.id = c.id;

        const title = document.createElement('div');
        title.className = 'ethos-chat-conv-title';
        title.textContent = c.title || 'Nueva conversación';
        const meta = document.createElement('div');
        meta.className = 'ethos-chat-conv-meta';
        meta.innerHTML =
            `<span>${c.message_count ?? 0} msg</span>` +
            `<span>${c.last_message_at ? new Date(c.last_message_at).toLocaleDateString('es-ES') : ''}</span>`;

        const actions = document.createElement('div');
        actions.className = 'ethos-chat-conv-actions';
        actions.innerHTML =
            `<button data-action="rename" title="Renombrar"><i class="ti ti-pencil"></i></button>` +
            `<button data-action="delete" title="Eliminar"><i class="ti ti-trash"></i></button>`;

        item.appendChild(title);
        item.appendChild(meta);
        item.appendChild(actions);

        item.addEventListener('click', (ev) => {
            const action = ev.target.closest('[data-action]')?.dataset.action;
            if (action === 'rename') return renameConversation(c);
            if (action === 'delete') return deleteConversation(c);
            openConversation(c.id);
        });

        el.convList.appendChild(item);
    }
}

async function openConversation(id) {
    try {
        const res = await fetch(CONV_SHOW(id), { headers: { Accept: 'application/json' } });
        if (!res.ok) return;
        const data = await res.json();
        state.conversationId = id;
        state.history = (data.messages ?? []).map(m => ({ role: m.role, content: m.content }));
        renderHistoryIntoPanel(data.messages ?? []);
        renderConversations();
    } catch { /* ignore */ }
}

function renderHistoryIntoPanel(messages) {
    el.messages.innerHTML = '';
    for (const m of messages) {
        const node = buildMessageNode(m.role, m.content, m.created_at ? fmtTime(new Date(m.created_at)) : fmtTime());
        if (m.role === 'assistant' && m.id) {
            node.root.dataset.logId = String(m.id);
            appendActionBar(node.root, { content: m.content, userMessage: findPrecedingUser(messages, m) });
        }
        el.messages.appendChild(node.root);
    }
    scrollToBottom(true);
    updateScrollPill();
}

function findPrecedingUser(messages, assistantMsg) {
    const idx = messages.indexOf(assistantMsg);
    for (let i = idx - 1; i >= 0; i--) {
        if (messages[i].role === 'user') return messages[i].content;
    }
    return '';
}

async function renameConversation(c) {
    const newTitle = prompt('Nuevo título:', c.title ?? '');
    if (!newTitle || newTitle === c.title) return;
    const res = await fetch(CONV_SHOW(c.id), {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
        body: JSON.stringify({ title: newTitle }),
    });
    if (res.ok) loadConversations();
}

async function deleteConversation(c) {
    if (!confirm(`¿Eliminar la conversación "${c.title ?? 'sin título'}"?`)) return;
    const res = await fetch(CONV_SHOW(c.id), {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
    });
    if (res.ok) {
        if (c.id === state.conversationId) {
            state.conversationId = null;
            state.history = [];
            showWelcome();
        }
        loadConversations();
    }
}

function exportCurrent() {
    if (!state.conversationId) {
        alert('Abre o inicia una conversación para exportar.');
        return;
    }
    window.location.href = CONV_EXPORT(state.conversationId);
}

// ── Messages rendering ──────────────────────────────────────────
function buildMessageNode(role, content, time) {
    const isUser = role === 'user';
    const div = document.createElement('div');
    div.className = 'ethos-chat-msg' + (isUser ? ' user-msg' : '');

    div.innerHTML = `
        <div class="ethos-chat-msg-avatar ${isUser ? 'user-avatar' : 'ai-avatar'}"></div>
        <div class="ethos-chat-msg-content">
            <div class="ethos-chat-bubble ${isUser ? 'user-bubble' : 'ai-bubble'}"></div>
            <div class="ethos-chat-time"></div>
        </div>
    `;

    const avatar = div.querySelector('.ethos-chat-msg-avatar');
    if (isUser) {
        if (USER_AVATAR) {
            const img = document.createElement('img');
            img.src = USER_AVATAR; img.alt = 'Avatar';
            avatar.appendChild(img);
        } else {
            avatar.textContent = USER_INIT;
        }
    } else {
        const icon = document.createElement('i');
        icon.className = 'ti ti-robot';
        avatar.appendChild(icon);
    }

    div.querySelector('.ethos-chat-time').textContent = time ?? fmtTime();
    const bubble = div.querySelector('.ethos-chat-bubble');
    if (isUser) bubble.textContent = content;
    else bubble.innerHTML = renderMarkdown(content);

    return { root: div, bubble };
}

function appendActionBar(msgRoot, { content, userMessage }) {
    if (msgRoot.querySelector('.ethos-chat-msg-actions')) return;
    const bar = document.createElement('div');
    bar.className = 'ethos-chat-msg-actions';
    bar.innerHTML = `
        <button data-action="copy"    title="Copiar"><i class="ti ti-copy"></i></button>
        <button data-action="regen"   title="Regenerar"><i class="ti ti-refresh"></i></button>
        <button data-action="fb-up"   data-rating="helpful"     title="Útil"><i class="ti ti-thumb-up"></i></button>
        <button data-action="fb-down" data-rating="not_helpful" title="No útil"><i class="ti ti-thumb-down"></i></button>
    `;
    bar.addEventListener('click', async (ev) => {
        const btn = ev.target.closest('button[data-action]');
        if (!btn) return;
        const action = btn.dataset.action;
        if (action === 'copy') {
            try { await navigator.clipboard.writeText(content); btn.classList.add('is-active'); setTimeout(() => btn.classList.remove('is-active'), 800); }
            catch { /* ignore */ }
        } else if (action === 'regen') {
            if (!userMessage) return;
            if (el.input) el.input.value = userMessage;
            sendMessage();
        } else if (action === 'fb-up' || action === 'fb-down') {
            if (btn.classList.contains('is-active')) return;
            const logId = parseInt(msgRoot.dataset.logId ?? '0', 10) || null;
            bar.querySelectorAll('[data-rating]').forEach(b => b.classList.remove('is-active'));
            btn.classList.add('is-active');
            fetch(FEEDBACK_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
                body: JSON.stringify({
                    admin_chat_log_id: logId,
                    rating: btn.dataset.rating,
                    context: 'dashboard',
                    user_message: userMessage,
                    assistant_message: content,
                }),
            }).catch(() => {});
        }
    });
    msgRoot.querySelector('.ethos-chat-msg-content').appendChild(bar);
}

function appendMessage(role, content, time) {
    const node = buildMessageNode(role, content, time);
    el.messages.appendChild(node.root);
    scrollToBottom();
    updateScrollPill();
    return node;
}

function appendStreamingAssistant() {
    const node = buildMessageNode('assistant', '', fmtTime());
    node.bubble.classList.add('is-streaming');
    el.messages.appendChild(node.root);
    scrollToBottom();
    return node;
}

function appendTyping() {
    const div = document.createElement('div');
    div.className = 'ethos-chat-msg ethos-chat-typing';
    div.id = 'chatTypingIndicator';
    div.innerHTML = `
        <div class="ethos-chat-msg-avatar ai-avatar"><i class="ti ti-robot"></i></div>
        <div class="typing-dots"><span></span><span></span><span></span></div>
    `;
    el.messages.appendChild(div);
    scrollToBottom();
    return div;
}
function removeTyping() { $('chatTypingIndicator')?.remove(); }

function showWelcome() {
    el.messages.innerHTML = `
        <div class="ethos-chat-welcome">
            <div class="ethos-chat-avatar-ai ethos-chat-avatar-lg"><i class="ti ti-robot"></i></div>
            <h6>Nueva conversación ✨</h6>
            <p>Escribe tu pregunta para comenzar.</p>
        </div>
    `;
}

function scrollToBottom(instant = false) {
    if (!el.messages) return;
    if (instant) { el.messages.scrollTop = el.messages.scrollHeight; return; }
    el.messages.scrollTop = el.messages.scrollHeight;
}

function isNearBottom() {
    if (!el.messages) return true;
    const threshold = 80;
    return (el.messages.scrollHeight - el.messages.scrollTop - el.messages.clientHeight) < threshold;
}

function updateScrollPill() {
    if (!el.scrollPill) return;
    if (isNearBottom()) el.scrollPill.classList.remove('visible');
    else                el.scrollPill.classList.add('visible');
}

function setLoading(v) {
    state.isLoading = v;
    if (el.send)  el.send.disabled  = v;
    if (el.input) el.input.disabled = v;
    if (el.stop)  el.stop.style.display = v ? 'inline-flex' : 'none';
}

function updateSendBtn() {
    if (!el.send) return;
    const val = el.input?.value.trim() ?? '';
    el.send.disabled = val === '' || state.isLoading;
}

// ── Slash commands (Fase 3.15) ──────────────────────────────────
const SLASH_COMMANDS = [
    { cmd: '/proyecto',  hint: '<id>',   desc: 'Estado de un proyecto' },
    { cmd: '/cliente',   hint: '<query>',desc: 'Buscar un cliente'     },
    { cmd: '/tareas',    hint: '',       desc: 'Tareas pendientes'     },
    { cmd: '/propuesta', hint: '<id>',   desc: 'Resumen de propuesta'  },
    { cmd: '/metricas',  hint: '',       desc: 'Métricas generales'    },
    { cmd: '/ayuda',     hint: '',       desc: 'Lista de comandos'     },
];

let slashMenuEl = null;
let slashSelectedIdx = 0;
let slashFiltered = [];

function ensureSlashMenu() {
    if (slashMenuEl) return slashMenuEl;
    slashMenuEl = document.createElement('div');
    slashMenuEl.className = 'ethos-chat-slash-menu';
    slashMenuEl.hidden = true;
    const container = el.input?.parentElement;
    if (container) {
        container.style.position = container.style.position || 'relative';
        container.appendChild(slashMenuEl);
    }
    return slashMenuEl;
}

function slashMenuVisible() {
    return slashMenuEl && !slashMenuEl.hidden;
}

function updateSlashMenu(value) {
    const menu = ensureSlashMenu();
    const v = (value || '').trimStart();
    if (!v.startsWith('/')) { menu.hidden = true; return; }
    const token = v.split(/\s+/)[0].toLowerCase();
    slashFiltered = SLASH_COMMANDS.filter(c => c.cmd.startsWith(token));
    if (!slashFiltered.length) { menu.hidden = true; return; }
    slashSelectedIdx = Math.min(slashSelectedIdx, slashFiltered.length - 1);
    menu.innerHTML = slashFiltered.map((c, i) => `
        <button type="button" class="ethos-chat-slash-item${i === slashSelectedIdx ? ' is-active' : ''}" data-cmd="${c.cmd}">
            <span class="cmd">${c.cmd}</span>
            <span class="hint">${c.hint}</span>
            <span class="desc">${c.desc}</span>
        </button>`).join('');
    menu.hidden = false;
    menu.querySelectorAll('.ethos-chat-slash-item').forEach(btn => {
        btn.addEventListener('click', () => applySlash(btn.dataset.cmd));
    });
}

function applySlash(cmd) {
    const c = SLASH_COMMANDS.find(x => x.cmd === cmd);
    if (!c) return;
    const hint = c.hint ? ' ' : '';
    if (el.input) {
        el.input.value = cmd + hint;
        el.input.focus();
        el.input.setSelectionRange(el.input.value.length, el.input.value.length);
    }
    if (slashMenuEl) slashMenuEl.hidden = true;
    updateSendBtn();
}

function handleSlashMenuKey(e) {
    if (!slashFiltered.length) return false;
    if (e.key === 'Escape') { slashMenuEl.hidden = true; e.preventDefault(); return true; }
    if (e.key === 'ArrowDown') {
        slashSelectedIdx = (slashSelectedIdx + 1) % slashFiltered.length;
        updateSlashMenu(el.input?.value || ''); e.preventDefault(); return true;
    }
    if (e.key === 'ArrowUp') {
        slashSelectedIdx = (slashSelectedIdx - 1 + slashFiltered.length) % slashFiltered.length;
        updateSlashMenu(el.input?.value || ''); e.preventDefault(); return true;
    }
    if (e.key === 'Enter' || e.key === 'Tab') {
        applySlash(slashFiltered[slashSelectedIdx].cmd);
        e.preventDefault(); return true;
    }
    return false;
}


// ── Send / stream ───────────────────────────────────────────────
async function sendMessage() {
    const message = el.input?.value.trim() ?? '';
    if (!message || state.isLoading) return;

    el.messages.querySelector('.ethos-chat-welcome')?.remove();
    if (el.input)     { el.input.value = ''; el.input.style.height = 'auto'; }
    if (el.charCount) { el.charCount.textContent = '0/2000'; }

    appendMessage('user', message);
    const typing = appendTyping();
    setLoading(true);

    state.abortCtrl = new AbortController();
    let streamNode = null;
    let accumulated = '';
    let finalReply  = '';
    let assistantLogId = null;

    try {
        const res = await fetch(STREAM_URL, {
            method: 'POST',
            signal: state.abortCtrl.signal,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                Accept: 'text/event-stream',
            },
            body: JSON.stringify({
                message,
                history: state.history,
                conversation_id: state.conversationId,
            }),
        });

        // Bloqueado por restricted topics → JSON, no SSE
        const ctype = res.headers.get('content-type') ?? '';
        if (!res.ok || !res.body || ctype.includes('application/json')) {
            removeTyping();
            const json = await res.json().catch(() => ({}));
            appendMessage('assistant', json.reply ?? ('⚠️ ' + (json.message ?? 'Error del asistente.')));
            return;
        }

        const reader  = res.body.getReader();
        const decoder = new TextDecoder('utf-8');
        let buf = '';

        while (true) {
            const { value, done } = await reader.read();
            if (done) break;
            buf += decoder.decode(value, { stream: true });

            let sep;
            while ((sep = buf.indexOf('\n\n')) !== -1) {
                const rawEvent = buf.slice(0, sep);
                buf = buf.slice(sep + 2);

                for (const line of rawEvent.split(/\r?\n/)) {
                    if (!line.startsWith('data:')) continue;
                    const payload = line.slice(5).trim();
                    if (!payload) continue;
                    let data;
                    try { data = JSON.parse(payload); } catch { continue; }

                    if (data.type === 'meta') {
                        state.conversationId = data.conversation_id;
                        removeTyping();
                        streamNode = appendStreamingAssistant();
                    } else if (data.type === 'delta') {
                        accumulated += data.content ?? '';
                        if (streamNode) {
                            streamNode.bubble.innerHTML = renderMarkdown(accumulated);
                            if (isNearBottom()) scrollToBottom();
                            else updateScrollPill();
                        }
                    } else if (data.type === 'done') {
                        finalReply = accumulated;
                        assistantLogId = data.admin_chat_log_id ?? null;
                        if (streamNode) {
                            streamNode.bubble.classList.remove('is-streaming');
                            if (assistantLogId) streamNode.root.dataset.logId = String(assistantLogId);
                            appendActionBar(streamNode.root, { content: finalReply, userMessage: message });
                        }
                    } else if (data.type === 'error') {
                        removeTyping();
                        if (streamNode) {
                            streamNode.bubble.classList.remove('is-streaming');
                            streamNode.bubble.textContent = '⚠️ ' + (data.message ?? 'Error del asistente.');
                        } else {
                            appendMessage('assistant', '⚠️ ' + (data.message ?? 'Error del asistente.'));
                        }
                    } else if (data.type === 'tool_call') {
                        // Fase 3.14 — mostrar indicador de tool en ejecución
                        if (streamNode) {
                            const pill = document.createElement('div');
                            pill.className = 'ethos-chat-tool-pill';
                            pill.textContent = '🔧 ' + (data.name || 'herramienta');
                            streamNode.bubble.appendChild(pill);
                        }
                    } else if (data.type === 'tool_result') {
                        if (streamNode) {
                            const pills = streamNode.bubble.querySelectorAll('.ethos-chat-tool-pill');
                            const last  = pills[pills.length - 1];
                            if (last) last.classList.add('is-done');
                        }
                    }
                }
            }
        }
    } catch (err) {
        removeTyping();
        if (err?.name === 'AbortError') {
            if (streamNode) {
                streamNode.bubble.classList.remove('is-streaming');
                const note = document.createElement('div');
                note.className = 'ethos-chat-aborted-note';
                note.textContent = '(respuesta detenida)';
                streamNode.bubble.appendChild(note);
                appendActionBar(streamNode.root, { content: accumulated, userMessage: message });
            } else {
                appendMessage('assistant', '(respuesta detenida)');
            }
            finalReply = accumulated;
        } else {
            appendMessage('assistant', '⚠️ Error de red. Verifica tu conexión e inténtalo nuevamente.');
        }
    } finally {
        setLoading(false);
        state.abortCtrl = null;
        updateSendBtn();
        el.input?.focus();
        if (finalReply.trim() !== '') {
            state.history.push({ role: 'user', content: message });
            state.history.push({ role: 'assistant', content: finalReply });
            if (state.history.length > 30) state.history = state.history.slice(-30);
            // Refrescar lista sidebar (titular, last_message_at)
            loadConversations();
        }
    }
}

// ── Clear history ───────────────────────────────────────────────
async function clearCurrent() {
    if (!confirm('¿Borrar toda la conversación actual?')) return;
    state.history = [];
    state.conversationId = null;
    state.abortCtrl?.abort();
    showWelcome();
    await fetch(CLEAR_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
    }).catch(() => {});
}

// ── Bind ────────────────────────────────────────────────────────
function bindEvents() {
    el.toggle?.addEventListener('click', () => state.isOpen ? closeChat() : openChat());
    el.close?.addEventListener('click', closeChat);
    el.clear?.addEventListener('click', clearCurrent);
    el.stop?.addEventListener('click', () => state.abortCtrl?.abort());
    el.sidebarToggle?.addEventListener('click', toggleSidebar);
    el.newConv?.addEventListener('click', () => {
        state.history = [];
        state.conversationId = null;
        showWelcome();
        renderConversations();
        el.input?.focus();
    });
    el.exportBtn?.addEventListener('click', exportCurrent);
    el.expandBtn?.addEventListener('click', () => {
        el.panel.classList.toggle('expanded');
        el.expandBtn.classList.toggle('is-active', el.panel.classList.contains('expanded'));
    });
    el.send?.addEventListener('click', sendMessage);
    el.scrollPill?.addEventListener('click', () => { scrollToBottom(); updateScrollPill(); });
    el.messages?.addEventListener('scroll', updateScrollPill);

    el.input?.addEventListener('input', function () {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        if (el.charCount) el.charCount.textContent = `${this.value.length}/2000`;
        updateSendBtn();
        updateSlashMenu(this.value);
    });
    el.input?.addEventListener('keydown', function (e) {
        if (slashMenuVisible() && ['ArrowDown', 'ArrowUp', 'Enter', 'Escape', 'Tab'].includes(e.key)) {
            if (handleSlashMenuKey(e)) return;
        }
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (!el.send?.disabled) sendMessage();
        }
    });

    document.querySelectorAll('.ethos-chat-suggestion').forEach(btn => {
        btn.addEventListener('click', () => {
            if (state.isLoading) return;
            const msg = btn.dataset.msg;
            if (msg && el.input) { el.input.value = msg; sendMessage(); }
        });
    });

    // Global shortcuts
    document.addEventListener('keydown', (e) => {
        // Ctrl+K / ⌘+K — open/focus chat
        if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
            e.preventDefault();
            if (!state.isOpen) openChat();
            else el.input?.focus();
            return;
        }
        if (e.key === 'Escape' && state.isOpen) closeChat();
    });
}

function init() {
    resolveEl();
    if (!el.toggle || !el.panel) return;
    bindEvents();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
