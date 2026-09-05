<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.reports.recidivist') }} — {{ $report['title'] }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-sans-ethiopic:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/recidivist-report.css'])
</head>
<body class="crime-report-export-body">
    <div class="crime-report-toolbar">
        <button type="button" class="primary" onclick="window.print()">{{ __('app.reports.crime_type_filters.print') }}</button>
        <a href="{{ route('user.reports.recidivist', ['eth_year' => $ethYear, 'eth_month' => $ethMonth]) }}">{{ __('app.reports.crime_type_filters.back') }}</a>
    </div>

    <main class="crime-report-page">
        <div class="crime-report-sheet rc-report-sheet">
            <h1 class="rc-report-title">{{ $report['title'] }}</h1>

            <div class="rc-report-table-scroll">
                <div class="rc-report-table-wrap">
                    @include('user.pages.reports.partials.recidivist-table', ['report' => $report])
                </div>
            </div>

            @include('user.pages.reports.partials.recidivist-signatures')
        </div>
    </main>
</body>
</html>
