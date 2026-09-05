import './ethiopian-calendar.js';
import './intake-modal.js';
import './intake-search.js';
import './intake-photo.js';
import './intake-parole-date.js';
import './expense-inmate-select.js';
import './prisoner-inmate-select.js';
import './prisoner-form.js';
import './prisoner-view-modal.js';
import './prisoner-documents-modal.js';
import './settings-page.js';
import { applyTheme, initTheme, initThemeToggles } from './theme.js';

function updateSidebarToggleState() {
    const toggleButton = document.querySelector('[data-sidebar-toggle]');

    if (! toggleButton) {
        return;
    }

    const isOpen = isDesktopSidebar()
        ? ! document.body.classList.contains('sidebar-collapsed')
        : document.body.classList.contains('sidebar-open');

    toggleButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
}

function isDesktopSidebar() {
    return window.matchMedia('(min-width: 1024px)').matches;
}

function closeSidebar() {
    document.body.classList.remove('sidebar-open');
    document.body.style.overflow = '';
    updateSidebarToggleState();
}

function openSidebar() {
    if (isDesktopSidebar()) {
        document.body.classList.remove('sidebar-collapsed');
        updateSidebarToggleState();
        return;
    }

    document.body.classList.add('sidebar-open');
    document.body.style.overflow = 'hidden';
    updateSidebarToggleState();
}

function toggleSidebar() {
    if (isDesktopSidebar()) {
        document.body.classList.toggle('sidebar-collapsed');
        document.body.classList.remove('sidebar-open');
        document.body.style.overflow = '';
        updateSidebarToggleState();
        return;
    }

    if (document.body.classList.contains('sidebar-open')) {
        closeSidebar();
    } else {
        openSidebar();
    }
}

function initSidebarNavGroups() {
    document.querySelectorAll('[data-nav-group]').forEach((group) => {
        const toggle = group.querySelector('[data-nav-group-toggle]');
        const items = group.querySelector('.nav-group-items');

        if (! toggle || ! items) {
            return;
        }

        if (group.hasAttribute('data-nav-group-open')) {
            group.classList.add('is-open');
        }

        toggle.addEventListener('click', () => {
            const isOpen = ! items.hasAttribute('hidden');

            if (isOpen) {
                items.setAttribute('hidden', '');
                group.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            } else {
                items.removeAttribute('hidden');
                group.classList.add('is-open');
                toggle.setAttribute('aria-expanded', 'true');
            }
        });
    });
}

function initDashboardSidebar() {
    if (localStorage.getItem('maremiya-sidebar-collapsed') === '1' && isDesktopSidebar()) {
        document.body.classList.add('sidebar-collapsed');
    }

    const toggleButton = document.querySelector('[data-sidebar-toggle]');
    const closeButton = document.querySelector('[data-sidebar-close]');
    const backdrop = document.querySelector('#sidebar-backdrop');
    const sidebarLinks = document.querySelectorAll('#dashboard-sidebar a');

    toggleButton?.addEventListener('click', () => {
        toggleSidebar();
        updateSidebarToggleState();
    });
    closeButton?.addEventListener('click', closeSidebar);
    backdrop?.addEventListener('click', closeSidebar);

    sidebarLinks.forEach((link) => {
        link.addEventListener('click', () => {
            if (! isDesktopSidebar()) {
                closeSidebar();
            }
        });
    });

    window.addEventListener('resize', () => {
        if (isDesktopSidebar()) {
            document.body.classList.remove('sidebar-open');
            document.body.style.overflow = '';
        } else {
            document.body.classList.remove('sidebar-collapsed');
        }

        updateSidebarToggleState();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (isDesktopSidebar()) {
                document.body.classList.add('sidebar-collapsed');
            } else {
                closeSidebar();
            }
        }
    });
}

initTheme();

document.addEventListener('DOMContentLoaded', () => {
    initThemeToggles();
    initSidebarNavGroups();
    initDashboardSidebar();
    updateSidebarToggleState();
});
