export const themeStorageKey = 'maremiya-theme';

export function applyTheme(theme) {
    document.documentElement.classList.toggle('dark', theme === 'dark');
    localStorage.setItem(themeStorageKey, theme);
    updateThemeToggleLabels(theme);
}

export function updateThemeToggleLabels(theme) {
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        const isDark = theme === 'dark';
        button.setAttribute('aria-pressed', isDark ? 'true' : 'false');
        button.setAttribute(
            'aria-label',
            button.dataset[isDark ? 'labelLight' : 'labelDark'] ?? '',
        );

        button.querySelector('[data-theme-icon="light"]')?.classList.toggle('hidden', isDark);
        button.querySelector('[data-theme-icon="dark"]')?.classList.toggle('hidden', ! isDark);
    });

    document.querySelectorAll('[data-theme-option]').forEach((input) => {
        if (input instanceof HTMLInputElement) {
            input.checked = input.value === theme;
        }
    });
}

export function initTheme() {
    const savedTheme = localStorage.getItem(themeStorageKey) ?? 'light';
    applyTheme(savedTheme);
}

export function initThemeToggles() {
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const nextTheme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
            applyTheme(nextTheme);
        });
    });
}

export function getCurrentTheme() {
    return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
}
