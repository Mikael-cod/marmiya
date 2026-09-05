<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.reports.under_18') }} — {{ $report['title'] }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-sans-ethiopic:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/under-18-report.css'])
</head>
<body class="crime-report-export-body">
    <div class="crime-report-toolbar">
        <button type="button" class="primary" onclick="window.print()">{{ __('app.reports.crime_type_filters.print') }}</button>
        <a href="{{ route('user.reports.under-18', ['eth_year' => $ethYear, 'eth_month' => $ethMonth]) }}">{{ __('app.reports.crime_type_filters.back') }}</a>
    </div>

    <main class="crime-report-page">
        <div class="crime-report-sheet under-18-report-sheet">
            <h1 class="under-18-report-title">{{ $report['title'] }}</h1>

            <div class="under-18-report-table-scroll">
                <div class="under-18-report-table-wrap">
                    @include('user.pages.reports.partials.under-18-table', ['report' => $report])
                </div>
            </div>

            @include('user.pages.reports.partials.under-18-signatures')
        </div>
    </main>
</body>
</html>
