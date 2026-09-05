<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.reports.new_intake') }} — {{ $report['title'] }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-sans-ethiopic:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/new-intake-report.css'])
</head>
<body class="crime-report-export-body">
    <div class="crime-report-toolbar">
        <button type="button" class="primary" onclick="window.print()">{{ __('app.reports.crime_type_filters.print') }}</button>
        <a href="{{ route('user.reports.new-intake', ['eth_year' => $ethYear, 'eth_month' => $ethMonth]) }}">{{ __('app.reports.crime_type_filters.back') }}</a>
    </div>

    <main class="crime-report-page">
        <div class="crime-report-sheet new-intake-report-sheet">
            <div class="new-intake-report-heading">
                <span class="new-intake-form-code">{{ $report['form_code'] }}</span>
                <h1 class="new-intake-report-title">{{ $report['title'] }}</h1>
            </div>

            <div class="crime-type-report-table-scroll">
                <div class="crime-type-report-table-wrap">
                    @include('user.pages.reports.partials.new-intake-table', ['report' => $report])
                </div>
            </div>

            <div class="new-intake-signatures">
                <div class="new-intake-signature-block">
                    <p class="new-intake-signature-label">{{ __('app.reports.new_intake_columns.verified_by') }}</p>
                    <div class="new-intake-signature-line"></div>
                </div>
                <div class="new-intake-signature-block new-intake-signature-block-narrow">
                    <p class="new-intake-signature-label">{{ __('app.reports.new_intake_columns.signature') }}</p>
                    <div class="new-intake-signature-line"></div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
