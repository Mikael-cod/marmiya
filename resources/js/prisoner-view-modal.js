function getPrisonerViewModal() {
    return document.getElementById('prisoner-view-modal');
}

function openPrisonerViewModal() {
    const modal = getPrisonerViewModal();

    if (! modal) {
        return;
    }

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('intake-modal-open');
    document.body.style.overflow = 'hidden';
}

function closePrisonerViewModal() {
    const modal = getPrisonerViewModal();

    if (! modal) {
        return;
    }

    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');

    if (! document.getElementById('intake-modal')?.classList.contains('is-open')) {
        document.body.classList.remove('intake-modal-open');
        document.body.style.overflow = '';
    }
}

export function initPrisonerViewModal() {
    const modal = getPrisonerViewModal();

    if (! modal) {
        return;
    }

    document.querySelectorAll('[data-prisoner-view-close]').forEach((button) => {
        button.addEventListener('click', closePrisonerViewModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closePrisonerViewModal();
        }
    });

    if (modal.dataset.openOnLoad === 'true') {
        openPrisonerViewModal();
    }
}

document.addEventListener('DOMContentLoaded', initPrisonerViewModal);
