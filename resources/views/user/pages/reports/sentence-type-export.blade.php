<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.reports.sentence_type') }} — {{ $report['title'] }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-sans-ethiopic:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/sentence-type-report.css'])
</head>
<body class="crime-report-export-body">
    <div class="crime-report-toolbar">
        <button type="button" class="primary" onclick="window.print()">{{ __('app.reports.crime_type_filters.print') }}</button>
        <a href="{{ route('user.reports.sentence-type', ['eth_year' => $ethYear, 'eth_month' => $ethMonth]) }}">{{ __('app.reports.crime_type_filters.back') }}</a>
    </div>

    <main class="crime-report-page">
        <div class="crime-report-sheet sentence-type-report-sheet">
            <div class="sentence-type-report-heading">
                <span class="sentence-type-form-code">{{ $report['form_code'] }}</span>
                <h1 class="sentence-type-report-title">{{ $report['title'] }}</h1>
            </div>

            <div class="sentence-type-report-table-scroll">
                <div class="sentence-type-report-table-wrap">
                    @include('user.pages.reports.partials.sentence-type-table', ['report' => $report])
                </div>
            </div>

            <div class="sentence-type-signatures">
                <div class="sentence-type-signature-block">
                    <p class="sentence-type-signature-label">{{ __('app.reports.sentence_type_columns.attestation') }}</p>
                    <div class="sentence-type-signature-line"></div>
                </div>
                <div class="sentence-type-signature-block sentence-type-signature-block-narrow">
                    <p class="sentence-type-signature-label">{{ __('app.reports.sentence_type_columns.signature') }}</p>
                    <div class="sentence-type-signature-line"></div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
