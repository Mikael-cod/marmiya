import { setEthDateField } from './ethiopian-calendar.js';

const formSelector = '#intake-registration-form';
const startFieldId = 'sentence_start_date';
const endFieldId = 'sentence_end_date';
const durationFieldId = 'sentence_duration';
const paroleFieldId = 'parole_release_date';

let activeRequest = null;
let debounceTimer = null;

function getFormValues() {
    return {
        start: document.getElementById(startFieldId)?.value ?? '',
        end: document.getElementById(endFieldId)?.value ?? '',
        sentenceDuration: document.getElementById(durationFieldId)?.value?.trim() ?? '',
    };
}

async function fetchParoleReleaseDate({ start, end, sentenceDuration }, signal) {
    const params = new URLSearchParams({ start, end });

    if (sentenceDuration) {
        params.set('sentence_duration', sentenceDuration);
    }

    const response = await fetch(`/income/parole-release-date?${params.toString()}`, {
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        signal,
    });

    if (! response.ok) {
        return null;
    }

    return response.json();
}

async function syncParoleReleaseDate() {
    const form = document.querySelector(formSelector);

    if (! form) {
        return;
    }

    const { start, end, sentenceDuration } = getFormValues();

    if (! start || ! end) {
        setEthDateField(paroleFieldId, '');

        return;
    }

    if (activeRequest) {
        activeRequest.abort();
    }

    activeRequest = new AbortController();

    try {
        const payload = await fetchParoleReleaseDate(
            { start, end, sentenceDuration },
            activeRequest.signal,
        );

        setEthDateField(paroleFieldId, payload?.parole_release_date ?? '');
    } catch (error) {
        if (error.name !== 'AbortError') {
            setEthDateField(paroleFieldId, '');
        }
    } finally {
        activeRequest = null;
    }
}

function scheduleParoleSync() {
    window.clearTimeout(debounceTimer);
    debounceTimer = window.setTimeout(() => {
        syncParoleReleaseDate();
    }, 200);
}

function bindParoleReleaseAutoFill(form) {
    [startFieldId, endFieldId].forEach((fieldId) => {
        document.getElementById(fieldId)?.addEventListener('change', scheduleParoleSync);
    });

    document.getElementById(durationFieldId)?.addEventListener('input', scheduleParoleSync);
    document.getElementById(durationFieldId)?.addEventListener('change', scheduleParoleSync);

    form.addEventListener('reset', () => {
        window.setTimeout(() => {
            setEthDateField(paroleFieldId, '');
        }, 0);
    });
}

export function refreshIntakeParoleDate() {
    const form = document.querySelector(formSelector);

    if (! form) {
        return;
    }

    if (form.dataset.paroleAutoReady !== 'true') {
        form.dataset.paroleAutoReady = 'true';
        bindParoleReleaseAutoFill(form);
    }

    syncParoleReleaseDate();
}

document.addEventListener('DOMContentLoaded', () => {
    refreshIntakeParoleDate();
});
