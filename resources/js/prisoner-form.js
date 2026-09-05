function calculateAgeFromBirthDate(birthDateIso) {
    if (! birthDateIso) {
        return '';
    }

    const birth = new Date(`${birthDateIso}T12:00:00`);
    const today = new Date(`${todayIso()}T12:00:00`);

    if (Number.isNaN(birth.getTime())) {
        return '';
    }

    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age -= 1;
    }

    return Math.max(0, age);
}

function todayIso() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

function syncAgeFromBirthDate() {
    const birthInput = document.getElementById('birth_date');
    const ageInput = document.getElementById('age');

    if (! birthInput || ! ageInput) {
        return;
    }

    const updateAge = () => {
        ageInput.value = calculateAgeFromBirthDate(birthInput.value);
    };

    if (! birthInput.dataset.ageSyncBound) {
        birthInput.addEventListener('change', updateAge);
        birthInput.dataset.ageSyncBound = 'true';
    }

    updateAge();
}

document.addEventListener('DOMContentLoaded', syncAgeFromBirthDate);

export function refreshPrisonerFormHelpers() {
    syncAgeFromBirthDate();
}
