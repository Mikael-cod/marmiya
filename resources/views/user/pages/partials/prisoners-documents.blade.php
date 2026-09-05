@php
    $listQuery = request()->only(['q', 'gender', 'from', 'to', 'per_page', 'page']);
    $inmate = $file->inmate;
@endphp

<div class="prisoner-documents">
    <div class="prisoner-documents-header">
        <div class="prisoner-documents-header-info">
            <h3 class="prisoner-documents-name">{{ $inmate?->full_name ?? '—' }}</h3>
            <dl class="prisoner-documents-meta">
                <div>
                    <dt>{{ __('app.income.fields.court_file_number') }}</dt>
                    <dd>{{ $inmate?->court_file_number ?: '—' }}</dd>
                </div>
                <div>
                    <dt>{{ __('app.income.fields.institution_file_number') }}</dt>
                    <dd>{{ $inmate?->institution_file_number ?: '—' }}</dd>
                </div>
            </dl>
        </div>

        @if ($file->pages->isNotEmpty())
            <div class="prisoner-documents-header-actions">
                <a
                    href="{{ route('user.prisoners.documents.export', $file) }}"
                    target="_blank"
                    rel="noopener"
                    class="btn-primary-brand prisoner-documents-export-btn"
                >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2"/>
                </svg>
                {{ __('app.prisoners.documents_export_pdf') }}
                </a>
            </div>
        @endif
    </div>

    <form
        method="POST"
        action="{{ route('user.prisoners.pages.store', $file) }}"
        enctype="multipart/form-data"
        class="prisoner-documents-upload"
    >
        @csrf
        @foreach ($listQuery as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
        <input type="hidden" name="documents" value="{{ $file->id }}">

        <label for="prisoner-document-pages" class="prisoner-documents-dropzone">
            <input
                id="prisoner-document-pages"
                name="pages[]"
                type="file"
                accept="image/jpeg,image/jpg,image/png,image/webp"
                multiple
                required
                class="prisoner-documents-input"
                data-prisoner-documents-input
            >
            <span class="prisoner-documents-dropzone-icon" aria-hidden="true">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                </svg>
            </span>
            <span class="prisoner-documents-dropzone-title">{{ __('app.prisoners.documents_upload_title') }}</span>
            <span class="prisoner-documents-dropzone-hint">{{ __('app.prisoners.documents_upload_hint') }}</span>
            <span class="prisoner-documents-dropzone-files hidden" data-prisoner-documents-selected></span>
        </label>

        @error('pages')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
        @error('pages.*')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror

        <div class="mt-4 flex justify-end">
            <button type="submit" class="btn-primary-brand">
                {{ __('app.prisoners.documents_upload_submit') }}
            </button>
        </div>
    </form>

    @if ($file->pages->isEmpty())
        <div class="prisoner-documents-empty">
            <p>{{ __('app.prisoners.documents_empty') }}</p>
        </div>
    @else
        <div class="prisoner-documents-grid">
            @foreach ($file->pages as $page)
                <article class="prisoner-documents-card">
                    <div class="prisoner-documents-card-head">
                        <span class="prisoner-documents-page-no">
                            {{ __('app.prisoners.documents_page_number', ['number' => $page->page_number]) }}
                        </span>
                        <span class="prisoner-documents-page-date">
                            <x-eth.datetime :value="$page->created_at" />
                        </span>
                    </div>

                    <a href="{{ $page->imageUrl() }}" target="_blank" rel="noopener" class="prisoner-documents-image-link">
                        <img
                            src="{{ $page->imageUrl() }}"
                            alt="{{ __('app.prisoners.documents_page_number', ['number' => $page->page_number]) }}"
                            class="prisoner-documents-image"
                            loading="lazy"
                        >
                    </a>

                    <form
                        action="{{ route('user.prisoners.pages.destroy', [$file, $page]) }}"
                        method="POST"
                        class="prisoner-documents-delete-form"
                        onsubmit="return confirm(@js(__('app.prisoners.documents_delete_confirm')))"
                    >
                        @csrf
                        @method('DELETE')
                        @foreach ($listQuery as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <input type="hidden" name="documents" value="{{ $file->id }}">
                        <button type="submit" class="intake-action-btn intake-action-delete w-full justify-center">
                            {{ __('app.prisoners.documents_delete_page') }}
                        </button>
                    </form>
                </article>
            @endforeach
        </div>
    @endif
</div>
