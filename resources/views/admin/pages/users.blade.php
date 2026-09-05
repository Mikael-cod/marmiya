@php
    $listQuery = request()->only(['q', 'role', 'per_page', 'page']);
    $hasFilters = collect($filters)->filter(fn ($value, $key) => $key !== 'per_page' && filled($value))->isNotEmpty();
    $isEdit = filled($editingUser);
@endphp

<x-layouts.admin :title="$title">
    @if (session('success'))
        <div class="alert-success mb-6">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert-error mb-6">{{ session('error') }}</div>
    @endif

    <section class="card-surface shadow-auth-card">
        <div class="intake-list-header">
            <div class="min-w-0">
                <h1 class="intake-list-title">{{ __('app.admin.users.list_title') }}</h1>
                <p class="intake-list-subtitle">{{ $description }}</p>
            </div>

            <a
                href="{{ route('admin.users', $listQuery) }}"
                data-intake-modal-open
                class="btn-primary-brand intake-register-btn"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('app.admin.users.create') }}
            </a>
        </div>

        <form
            method="GET"
            action="{{ route('admin.users') }}"
            id="intake-search-form"
            class="intake-search-panel mt-3"
            data-intake-auto-search
        >
            <div class="intake-search-grid">
                <div class="intake-search-field intake-search-field-wide">
                    <label for="search-q" class="intake-search-label">{{ __('app.admin.users.search') }}</label>
                    <div class="intake-search-input-wrap">
                        <svg class="intake-search-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/>
                        </svg>
                        <input
                            id="search-q"
                            name="q"
                            type="search"
                            value="{{ $filters['q'] }}"
                            placeholder="{{ __('app.admin.users.search_placeholder') }}"
                            class="intake-search-control intake-search-input"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="intake-search-field">
                    <label for="search-role" class="intake-search-label">{{ __('app.admin.users.fields.role') }}</label>
                    <select id="search-role" name="role" class="intake-search-control">
                        <option value="">{{ __('app.admin.users.all_roles') }}</option>
                        @foreach (\App\Enums\UserRole::cases() as $roleOption)
                            <option value="{{ $roleOption->value }}" @selected($filters['role'] === $roleOption->value)>
                                {{ $roleOption->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="intake-search-field">
                    <label for="search-per-page" class="intake-search-label">{{ __('app.admin.users.per_page') }}</label>
                    <select id="search-per-page" name="per_page" class="intake-search-control">
                        @foreach ([10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected($filters['per_page'] === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="intake-search-clear-wrap" class="intake-search-field intake-search-clear-field @unless($hasFilters || filled($filters['q'])) hidden @endunless">
                    <span class="intake-search-label intake-search-label-hidden" aria-hidden="true">&nbsp;</span>
                    <button type="button" data-intake-search-clear class="intake-search-clear-btn">
                        {{ __('app.admin.users.clear_search') }}
                    </button>
                </div>
            </div>
        </form>

        <div id="intake-results" class="intake-results">
            @include('admin.pages.partials.users-results', [
                'users' => $users,
                'filters' => $filters,
            ])
        </div>
    </section>

    <div
        id="intake-modal"
        class="intake-modal"
        aria-hidden="true"
        @if ($errors->any() || $isEdit) data-open-on-load="true" @endif
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
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-teal/10 text-brand-teal">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>

                <div class="min-w-0 flex-1">
                    <h2 id="intake-modal-title" class="text-lg font-bold leading-relaxed text-brand-dark sm:text-xl">
                        {{ $isEdit ? __('app.admin.users.edit_title') : __('app.admin.users.create_title') }}
                    </h2>
                    <p class="mt-1 text-sm leading-relaxed text-brand-muted">
                        {{ $isEdit ? __('app.admin.users.edit_subtitle') : __('app.admin.users.create_subtitle') }}
                    </p>
                </div>

                <a
                    href="{{ route('admin.users', $listQuery) }}"
                    class="intake-modal-close btn-icon-brand shrink-0"
                    data-intake-modal-close
                    aria-label="{{ __('app.admin.users.close') }}"
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
                    @include('admin.pages.partials.users-form', [
                        'editingUser' => $editingUser,
                    ])
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
