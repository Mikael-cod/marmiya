@php
    $hasFilters = collect($filters)->filter(fn ($value, $key) => $key !== 'per_page' && filled($value))->isNotEmpty();
@endphp

<x-layouts.admin :title="$title">
    <section class="overflow-hidden rounded-3xl border border-brand-border bg-brand-surface shadow-auth-card">
        <div class="relative px-6 py-8 sm:px-8 sm:py-10">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-brand-blue/5 via-transparent to-brand-teal/5"></div>
            <div class="relative">
                <h1 class="text-2xl font-bold text-brand-dark sm:text-3xl">{{ $title }}</h1>
                <p class="mt-3 max-w-3xl text-sm leading-relaxed text-brand-muted sm:text-base">{{ $description }}</p>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-5">
        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.activity.info_logins_today') }}</p>
            <p class="mt-2 text-lg font-bold text-brand-dark">{{ $overview['logins_today'] ?? 0 }}</p>
        </div>

        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.activity.info_logouts_today') }}</p>
            <p class="mt-2 text-lg font-bold text-brand-dark">{{ $overview['logouts_today'] ?? 0 }}</p>
        </div>

        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.activity.info_failed_24h') }}</p>
            <p class="mt-2 text-lg font-bold {{ ($overview['failed_logins_24h'] ?? 0) > 0 ? 'text-amber-600' : 'text-brand-dark' }}">
                {{ $overview['failed_logins_24h'] ?? 0 }}
            </p>
        </div>

        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.activity.info_admin_actions_today') }}</p>
            <p class="mt-2 text-lg font-bold text-brand-dark">{{ $overview['admin_actions_today'] ?? 0 }}</p>
        </div>

        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.activity.info_active_sessions') }}</p>
            <p class="mt-2 text-lg font-bold text-brand-dark">{{ $overview['active_sessions'] ?? 0 }}</p>
        </div>
    </section>

    <section class="card-surface mt-6 shadow-auth-card">
        <div class="border-b border-brand-border px-6 py-4">
            <h2 class="text-lg font-bold text-brand-dark">{{ __('app.admin.activity.list_title') }}</h2>
            <p class="mt-1 text-sm text-brand-muted">{{ __('app.admin.activity.list_subtitle') }}</p>
        </div>

        <form method="GET" action="{{ route('admin.activity') }}" class="intake-search-panel mt-3 px-6">
            <div class="intake-search-grid">
                <div class="intake-search-field intake-search-field-wide">
                    <label for="activity-q" class="intake-search-label">{{ __('app.admin.activity.search') }}</label>
                    <div class="intake-search-input-wrap">
                        <svg class="intake-search-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/>
                        </svg>
                        <input
                            id="activity-q"
                            name="q"
                            type="search"
                            value="{{ $filters['q'] }}"
                            placeholder="{{ __('app.admin.activity.search_placeholder') }}"
                            class="intake-search-control intake-search-input"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="intake-search-field">
                    <label for="activity-category" class="intake-search-label">{{ __('app.admin.activity.fields.category') }}</label>
                    <select id="activity-category" name="category" class="intake-search-control">
                        <option value="">{{ __('app.admin.activity.all_categories') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" @selected($filters['category'] === $category)>
                                {{ __("app.admin.activity.categories.{$category}") }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="intake-search-field">
                    <label for="activity-event" class="intake-search-label">{{ __('app.admin.activity.fields.event') }}</label>
                    <select id="activity-event" name="event" class="intake-search-control">
                        <option value="">{{ __('app.admin.activity.all_events') }}</option>
                        @foreach ($events as $event)
                            <option value="{{ $event }}" @selected($filters['event'] === $event)>
                                {{ __("app.admin.activity.events.{$event}") }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="intake-search-field">
                    <label for="activity-date-from" class="intake-search-label">{{ __('app.admin.activity.fields.date_from') }}</label>
                    <input id="activity-date-from" name="date_from" type="date" value="{{ $filters['date_from'] }}" class="intake-search-control">
                </div>

                <div class="intake-search-field">
                    <label for="activity-date-to" class="intake-search-label">{{ __('app.admin.activity.fields.date_to') }}</label>
                    <input id="activity-date-to" name="date_to" type="date" value="{{ $filters['date_to'] }}" class="intake-search-control">
                </div>

                <div class="intake-search-field">
                    <label for="activity-per-page" class="intake-search-label">{{ __('app.admin.activity.per_page') }}</label>
                    <select id="activity-per-page" name="per_page" class="intake-search-control">
                        @foreach (config('activity.per_page_options', [15, 20, 50, 100]) as $size)
                            <option value="{{ $size }}" @selected($filters['per_page'] === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="intake-search-field intake-search-clear-field">
                    <span class="intake-search-label intake-search-label-hidden" aria-hidden="true">&nbsp;</span>
                    <div class="flex gap-2">
                        <button type="submit" class="btn-primary-brand flex-1 px-4 py-2 text-sm">{{ __('app.admin.activity.apply_filters') }}</button>
                        @if ($hasFilters)
                            <a href="{{ route('admin.activity') }}" class="btn-secondary-brand px-4 py-2 text-sm">{{ __('app.admin.activity.clear_filters') }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </form>

        @if ($logs->total() > 0)
            <p class="px-6 pt-4 text-xs text-brand-muted">
                {{ __('app.admin.activity.results_summary', [
                    'from' => $logs->firstItem(),
                    'to' => $logs->lastItem(),
                    'total' => $logs->total(),
                ]) }}
            </p>
        @endif

        @if ($logs->isEmpty())
            <p class="px-6 py-12 text-center text-sm text-brand-muted">
                {{ $hasFilters ? __('app.admin.activity.no_search_results') : __('app.admin.activity.no_records') }}
            </p>
        @else
            <div class="mt-4 overflow-x-auto px-2 pb-4 sm:px-4">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-brand-border text-start text-brand-muted">
                            <th class="px-3 py-3 font-medium">#</th>
                            <th class="px-3 py-3 font-medium">{{ __('app.admin.activity.columns.time') }}</th>
                            <th class="px-3 py-3 font-medium">{{ __('app.admin.activity.columns.user') }}</th>
                            <th class="px-3 py-3 font-medium">{{ __('app.admin.activity.columns.category') }}</th>
                            <th class="px-3 py-3 font-medium">{{ __('app.admin.activity.columns.event') }}</th>
                            <th class="px-3 py-3 font-medium">{{ __('app.admin.activity.columns.description') }}</th>
                            <th class="px-3 py-3 font-medium">{{ __('app.admin.activity.columns.ip_address') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr class="border-b border-brand-border/70 last:border-0">
                                <td class="px-3 py-3 text-brand-muted">{{ $logs->firstItem() + $loop->index }}</td>
                                <td class="px-3 py-3 whitespace-nowrap text-brand-muted">{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                                <td class="px-3 py-3">
                                    @if (filled($log->user_name))
                                        <p class="font-medium text-brand-dark">{{ $log->user_name }}</p>
                                        @if (filled($log->user_email))
                                            <p class="text-xs text-brand-muted">{{ $log->user_email }}</p>
                                        @endif
                                    @else
                                        <span class="text-brand-muted">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3">
                                    <span @class([
                                        'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                        'bg-brand-blue/10 text-brand-blue' => $log->category === 'auth',
                                        'bg-brand-teal/10 text-brand-teal' => $log->category === 'admin',
                                        'bg-amber-500/10 text-amber-700 dark:text-amber-300' => $log->category === 'security',
                                        'bg-slate-500/10 text-slate-600 dark:text-slate-300' => $log->category === 'system',
                                    ])>
                                        {{ __("app.admin.activity.categories.{$log->category}") }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-brand-dark">{{ __("app.admin.activity.events.{$log->event}") }}</td>
                                <td class="px-3 py-3 max-w-xs break-words text-brand-muted">{{ $log->description ?: '—' }}</td>
                                <td class="px-3 py-3 font-mono text-xs text-brand-muted">{{ $log->ip_address ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($logs->hasPages())
                <div class="border-t border-brand-border px-6 py-4">
                    {{ $logs->onEachSide(1)->links() }}
                </div>
            @endif
        @endif
    </section>
</x-layouts.admin>
