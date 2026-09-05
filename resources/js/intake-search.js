export function initIntakeAutoSearch() {
    const form = document.querySelector('[data-intake-auto-search]');
    const resultsContainer = document.getElementById('intake-results');
    const clearWrap = document.getElementById('intake-search-clear-wrap');

    if (! form || ! resultsContainer) {
        return;
    }

    let debounceTimer = null;
    let activeController = null;

    const searchInput = form.querySelector('input[name="q"]');

    const hasActiveFilters = () => ['q', 'status', 'gender', 'from', 'to'].some((name) => {
        const field = form.elements[name];

        return field && String(field.value).trim() !== '';
    });

    const updateClearButton = () => {
        if (! clearWrap) {
            return;
        }

        clearWrap.classList.toggle('hidden', ! hasActiveFilters());
    };

    const buildSearchUrl = (page = null) => {
        const params = new URLSearchParams(new FormData(form));

        if (page) {
            params.set('page', page);
        } else {
            params.delete('page');
        }

        params.forEach((value, key) => {
            if (value === '') {
                params.delete(key);
            }
        });

        const query = params.toString();

        return `${form.action}${query ? `?${query}` : ''}`;
    };

    const runSearch = async (page = null) => {
        const url = buildSearchUrl(page);
        const cursorStart = searchInput?.selectionStart ?? null;
        const cursorEnd = searchInput?.selectionEnd ?? null;

        if (activeController) {
            activeController.abort();
        }

        activeController = new AbortController();
        resultsContainer.classList.add('is-loading');

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Intake-Search': '1',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: activeController.signal,
            });

            if (! response.ok) {
                throw new Error('Search request failed');
            }

            const html = await response.text();
            resultsContainer.innerHTML = html;

            window.history.replaceState({}, '', url);
            updateClearButton();

            if (searchInput) {
                searchInput.focus();

                if (cursorStart !== null && cursorEnd !== null) {
                    searchInput.setSelectionRange(cursorStart, cursorEnd);
                }
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                form.submit();
            }
        } finally {
            resultsContainer.classList.remove('is-loading');
            activeController = null;
        }
    };

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        runSearch();
    });

    searchInput?.addEventListener('input', () => {
        updateClearButton();
        window.clearTimeout(debounceTimer);
        debounceTimer = window.setTimeout(() => runSearch(), 400);
    });

    searchInput?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            window.clearTimeout(debounceTimer);
            runSearch();
        }
    });

    form.querySelectorAll('select, input[type="date"]').forEach((input) => {
        input.addEventListener('change', () => {
            updateClearButton();
            runSearch();
        });
    });

    clearWrap?.querySelector('[data-intake-search-clear]')?.addEventListener('click', (event) => {
        event.preventDefault();

        ['q', 'status', 'gender', 'from', 'to'].forEach((name) => {
            const field = form.elements[name];

            if (field) {
                field.value = '';
            }
        });

        updateClearButton();
        runSearch();
    });

    resultsContainer.addEventListener('click', (event) => {
        const link = event.target.closest('[data-intake-pagination] a, .pagination a');

        if (! link || ! resultsContainer.contains(link)) {
            return;
        }

        event.preventDefault();

        const pageUrl = new URL(link.href, window.location.origin);
        const page = pageUrl.searchParams.get('page');

        runSearch(page);
    });

    updateClearButton();
}

document.addEventListener('DOMContentLoaded', initIntakeAutoSearch);
