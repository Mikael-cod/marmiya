let inmateSelectInitialized = false;

function updateInmateMetaFields() {
    const select = document.querySelector('[data-inmate-select]');
    const courtInput = document.getElementById('inmate-court-file');
    const institutionInput = document.getElementById('inmate-institution-file');

    if (! select || ! courtInput || ! institutionInput) {
        return;
    }

    const sync = () => {
        const option = select.options[select.selectedIndex];

        courtInput.value = option?.dataset.courtFile ?? '';
        institutionInput.value = option?.dataset.institutionFile ?? '';
    };

    if (! inmateSelectInitialized) {
        select.addEventListener('change', sync);
        inmateSelectInitialized = true;
    }

    sync();
}

document.addEventListener('DOMContentLoaded', updateInmateMetaFields);

export function refreshPrisonerInmateSelect() {
    updateInmateMetaFields();
}
