@php
    $listQuery = request()->only(['q', 'status', 'from', 'to', 'per_page', 'page']);
    $hasFilters = collect($filters)->filter(fn ($value, $key) => $key !== 'per_page' && filled($value))->isNotEmpty();
@endphp

<x-layouts.user :title="$title">
    @if (session('success'))
        <div class="alert-success mb-6">{{ session('success') }}</div>
    @endif

    <section class="card-surface shadow-auth-card">
        <div class="intake-list-header">
            <div class="min-w-0">
                <h1 class="intake-list-title">{{ __('app.income.records_list') }}</h1>
                <p class="intake-list-subtitle">{{ $description }}</p>
            </div>

            <a
                href="{{ route('user.income', $listQuery) }}"
                data-intake-modal-open
                class="btn-primary-brand intake-register-btn"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('app.income.register') }}
            </a>
        </div>

        {{-- Search & filters --}}
        <form
            method="GET"
            action="{{ route('user.income') }}"
            id="intake-search-form"
            class="intake-search-panel mt-3"
            data-intake-auto-search
        >
            <div class="intake-search-grid">
                <div class="intake-search-field intake-search-field-wide">
                    <label for="search-q" class="intake-search-label">{{ __('app.income.search') }}</label>
                    <div class="intake-search-input-wrap">
                        <svg class="intake-search-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/>
                        </svg>
                        <input
                            id="search-q"
                            name="q"
                            type="search"
                            value="{{ $filters['q'] }}"
                            placeholder="{{ __('app.income.search_placeholder') }}"
                            class="intake-search-control intake-search-input"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="intake-search-field">
                    <label for="search-status" class="intake-search-label">{{ __('app.income.fields.sentence_status') }}</label>
                    <select id="search-status" name="status" class="intake-search-control">
                        <option value="">{{ __('app.income.all_statuses') }}</option>
                        <option value="remand" @selected($filters['status'] === 'remand')>{{ __('app.income.status_remand') }}</option>
                        <option value="convicted" @selected($filters['status'] === 'convicted')>{{ __('app.income.status_convicted') }}</option>
                    </select>
                </div>

                <div class="intake-search-field">
                    <label for="search-from" class="intake-search-label">{{ __('app.income.admission_from') }}</label>
                    <input id="search-from" name="from" type="date" value="{{ $filters['from'] }}" class="intake-search-control">
                </div>

                <div class="intake-search-field">
                    <label for="search-to" class="intake-search-label">{{ __('app.income.admission_to') }}</label>
                    <input id="search-to" name="to" type="date" value="{{ $filters['to'] }}" class="intake-search-control">
                </div>

                <div class="intake-search-field">
                    <label for="search-per-page" class="intake-search-label">{{ __('app.income.per_page') }}</label>
                    <select id="search-per-page" name="per_page" class="intake-search-control">
                        @foreach ([10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected($filters['per_page'] === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="intake-search-clear-wrap" class="intake-search-field intake-search-clear-field @unless($hasFilters || filled($filters['q'])) hidden @endunless">
                    <span class="intake-search-label intake-search-label-hidden" aria-hidden="true">&nbsp;</span>
                    <button type="button" data-intake-search-clear class="intake-search-clear-btn">
                        {{ __('app.income.clear_search') }}
                    </button>
                </div>
            </div>
        </form>

        <div id="intake-results" class="intake-results">
            @include('user.pages.partials.income-results', [
                'registrations' => $registrations,
                'filters' => $filters,
            ])
        </div>
    </section>

    {{-- Registration modal --}}
    <div
        id="intake-modal"
        class="intake-modal"
        aria-hidden="true"
        @if ($errors->any() || filled($editingRegistration)) data-open-on-load="true" @endif
    >
        <div class="intake-modal-backdrop" data-intake-modal-close aria-hidden="true"></div>

        <div
            class="intake-modal-panel"
            role="dialog"
            aria-modal="true"
            aria-labelledby="intake-modal-title"
        >
            <div class="intake-modal-header">
                <div class="intake-form-emblem">
                    <img
                        src="{{ asset('images/intake-logo.jpeg') }}"
                        alt="{{ __('app.institute') }}"
                        class="intake-form-logo"
                    >
                </div>

                <div class="min-w-0 flex-1">
                    <h2 id="intake-modal-title" class="text-lg font-bold leading-relaxed text-brand-dark sm:text-xl">
                        {{ filled($editingRegistration) ? __('app.income.edit_title') : __('app.income.form_title') }}
                    </h2>
                    <p class="mt-1 text-sm leading-relaxed text-brand-muted">
                        {{ filled($editingRegistration) ? __('app.income.edit_subtitle') : __('app.income.form_subtitle', ['institute' => __('app.institute')]) }}
                    </p>
                </div>

                <a
                    href="{{ route('user.income', $listQuery) }}"
                    class="intake-modal-close btn-icon-brand shrink-0"
                    data-intake-modal-close
                    aria-label="{{ __('app.income.close') }}"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
            </div>

            <div class="intake-modal-body">
                @if ($errors->any())
                    <div class="alert-error mb-6">
                        <ul class="list-disc space-y-1 ps-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="intake-form-shell intake-form-shell-modal">
                    @include('user.pages.partials.income-form', ['editingRegistration' => $editingRegistration ?? null])
                </div>
            </div>
        </div>
    </div>
</x-layouts.user>
