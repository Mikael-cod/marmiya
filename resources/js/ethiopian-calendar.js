import '@bekim_2121/ethiopian-datepicker/dist/ethiopian-datepicker.css';
import ethiopianPicker from '@bekim_2121/ethiopian-datepicker/dist/ethiopian-datepicker.js';

const { EthiopianCalendar, EthiopianDatePicker, EthiopianTimePicker } = ethiopianPicker;

const calendar = new EthiopianCalendar();

function isDarkMode() {
    return document.documentElement.classList.contains('dark');
}

function toGregorianIso(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

function toGregorianTime(hours, minutes) {
    return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
}

function syncPickerDarkMode() {
    document.querySelectorAll('.ethio-datepicker, .ethio-timepicker').forEach((element) => {
        element.classList.toggle('dark-mode', isDarkMode());
    });
}

function syncDateInputFromHidden(hiddenInput, displayInput) {
    if (! hiddenInput.value) {
        displayInput.value = '';
        displayInput.pickerInstance?.selectedDate && (displayInput.pickerInstance.selectedDate = null);

        return;
    }

    const gregorianDate = new Date(`${hiddenInput.value}T12:00:00`);
    const ethiopian = calendar.gregorianToEthiopian(gregorianDate);
    displayInput.pickerInstance?.setDate(ethiopian.year, ethiopian.month, ethiopian.day);
    hiddenInput.value = toGregorianIso(gregorianDate);
}

function syncTimeInputFromHidden(hiddenInput, displayInput) {
    if (! hiddenInput.value) {
        displayInput.value = '';

        return;
    }

    const [hours, minutes] = hiddenInput.value.split(':').map(Number);
    const ethHour = (hours - 6 + 24) % 12 || 12;
    const period = (hours >= 6 && hours < 18) ? 'ቀን' : 'ማታ';

    displayInput.value = `${String(ethHour).padStart(2, '0')}:${String(minutes).padStart(2, '0')} ${period}`;

    if (displayInput.pickerInstance) {
        displayInput.pickerInstance.hours = ethHour;
        displayInput.pickerInstance.minutes = minutes;
        displayInput.pickerInstance.setPeriod(period);
    }
}

function initEthiopianDateInputs() {
    document.querySelectorAll('[data-eth-date-input]').forEach((hiddenInput) => {
        const displayInput = document.querySelector(
            `[data-eth-date-display="${hiddenInput.id}"]`,
        );

        if (! displayInput || displayInput.dataset.ethPickerReady === 'true') {
            return;
        }

        if (displayInput.hasAttribute('data-eth-date-auto-calculated')) {
            displayInput.dataset.ethPickerReady = 'true';

            return;
        }

        displayInput.dataset.ethPickerReady = 'true';

        const picker = new EthiopianDatePicker(displayInput, {
            locale: 'am',
            highlightHolidays: true,
            darkMode: isDarkMode(),
            onChange: ({ gregorian }) => {
                hiddenInput.value = toGregorianIso(gregorian);
                hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
            },
        });

        displayInput.pickerInstance = picker;
        syncDateInputFromHidden(hiddenInput, displayInput);
    });
}

function initEthiopianTimeInputs() {
    document.querySelectorAll('[data-eth-time-input]').forEach((hiddenInput) => {
        const displayInput = document.querySelector(
            `[data-eth-time-display="${hiddenInput.id}"]`,
        );

        if (! displayInput || displayInput.dataset.ethPickerReady === 'true') {
            return;
        }

        displayInput.dataset.ethPickerReady = 'true';

        const picker = new EthiopianTimePicker(displayInput, {
            locale: 'am',
            useEthiopianTime: true,
            twelveHour: true,
            darkMode: isDarkMode(),
            onChange: ({ gregorian }) => {
                hiddenInput.value = toGregorianTime(gregorian.hours, gregorian.minutes);
                hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
            },
        });

        displayInput.pickerInstance = picker;
        syncTimeInputFromHidden(hiddenInput, displayInput);
    });
}

function initEthiopianClock() {
    const clockElements = document.querySelectorAll('[data-eth-clock]');

    if (clockElements.length === 0) {
        return;
    }

    const updateClock = () => {
        const now = new Date();
        const ethiopian = calendar.gregorianToEthiopian(now);
        const formattedDate = calendar.formatDate(
            ethiopian.year,
            ethiopian.month,
            ethiopian.day,
            'am',
            true,
        );

        const gregHour = now.getHours();
        const gregMinute = now.getMinutes();
        const ethHour = (gregHour - 6 + 24) % 12 || 12;
        const period = (gregHour >= 6 && gregHour < 18) ? 'ቀን' : 'ማታ';
        const formattedTime = `${period} ${ethHour}:${String(gregMinute).padStart(2, '0')}`;

        clockElements.forEach((element) => {
            element.textContent = `${formattedDate} · ${formattedTime}`;
        });
    };

    updateClock();
    window.setInterval(updateClock, 30_000);
}

function initEthiopianFormResets() {
    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('reset', () => {
            window.setTimeout(() => {
                document.querySelectorAll('[data-eth-date-input]').forEach((hiddenInput) => {
                    const displayInput = document.querySelector(
                        `[data-eth-date-display="${hiddenInput.id}"]`,
                    );

                    if (displayInput) {
                        syncDateInputFromHidden(hiddenInput, displayInput);
                    }
                });

                document.querySelectorAll('[data-eth-time-input]').forEach((hiddenInput) => {
                    const displayInput = document.querySelector(
                        `[data-eth-time-display="${hiddenInput.id}"]`,
                    );

                    if (displayInput) {
                        syncTimeInputFromHidden(hiddenInput, displayInput);
                    }
                });
            }, 0);
        });
    });
}

function initEthiopianCalendar() {
    initEthiopianDateInputs();
    initEthiopianTimeInputs();
    initEthiopianClock();
    initEthiopianFormResets();
    syncPickerDarkMode();
}

export function refreshEthiopianCalendar() {
    initEthiopianCalendar();
}

export function setEthDateField(fieldId, isoValue) {
    const hiddenInput = document.getElementById(fieldId);
    const displayInput = document.querySelector(
        `[data-eth-date-display="${fieldId}"]`,
    );

    if (! hiddenInput || ! displayInput) {
        return;
    }

    hiddenInput.value = isoValue ?? '';

    if (! isoValue) {
        displayInput.value = '';

        return;
    }

    const gregorianDate = new Date(`${isoValue}T12:00:00`);
    const ethiopian = calendar.gregorianToEthiopian(gregorianDate);

    displayInput.value = calendar.formatDate(
        ethiopian.year,
        ethiopian.month,
        ethiopian.day,
        'am',
        false,
    );
}

export function watchEthiopianTheme() {
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            window.setTimeout(syncPickerDarkMode, 0);
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initEthiopianCalendar();
    watchEthiopianTheme();
});
