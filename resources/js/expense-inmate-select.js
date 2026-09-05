let expenseInmateSelectInitialized = false;

const expenseFieldMap = {
    full_name: 'expense-full-name',
    gender: 'expense-gender',
    age: 'expense-age',
    religion: 'expense-religion',
    nationality: 'expense-nationality',
    country_of_birth: 'expense-country-of-birth',
    admission_date: 'expense-admission-date',
    sentencing_court: 'expense-sentencing-court',
    sentence_duration: 'expense-sentence-duration',
    court_file_number: 'expense-court-file-number',
    crime_type: 'expense-crime-type',
    institution_id_number: 'expense-institution-id-number',
    education_skill_before: 'expense-education-skill-before',
};

function setExpenseField(id, value) {
    const input = document.getElementById(id);

    if (input) {
        input.value = value ?? '';
    }
}

function clearExpenseCopiedFields() {
    Object.values(expenseFieldMap).forEach((id) => setExpenseField(id, ''));

    const photoWrap = document.getElementById('expense-photo-wrap');
    const photoPreview = document.getElementById('expense-photo-preview');

    if (photoWrap) {
        photoWrap.hidden = true;
    }

    if (photoPreview) {
        photoPreview.removeAttribute('src');
    }
}

function fillExpenseCopiedFields(data) {
    Object.entries(expenseFieldMap).forEach(([key, id]) => {
        setExpenseField(id, data[key] ?? '');
    });

    const releaseReason = document.getElementById('release_reason');
    const releaseDate = document.querySelector('[name="release_date"]');

    if (releaseReason && ! releaseReason.value && data.release_reason) {
        releaseReason.value = data.release_reason;
    }

    if (releaseDate && ! releaseDate.value && data.release_date) {
        releaseDate.value = data.release_date;
        releaseDate.dispatchEvent(new Event('change', { bubbles: true }));
    }

    const photoWrap = document.getElementById('expense-photo-wrap');
    const photoPreview = document.getElementById('expense-photo-preview');

    if (photoWrap && photoPreview) {
        if (data.photo_url) {
            photoPreview.src = data.photo_url;
            photoWrap.hidden = false;
        } else {
            photoPreview.removeAttribute('src');
            photoWrap.hidden = true;
        }
    }
}

async function loadExpenseInmateData(inmateId) {
    const form = document.querySelector('[data-expense-form]');

    if (! form || ! inmateId) {
        clearExpenseCopiedFields();
        return;
    }

    const baseUrl = form.dataset.inmateDataUrl;

    try {
        const response = await fetch(`${baseUrl}/${inmateId}`, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (! response.ok) {
            clearExpenseCopiedFields();
            return;
        }

        const data = await response.json();
        fillExpenseCopiedFields(data);
    } catch {
        clearExpenseCopiedFields();
    }
}

function initExpenseInmateSelect() {
    const select = document.querySelector('[data-expense-inmate-select]');

    if (! select) {
        return;
    }

    const sync = () => {
        loadExpenseInmateData(select.value);
    };

    if (! expenseInmateSelectInitialized) {
        select.addEventListener('change', sync);
        expenseInmateSelectInitialized = true;
    }

    if (select.value) {
        sync();
    }
}

document.addEventListener('DOMContentLoaded', initExpenseInmateSelect);

export function refreshExpenseInmateSelect() {
    initExpenseInmateSelect();
}
