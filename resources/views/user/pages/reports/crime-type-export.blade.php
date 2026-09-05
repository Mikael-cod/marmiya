<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.reports.crime_type') }} — {{ $report['title'] }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-sans-ethiopic:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/crime-type-report.css'])
</head>
<body class="crime-report-export-body">
    <div class="crime-report-toolbar">
        <button type="button" class="primary" onclick="window.print()">{{ __('app.reports.crime_type_filters.print') }}</button>
        <a href="{{ route('user.reports.crime-type', ['eth_year' => $ethYear, 'eth_month' => $ethMonth]) }}">{{ __('app.reports.crime_type_filters.back') }}</a>
    </div>

    <main class="crime-report-page">
        <div class="crime-report-sheet crime-type-report-sheet">
            <h1 class="crime-type-report-title">{{ $report['title'] }}</h1>
            <p class="crime-type-report-meta">
                {{ __('app.reports.crime_type_filters.period_days', ['days' => $report['period']['days']]) }}
            </p>

            <div class="crime-type-report-table-scroll">
                <p class="crime-type-scroll-hint">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                    {{ __('app.reports.crime_type_filters.scroll_hint') }}
                </p>
                <div class="crime-type-report-table-wrap">
                    @include('user.pages.reports.partials.crime-type-table', ['report' => $report])
                </div>
            </div>
        </div>
    </main>
</body>
</html>
