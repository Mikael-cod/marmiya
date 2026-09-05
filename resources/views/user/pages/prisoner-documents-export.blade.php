<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.prisoners.documents_export_title', ['name' => $file->inmate?->full_name ?? '—']) }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-sans-ethiopic:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/prisoner-documents-export.css'])
</head>
<body class="prisoner-documents-export-body">
    <div class="crime-report-toolbar">
        <button type="button" class="primary" onclick="window.print()">{{ __('app.reports.crime_type_filters.print') }}</button>
        <a href="{{ route('user.prisoners', ['documents' => $file->id]) }}">{{ __('app.reports.crime_type_filters.back') }}</a>
    </div>

    <main class="prisoner-documents-export">
        @forelse ($pages as $page)
            <section class="prisoner-documents-export-page">
                <header class="prisoner-documents-export-header">
                    <div>
                        <h1>{{ $file->inmate?->full_name ?? '—' }}</h1>
                        <p>
                            {{ __('app.income.fields.court_file_number') }}: {{ $file->inmate?->court_file_number ?: '—' }}
                            · {{ __('app.income.fields.institution_file_number') }}: {{ $file->inmate?->institution_file_number ?: '—' }}
                        </p>
                    </div>
                    <p class="prisoner-documents-export-page-label">
                        {{ __('app.prisoners.documents_page_number', ['number' => $page->page_number]) }}
                        · <x-eth.datetime :value="$page->created_at" />
                    </p>
                </header>

                <div class="prisoner-documents-export-image-wrap">
                    <img
                        src="{{ $page->imageUrl() }}"
                        alt="{{ __('app.prisoners.documents_page_number', ['number' => $page->page_number]) }}"
                        class="prisoner-documents-export-image"
                    >
                </div>
            </section>
        @empty
            <section class="prisoner-documents-export-empty">
                <p>{{ __('app.prisoners.documents_empty') }}</p>
            </section>
        @endforelse
    </main>
</body>
</html>
