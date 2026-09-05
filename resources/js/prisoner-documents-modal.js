function getPrisonerDocumentsModal() {
    return document.getElementById('prisoner-documents-modal');
}

function openPrisonerDocumentsModal() {
    const modal = getPrisonerDocumentsModal();

    if (! modal) {
        return;
    }

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('intake-modal-open');
    document.body.style.overflow = 'hidden';
}

function closePrisonerDocumentsModal() {
    const modal = getPrisonerDocumentsModal();

    if (! modal) {
        return;
    }

    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');

    if (! document.getElementById('intake-modal')?.classList.contains('is-open')
        && ! document.getElementById('prisoner-view-modal')?.classList.contains('is-open')) {
        document.body.classList.remove('intake-modal-open');
        document.body.style.overflow = '';
    }
}

function initDocumentFilePreview() {
    const modal = getPrisonerDocumentsModal();

    if (! modal) {
        return;
    }

    modal.querySelectorAll('[data-prisoner-documents-input]').forEach((input) => {
        const selectedLabel = input.closest('label')?.querySelector('[data-prisoner-documents-selected]');

        if (! selectedLabel) {
            return;
        }

        input.addEventListener('change', () => {
            const files = Array.from(input.files ?? []);

            if (files.length === 0) {
                selectedLabel.textContent = '';
                selectedLabel.classList.add('hidden');
                return;
            }

            selectedLabel.textContent = files.map((file) => file.name).join(' · ');
            selectedLabel.classList.remove('hidden');
        });
    });
}

export function initPrisonerDocumentsModal() {
    const modal = getPrisonerDocumentsModal();

    if (! modal) {
        return;
    }

    document.querySelectorAll('[data-prisoner-documents-close]').forEach((button) => {
        button.addEventListener('click', closePrisonerDocumentsModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closePrisonerDocumentsModal();
        }
    });

    initDocumentFilePreview();

    if (modal.dataset.openOnLoad === 'true') {
        openPrisonerDocumentsModal();
    }
}

document.addEventListener('DOMContentLoaded', initPrisonerDocumentsModal);
