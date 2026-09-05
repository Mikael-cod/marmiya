<x-layouts.user :title="$title">
    <section class="crime-type-page-header overflow-hidden rounded-2xl border border-brand-border bg-brand-surface shadow-auth-card">
        <div class="relative px-4 py-3 sm:px-5">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-brand-blue/5 via-transparent to-brand-teal/5"></div>
            <div class="relative flex flex-row flex-wrap items-center justify-between gap-x-4 gap-y-2">
                <h1 class="crime-type-page-title">{{ $title }}</h1>

                <form method="GET" action="{{ route('user.reports.new-intake') }}" class="crime-type-page-filters">
                    <label for="eth_year" class="sr-only">{{ __('app.reports.crime_type_filters.year') }}</label>
                    <select id="eth_year" name="eth_year" class="intake-input crime-type-filter-input" title="{{ __('app.reports.crime_type_filters.year') }}">
                        @foreach ($ethYears as $year)
                            <option value="{{ $year }}" @selected($ethYear === $year)>{{ $year }}</option>
                        @endforeach
                    </select>

                    <label for="eth_month" class="sr-only">{{ __('app.reports.crime_type_filters.month') }}</label>
                    <select id="eth_month" name="eth_month" class="intake-input crime-type-filter-input" title="{{ __('app.reports.crime_type_filters.month') }}">
                        @foreach ($ethMonths as $monthNumber => $monthName)
                            <option value="{{ $monthNumber }}" @selected($ethMonth === $monthNumber)>{{ $monthName }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn-primary-brand crime-type-filter-btn">{{ __('app.reports.crime_type_filters.generate') }}</button>
                    <a
                        href="{{ route('user.reports.new-intake.export', ['eth_year' => $ethYear, 'eth_month' => $ethMonth]) }}"
                        target="_blank"
                        rel="noopener"
                        class="crime-type-filter-btn crime-type-filter-btn-outline"
                    >
                        {{ __('app.reports.crime_type_filters.export') }}
                    </a>
                </form>
            </div>
        </div>
    </section>

    <section class="card-surface new-intake-report-card mt-6 shadow-auth-card">
        <div class="new-intake-report-sheet">
            <div class="new-intake-report-heading">
                <span class="new-intake-form-code">{{ $report['form_code'] }}</span>
                <h2 class="new-intake-report-title">{{ $report['title'] }}</h2>
            </div>

            <div class="crime-type-report-table-scroll">
                <p class="crime-type-scroll-hint">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                    {{ __('app.reports.crime_type_filters.scroll_hint') }}
                </p>
                <div class="crime-type-report-table-wrap">
                    @include('user.pages.reports.partials.new-intake-table', ['report' => $report])
                </div>
            </div>
        </div>
    </section>
</x-layouts.user>
