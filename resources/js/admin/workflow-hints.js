import { modal } from './sweet-alerts';

const FLASH_KEY = 'ethos.workflow.flash';
const CARD_PREFIX = 'ethos.workflow.card.';

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function activateTabFromHash(hash = window.location.hash) {
    if (!hash || !window.bootstrap?.Tab) {
        return false;
    }

    const trigger = document.querySelector(`[data-bs-target="${hash}"]`);

    if (!trigger) {
        return false;
    }

    window.bootstrap.Tab.getOrCreateInstance(trigger).show();

    return true;
}

function buildModalHtml(payload = {}) {
    const description = payload.description
        ? `<p class="ethos-workflow-modal__copy">${escapeHtml(payload.description)}</p>`
        : '';

    const steps = Array.isArray(payload.steps) && payload.steps.length > 0
        ? `<ol class="ethos-workflow-modal__steps">${payload.steps
            .map((step) => `<li>${escapeHtml(step)}</li>`)
            .join('')}</ol>`
        : '';

    const note = payload.note
        ? `<div class="ethos-workflow-modal__note">${escapeHtml(payload.note)}</div>`
        : '';

    return `<div class="ethos-workflow-modal">${description}${steps}${note}</div>`;
}

function rememberWorkflowHint(payload) {
    if (typeof window === 'undefined' || !payload?.title) {
        return;
    }

    try {
        window.sessionStorage.setItem(FLASH_KEY, JSON.stringify(payload));
    } catch {
        // Ignore session storage failures.
    }
}

function consumeWorkflowHint() {
    if (typeof window === 'undefined') {
        return null;
    }

    try {
        const raw = window.sessionStorage.getItem(FLASH_KEY);

        if (!raw) {
            return null;
        }

        window.sessionStorage.removeItem(FLASH_KEY);

        return JSON.parse(raw);
    } catch {
        return null;
    }
}

async function showWorkflowHint(payload = {}) {
    if (!payload?.title) {
        return null;
    }

    if (payload.focusTab) {
        activateTabFromHash(payload.focusTab);
    }

    const result = await modal({
        title: payload.title,
        html: buildModalHtml(payload),
        icon: payload.icon || 'info',
        confirmButtonText: payload.confirmButtonText || 'Entendido',
        showCancelButton: Boolean(payload.cancelButtonText),
        cancelButtonText: payload.cancelButtonText || 'Ahora no',
    });

    if (result?.isConfirmed && payload.confirmUrl) {
        window.location.assign(payload.confirmUrl);
    }

    if (result?.dismiss === window.Swal?.DismissReason.cancel && payload.cancelUrl) {
        window.location.assign(payload.cancelUrl);
    }

    return result;
}

function dismissCard(card) {
    const storageKey = card?.dataset.workflowCard;

    if (storageKey) {
        try {
            window.localStorage.setItem(`${CARD_PREFIX}${storageKey}`, '1');
        } catch {
            // Ignore local storage failures.
        }
    }

    card?.remove();
}

function syncDismissedCards() {
    document.querySelectorAll('[data-workflow-card]').forEach((card) => {
        const storageKey = card.dataset.workflowCard;

        if (!storageKey) {
            return;
        }

        try {
            if (window.localStorage.getItem(`${CARD_PREFIX}${storageKey}`) === '1') {
                card.remove();
            }
        } catch {
            // Ignore local storage failures.
        }
    });
}

function bindDismissButtons() {
    document.addEventListener('click', (event) => {
        const dismissButton = event.target.closest('[data-workflow-dismiss]');

        if (!dismissButton) {
            return;
        }

        dismissCard(dismissButton.closest('.ethos-workflow-card'));
    });
}

function bootWorkflowHints() {
    syncDismissedCards();
    bindDismissButtons();
    activateTabFromHash();

    const storedHint = consumeWorkflowHint();

    if (storedHint) {
        window.requestAnimationFrame(() => showWorkflowHint(storedHint));
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootWorkflowHints, { once: true });
} else {
    bootWorkflowHints();
}

window.addEventListener('hashchange', () => activateTabFromHash());

window.EthosWorkflow = {
    activateTab: activateTabFromHash,
    consume: consumeWorkflowHint,
    dismissCard,
    remember: rememberWorkflowHint,
    show: showWorkflowHint,
};

export { activateTabFromHash, consumeWorkflowHint, rememberWorkflowHint, showWorkflowHint };

export default window.EthosWorkflow;