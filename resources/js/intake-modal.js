import { refreshEthiopianCalendar } from './ethiopian-calendar.js';
import { refreshExpenseInmateSelect } from './expense-inmate-select.js';
import { refreshIntakeParoleDate } from './intake-parole-date.js';
import { refreshPrisonerInmateSelect } from './prisoner-inmate-select.js';
import { refreshPrisonerFormHelpers } from './prisoner-form.js';

function getIntakeModal() {
    return document.getElementById('intake-modal');
}

function openIntakeModal() {
    const modal = getIntakeModal();

    if (! modal) {
        return;
    }

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('intake-modal-open');
    document.body.style.overflow = 'hidden';

    refreshEthiopianCalendar();
    refreshIntakeParoleDate();
    refreshExpenseInmateSelect();
    refreshPrisonerInmateSelect();
    refreshPrisonerFormHelpers();
}

function closeIntakeModal() {
    const modal = getIntakeModal();

    if (! modal) {
        return;
    }

    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('intake-modal-open');
    document.body.style.overflow = '';
}

export function initIntakeModal() {
    const modal = getIntakeModal();

    if (! modal) {
        return;
    }

    document.addEventListener('click', (event) => {
        const openTrigger = event.target.closest('[data-intake-modal-open]');

        if (openTrigger) {
            event.preventDefault();
            openIntakeModal();
        }
    });

    document.querySelectorAll('[data-intake-modal-close]').forEach((button) => {
        button.addEventListener('click', closeIntakeModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeIntakeModal();
        }
    });

    if (modal.dataset.openOnLoad === 'true') {
        openIntakeModal();
    }
}

document.addEventListener('DOMContentLoaded', initIntakeModal);
