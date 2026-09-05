import { applyTheme, getCurrentTheme } from './theme.js';

const sidebarStorageKey = 'maremiya-sidebar-collapsed';

function applySidebarCollapsed(collapsed) {
    if (window.matchMedia('(min-width: 1024px)').matches) {
        document.body.classList.toggle('sidebar-collapsed', collapsed);
    }

    localStorage.setItem(sidebarStorageKey, collapsed ? '1' : '0');
    document.querySelectorAll('[data-sidebar-collapsed-option]').forEach((input) => {
        if (input instanceof HTMLInputElement) {
            input.checked = input.value === (collapsed ? '1' : '0');
        }
    });
}

function initThemeSettings() {
    document.querySelectorAll('[data-theme-option]').forEach((input) => {
        input.addEventListener('change', () => {
            if (input instanceof HTMLInputElement && input.checked) {
                applyTheme(input.value);
            }
        });
    });
}

function initSidebarSettings() {
    const saved = localStorage.getItem(sidebarStorageKey) === '1';

    document.querySelectorAll('[data-sidebar-collapsed-option]').forEach((input) => {
        if (input instanceof HTMLInputElement) {
            input.checked = input.value === (saved ? '1' : '0');
            input.addEventListener('change', () => {
                if (input.checked) {
                    applySidebarCollapsed(input.value === '1');
                }
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    if (! document.querySelector('[data-settings-page]')) {
        return;
    }

    initThemeSettings();
    initSidebarSettings();

    document.querySelectorAll('[data-theme-option]').forEach((input) => {
        if (input instanceof HTMLInputElement) {
            input.checked = input.value === getCurrentTheme();
        }
    });
});
