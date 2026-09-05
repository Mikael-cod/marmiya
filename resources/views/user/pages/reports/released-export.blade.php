<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.reports.released') }} — {{ $report['title'] }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-sans-ethiopic:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/released-report.css'])
</head>
<body class="crime-report-export-body">
    <div class="crime-report-toolbar">
        <button type="button" class="primary" onclick="window.print()">{{ __('app.reports.crime_type_filters.print') }}</button>
        <a href="{{ route('user.reports.released', ['eth_year' => $ethYear, 'eth_month' => $ethMonth]) }}">{{ __('app.reports.crime_type_filters.back') }}</a>
    </div>

    <main class="crime-report-page">
        <div class="crime-report-sheet released-report-sheet">
            <div class="released-report-heading">
                <span class="released-form-code">{{ $report['form_code'] }}</span>
                <h1 class="released-report-title">{{ $report['title'] }}</h1>
            </div>

            <div class="released-report-table-scroll">
                <div class="released-report-table-wrap">
                    @include('user.pages.reports.partials.released-table', ['report' => $report])
                </div>
            </div>

            <div class="released-signatures">
                <div class="released-signature-block">
                    <p class="released-signature-label">{{ __('app.reports.released_columns.verified_by') }}</p>
                    <div class="released-signature-line"></div>
                </div>
                <div class="released-signature-block released-signature-block-narrow">
                    <p class="released-signature-label">{{ __('app.reports.released_columns.signature') }}</p>
                    <div class="released-signature-line"></div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
