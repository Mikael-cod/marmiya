@php
    $listQuery = request()->only(['q', 'gender', 'from', 'to', 'per_page', 'page']);
    $hasFilters = collect($filters)->filter(fn ($value, $key) => $key !== 'per_page' && filled($value))->isNotEmpty();
@endphp

<x-layouts.user :title="$title">
    @if (session('success'))
        <div class="alert-success mb-6">{{ session('success') }}</div>
    @endif

    <section class="card-surface shadow-auth-card">
        <div class="intake-list-header">
            <div class="min-w-0">
                <h1 class="intake-list-title">{{ __('app.prisoners.records_list') }}</h1>
                <p class="intake-list-subtitle">{{ $description }}</p>
            </div>

            <a
                href="{{ route('user.prisoners', $listQuery) }}"
                data-intake-modal-open
                class="btn-primary-brand intake-register-btn"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('app.prisoners.register') }}
            </a>
        </div>

        <form
            method="GET"
            action="{{ route('user.prisoners') }}"
            id="intake-search-form"
            class="intake-search-panel mt-3"
            data-intake-auto-search
        >
            <div class="intake-search-grid">
                <div class="intake-search-field intake-search-field-wide">
                    <label for="search-q" class="intake-search-label">{{ __('app.prisoners.search') }}</label>
                    <div class="intake-search-input-wrap">
                        <svg class="intake-search-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/>
                        </svg>
                        <input
                            id="search-q"
                            name="q"
                            type="search"
                            value="{{ $filters['q'] }}"
                            placeholder="{{ __('app.prisoners.search_placeholder') }}"
                            class="intake-search-control intake-search-input"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="intake-search-field">
                    <label for="search-gender" class="intake-search-label">{{ __('app.prisoners.fields.gender') }}</label>
                    <select id="search-gender" name="gender" class="intake-search-control">
                        <option value="">{{ __('app.prisoners.all_genders') }}</option>
                        <option value="male" @selected($filters['gender'] === 'male')>{{ __('app.prisoners.gender_male') }}</option>
                        <option value="female" @selected($filters['gender'] === 'female')>{{ __('app.prisoners.gender_female') }}</option>
                    </select>
                </div>

                <div class="intake-search-field">
                    <label for="search-from" class="intake-search-label">{{ __('app.prisoners.registered_from') }}</label>
                    <input id="search-from" name="from" type="date" value="{{ $filters['from'] }}" class="intake-search-control">
                </div>

                <div class="intake-search-field">
                    <label for="search-to" class="intake-search-label">{{ __('app.prisoners.registered_to') }}</label>
                    <input id="search-to" name="to" type="date" value="{{ $filters['to'] }}" class="intake-search-control">
                </div>

                <div class="intake-search-field">
                    <label for="search-per-page" class="intake-search-label">{{ __('app.prisoners.per_page') }}</label>
                    <select id="search-per-page" name="per_page" class="intake-search-control">
                        @foreach ([10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected($filters['per_page'] === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="intake-search-clear-wrap" class="intake-search-field intake-search-clear-field @unless($hasFilters || filled($filters['q'])) hidden @endunless">
                    <span class="intake-search-label intake-search-label-hidden" aria-hidden="true">&nbsp;</span>
                    <button type="button" data-intake-search-clear class="intake-search-clear-btn">
                        {{ __('app.prisoners.clear_search') }}
                    </button>
                </div>
            </div>
        </form>

        <div id="intake-results" class="intake-results">
            @include('user.pages.partials.prisoners-results', [
                'files' => $files,
                'filters' => $filters,
            ])
        </div>
    </section>

    <div
        id="intake-modal"
        class="intake-modal"
        aria-hidden="true"
        @if ($errors->any() || filled($editingFile)) data-open-on-load="true" @endif
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
                        {{ filled($editingFile) ? __('app.prisoners.edit_title') : __('app.prisoners.form_title') }}
                    </h2>
                    <p class="mt-1 text-sm leading-relaxed text-brand-muted">
                        {{ filled($editingFile) ? __('app.prisoners.edit_subtitle') : __('app.prisoners.form_subtitle', ['institute' => __('app.institute')]) }}
                    </p>
                </div>

                <a
                    href="{{ route('user.prisoners', $listQuery) }}"
                    class="intake-modal-close btn-icon-brand shrink-0"
                    data-intake-modal-close
                    aria-label="{{ __('app.prisoners.close') }}"
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
                    @include('user.pages.partials.prisoners-form', [
                        'editingFile' => $editingFile ?? null,
                        'availableInmates' => $availableInmates,
                    ])
                </div>
            </div>
        </div>
    </div>

    @if ($documentsFile)
        <div
            id="prisoner-documents-modal"
            class="intake-modal"
            aria-hidden="true"
            data-open-on-load="true"
        >
            <div class="intake-modal-backdrop" data-prisoner-documents-close aria-hidden="true"></div>

            <div
                class="intake-modal-panel intake-modal-panel-wide"
                role="dialog"
                aria-modal="true"
                aria-labelledby="prisoner-documents-modal-title"
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
                        <h2 id="prisoner-documents-modal-title" class="text-lg font-bold leading-relaxed text-brand-dark sm:text-xl">
                            {{ __('app.prisoners.documents_title') }}
                        </h2>
                        <p class="mt-1 text-sm leading-relaxed text-brand-muted">
                            {{ __('app.prisoners.documents_subtitle') }}
                        </p>
                    </div>

                    <a
                        href="{{ route('user.prisoners', $listQuery) }}"
                        class="intake-modal-close btn-icon-brand shrink-0"
                        data-prisoner-documents-close
                        aria-label="{{ __('app.prisoners.close') }}"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                </div>

                <div class="intake-modal-body">
                    @if ($errors->has('pages') || $errors->has('pages.*'))
                        <div class="alert-error mb-6">
                            <ul class="list-disc space-y-1 ps-5">
                                @foreach ($errors->get('pages') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                                @foreach ($errors->get('pages.*') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @include('user.pages.partials.prisoners-documents', ['file' => $documentsFile])
                </div>
            </div>
        </div>
    @endif

    @if ($viewingFile)
        <div
            id="prisoner-view-modal"
            class="intake-modal"
            aria-hidden="true"
            data-open-on-load="true"
        >
            <div class="intake-modal-backdrop" data-prisoner-view-close aria-hidden="true"></div>

            <div
                class="intake-modal-panel"
                role="dialog"
                aria-modal="true"
                aria-labelledby="prisoner-view-modal-title"
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
                        <h2 id="prisoner-view-modal-title" class="text-lg font-bold leading-relaxed text-brand-dark sm:text-xl">
                            {{ __('app.prisoners.view_title') }}
                        </h2>
                        <p class="mt-1 text-sm leading-relaxed text-brand-muted">
                            {{ __('app.prisoners.view_subtitle') }}
                        </p>
                    </div>

                    <a
                        href="{{ route('user.prisoners', $listQuery) }}"
                        class="intake-modal-close btn-icon-brand shrink-0"
                        data-prisoner-view-close
                        aria-label="{{ __('app.prisoners.close') }}"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                </div>

                <div class="intake-modal-body">
                    @include('user.pages.partials.prisoners-view', ['file' => $viewingFile])
                </div>
            </div>
        </div>
    @endif
</x-layouts.user>
