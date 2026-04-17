import Swal from 'sweetalert2';

const baseCustomClass = {
    popup: 'ethos-swal-popup',
    title: 'ethos-swal-title',
    htmlContainer: 'ethos-swal-html',
    actions: 'ethos-swal-actions',
    confirmButton: 'btn btn-primary ethos-swal-confirm',
    cancelButton: 'btn btn-label-secondary ethos-swal-cancel',
    input: 'form-control ethos-swal-input',
    validationMessage: 'ethos-swal-validation',
};

let formsBound = false;

function buildCustomClass({ danger = false, customClass = {} } = {}) {
    return {
        ...baseCustomClass,
        ...customClass,
        confirmButton: customClass.confirmButton
            ?? `btn ${danger ? 'btn-danger' : 'btn-primary'} ethos-swal-confirm`,
        cancelButton: customClass.cancelButton ?? baseCustomClass.cancelButton,
    };
}

function fireModal(options = {}, meta = {}) {
    return Swal.fire({
        heightAuto: false,
        buttonsStyling: false,
        reverseButtons: options.showCancelButton === true,
        allowOutsideClick: () => !Swal.isLoading(),
        customClass: buildCustomClass(meta),
        ...options,
    });
}

export async function confirm(options = {}) {
    const {
        danger = false,
        title = '¿Confirmar acción?',
        text,
        html,
        icon = 'warning',
        confirmButtonText = 'Confirmar',
        cancelButtonText = 'Cancelar',
        customClass,
        ...rest
    } = options;

    const result = await fireModal({
        title,
        text,
        html,
        icon,
        showCancelButton: true,
        focusCancel: true,
        confirmButtonText,
        cancelButtonText,
        ...rest,
    }, { danger, customClass });

    return result.isConfirmed;
}

export async function modal(options = {}) {
    const {
        danger = false,
        title,
        text,
        html,
        icon = 'info',
        confirmButtonText = 'Entendido',
        customClass,
        ...rest
    } = options;

    return fireModal({
        title,
        text,
        html,
        icon,
        confirmButtonText,
        ...rest,
    }, { danger, customClass });
}

export async function promptText(options = {}) {
    const {
        title = 'Ingresa un valor',
        inputLabel,
        inputValue = '',
        inputPlaceholder,
        confirmButtonText = 'Guardar',
        cancelButtonText = 'Cancelar',
        customClass,
        requiredMessage = 'Este campo es obligatorio.',
        inputValidator,
        ...rest
    } = options;

    const result = await fireModal({
        title,
        input: 'text',
        inputLabel,
        inputValue,
        inputPlaceholder,
        showCancelButton: true,
        confirmButtonText,
        cancelButtonText,
        inputAttributes: {
            autocapitalize: 'sentences',
            maxlength: rest.inputAttributes?.maxlength ?? 120,
            ...rest.inputAttributes,
        },
        inputValidator: inputValidator ?? ((value) => {
            if (!String(value ?? '').trim()) {
                return requiredMessage;
            }

            return undefined;
        }),
        ...rest,
    }, { customClass });

    if (!result.isConfirmed) {
        return null;
    }

    return String(result.value ?? '').trim();
}

export function toast(title, icon = 'success', options = {}) {
    const { didOpen, customClass = {}, ...rest } = options;

    return Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3200,
        timerProgressBar: true,
        icon,
        title,
        customClass: {
            popup: 'ethos-swal-toast',
            title: 'ethos-swal-toast-title',
            ...customClass,
        },
        didOpen: (toastElement) => {
            toastElement.addEventListener('mouseenter', Swal.stopTimer);
            toastElement.addEventListener('mouseleave', Swal.resumeTimer);

            if (typeof didOpen === 'function') {
                didOpen(toastElement);
            }
        },
        ...rest,
    });
}

export function success(title, options = {}) {
    return toast(title, 'success', options);
}

export function error(title, options = {}) {
    return toast(title, 'error', { timer: 4200, ...options });
}

export function info(title, options = {}) {
    return toast(title, 'info', options);
}

export function warning(title, options = {}) {
    return toast(title, 'warning', { timer: 3800, ...options });
}

function bindConfirmableForms(api) {
    if (formsBound || typeof document === 'undefined') {
        return;
    }

    formsBound = true;

    document.addEventListener('submit', async (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement) || !form.matches('[data-swal-confirm]')) {
            return;
        }

        if (form.dataset.swalConfirmed === '1') {
            delete form.dataset.swalConfirmed;
            return;
        }

        event.preventDefault();

        const isConfirmed = await api.confirm({
            title: form.dataset.swalTitle || '¿Confirmar acción?',
            text: form.dataset.swalText || '',
            icon: form.dataset.swalIcon || 'warning',
            confirmButtonText: form.dataset.swalConfirmText || 'Confirmar',
            cancelButtonText: form.dataset.swalCancelText || 'Cancelar',
            danger: form.dataset.swalDanger === '1' || form.dataset.swalDanger === 'true',
        });

        if (!isConfirmed) {
            return;
        }

        form.dataset.swalConfirmed = '1';

        if (typeof form.requestSubmit === 'function') {
            form.requestSubmit(event.submitter);
            return;
        }

        HTMLFormElement.prototype.submit.call(form);
    });
}

export function initSweetAlerts() {
    if (window.EthosAlerts) {
        return window.EthosAlerts;
    }

    const api = {
        Swal,
        confirm,
        modal,
        promptText,
        toast,
        success,
        error,
        info,
        warning,
    };

    window.EthosAlerts = api;
    window.Swal = Swal;

    bindConfirmableForms(api);

    return api;
}

export default initSweetAlerts();
