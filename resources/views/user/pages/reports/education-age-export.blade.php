<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.reports.education_age') }} — {{ $report['title'] }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-sans-ethiopic:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/education-age-report.css'])
</head>
<body class="crime-report-export-body">
    <div class="crime-report-toolbar">
        <button type="button" class="primary" onclick="window.print()">{{ __('app.reports.crime_type_filters.print') }}</button>
        <a href="{{ route('user.reports.education-age', ['eth_year' => $ethYear, 'eth_month' => $ethMonth]) }}">{{ __('app.reports.crime_type_filters.back') }}</a>
    </div>

    <main class="crime-report-page">
        <div class="crime-report-sheet education-age-report-sheet">
            <div class="education-age-report-heading">
                <span class="education-age-form-code">{{ $report['form_code'] }}</span>
                <h1 class="education-age-report-title">{{ $report['title'] }}</h1>
            </div>

            <div class="education-age-report-table-scroll">
                <div class="education-age-report-table-wrap">
                    @include('user.pages.reports.partials.education-age-table', ['report' => $report])
                </div>
            </div>

            <div class="education-age-signatures">
                <div class="education-age-signature-block">
                    <p class="education-age-signature-label">{{ __('app.reports.education_age_columns.filled_by') }}</p>
                    <div class="education-age-signature-line"></div>
                </div>
                <div class="education-age-signature-block">
                    <p class="education-age-signature-label">{{ __('app.reports.education_age_columns.verified_by') }}</p>
                    <div class="education-age-signature-line"></div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
