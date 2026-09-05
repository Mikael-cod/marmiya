@php
    $registration = $editingRegistration ?? null;
    $isEdit = filled($registration);

    $fieldValue = function (string $field) use ($registration) {
        $value = old($field, $registration?->{$field});

        if ($value instanceof \Illuminate\Support\Carbon) {
            return $value->format('Y-m-d');
        }

        if ($field === 'admission_time' && is_string($value)) {
            return substr($value, 0, 5);
        }

        return $value;
    };
@endphp

<form
    action="{{ $isEdit ? route('user.income.update', $registration) : route('user.income.store') }}"
    method="POST"
    id="intake-registration-form"
    class="intake-form-body intake-form-grid"
    enctype="multipart/form-data"
>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    @foreach (request()->only(['q', 'status', 'from', 'to', 'per_page', 'page']) as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach

    <div class="intake-field">
        <label for="court_file_number" class="intake-label">{{ __('app.income.fields.court_file_number') }} <span class="text-red-500">*</span></label>
        <input id="court_file_number" name="court_file_number" type="text" value="{{ $fieldValue('court_file_number') }}" required class="intake-input">
    </div>

    <div class="intake-field">
        <label for="institution_file_number" class="intake-label">{{ __('app.income.fields.institution_file_number') }} <span class="text-red-500">*</span></label>
        <input id="institution_file_number" name="institution_file_number" type="text" value="{{ $fieldValue('institution_file_number') }}" required class="intake-input">
    </div>

    <div class="intake-field">
        <label for="cell_number" class="intake-label">{{ __('app.income.fields.cell_number') }} <span class="text-red-500">*</span></label>
        <input id="cell_number" name="cell_number" type="text" value="{{ $fieldValue('cell_number') }}" required class="intake-input">
    </div>

    <div class="intake-field intake-field-full">
        <label for="full_name" class="intake-label">{{ __('app.income.fields.full_name') }} <span class="text-red-500">*</span></label>
        <input id="full_name" name="full_name" type="text" value="{{ $fieldValue('full_name') }}" required class="intake-input">
    </div>

    <div class="intake-field intake-field-full">
        <label for="photo" class="intake-label">
            {{ __('app.income.fields.photo') }}
            @unless($isEdit)
                <span class="text-red-500">*</span>
            @endunless
        </label>

        <div class="intake-photo-upload">
            @if ($isEdit && $registration->photo_path)
                <img
                    src="{{ $registration->photoUrl() }}"
                    alt="{{ $registration->full_name }}"
                    class="intake-photo-preview"
                    id="photo-preview"
                    data-default-src="{{ $registration->photoUrl() }}"
                >
            @else
                <div class="intake-photo-preview intake-photo-preview-empty" id="photo-preview">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0v.75H4.5v-.75z"/>
                    </svg>
                </div>
            @endif

            <div class="min-w-0 flex-1">
                <input
                    id="photo"
                    name="photo"
                    type="file"
                    accept="image/jpeg,image/jpg,image/png,image/webp"
                    class="intake-input intake-photo-input"
                    @unless($isEdit) required @endunless
                >
                <p class="mt-1 text-xs text-brand-muted">{{ __('app.income.photo_hint') }}</p>
            </div>
        </div>
    </div>

    <div class="intake-field">
        <label for="crime_type" class="intake-label">{{ __('app.income.fields.crime_type') }} <span class="text-red-500">*</span></label>
        @php
            $selectedCrimeType = $fieldValue('crime_type');
            $crimeTypes = config('crime_types');

            if ($selectedCrimeType && ! in_array($selectedCrimeType, $crimeTypes, true)) {
                $crimeTypes = array_merge([$selectedCrimeType], $crimeTypes);
            }
        @endphp
        <select id="crime_type" name="crime_type" required class="intake-input">
            @unless($selectedCrimeType)
                <option value="" disabled selected hidden>{{ __('app.income.select_crime_type') }}</option>
            @endunless
            @foreach ($crimeTypes as $crimeType)
                <option value="{{ $crimeType }}" @selected($selectedCrimeType === $crimeType)>{{ $crimeType }}</option>
            @endforeach
        </select>
    </div>

    <div class="intake-field">
        <label for="detaining_court" class="intake-label">{{ __('app.income.fields.detaining_court') }} <span class="text-red-500">*</span></label>
        <input id="detaining_court" name="detaining_court" type="text" value="{{ $fieldValue('detaining_court') }}" required class="intake-input">
    </div>

    <div class="intake-field">
        <label for="verdict_court" class="intake-label">{{ __('app.income.fields.verdict_court') }} <span class="text-red-500">*</span></label>
        <input id="verdict_court" name="verdict_court" type="text" value="{{ $fieldValue('verdict_court') }}" required class="intake-input">
    </div>

    <x-eth.date-input
        name="admission_date"
        :label="__('app.income.fields.admission_date')"
        :value="$fieldValue('admission_date')"
        required
    />

    <x-eth.time-input
        name="admission_time"
        :label="__('app.income.fields.admission_time')"
        :value="$fieldValue('admission_time')"
        required
    />

    <x-eth.date-input
        name="verdict_date"
        :label="__('app.income.fields.verdict_date')"
        :value="$fieldValue('verdict_date')"
        required
    />

    <div class="intake-field">
        <span class="intake-label">{{ __('app.income.fields.sentence_status') }} <span class="text-red-500">*</span></span>
        <div class="intake-status-group">
            <label class="intake-status-option">
                <input type="radio" name="sentence_status" value="remand" required @checked($fieldValue('sentence_status') === 'remand')>
                {{ __('app.income.status_remand') }}
            </label>
            <label class="intake-status-option">
                <input type="radio" name="sentence_status" value="convicted" @checked($fieldValue('sentence_status') === 'convicted')>
                {{ __('app.income.status_convicted') }}
            </label>
        </div>
    </div>

    <div class="intake-field">
        <label for="sentence_duration" class="intake-label">{{ __('app.income.fields.sentence_duration') }} <span class="text-red-500">*</span></label>
        @php
            $selectedSentenceType = $fieldValue('sentence_duration');
            $sentenceTypes = config('sentence_types', []);

            if ($selectedSentenceType && ! in_array($selectedSentenceType, $sentenceTypes, true)) {
                $sentenceTypes = array_merge([$selectedSentenceType], $sentenceTypes);
            }
        @endphp
        <select id="sentence_duration" name="sentence_duration" required class="intake-input">
            @unless($selectedSentenceType)
                <option value="" disabled selected hidden>{{ __('app.income.select_sentence_type') }}</option>
            @endunless
            @foreach ($sentenceTypes as $sentenceType)
                <option value="{{ $sentenceType }}" @selected($selectedSentenceType === $sentenceType)>{{ $sentenceType }}</option>
            @endforeach
        </select>
    </div>

    <div class="intake-field">
        <label for="appeal_court" class="intake-label">{{ __('app.income.fields.appeal_court') }} <span class="text-red-500">*</span></label>
        <input id="appeal_court" name="appeal_court" type="text" value="{{ $fieldValue('appeal_court') }}" required class="intake-input">
    </div>

    <x-eth.date-input
        name="sentence_start_date"
        :label="__('app.income.fields.sentence_start_date') . ' (' . __('app.income.from') . ')'"
        :value="$fieldValue('sentence_start_date')"
        required
    />

    <x-eth.date-input
        name="sentence_end_date"
        :label="__('app.income.fields.sentence_end_date') . ' (' . __('app.income.until') . ')'"
        :value="$fieldValue('sentence_end_date')"
        required
    />

    <x-eth.date-input
        name="parole_release_date"
        :label="__('app.income.fields.parole_release_date')"
        :value="$fieldValue('parole_release_date')"
        :hint="__('app.income.parole_release_auto_hint')"
        auto-calculated
        required
    />

    <x-eth.date-input
        name="full_release_date"
        :label="__('app.income.fields.full_release_date')"
        :value="$fieldValue('full_release_date')"
    />

    <div class="intake-field intake-field-full">
        <label for="release_reason" class="intake-label">{{ __('app.income.fields.release_reason') }}</label>
        <textarea id="release_reason" name="release_reason" rows="3" class="intake-input intake-textarea">{{ $fieldValue('release_reason') }}</textarea>
    </div>

    <div class="intake-form-actions">
        <button type="button" class="btn-secondary-brand px-8 py-3" data-intake-modal-close>{{ __('app.income.close') }}</button>
        <button type="reset" class="btn-secondary-brand px-8 py-3">{{ __('app.income.reset') }}</button>
        <button type="submit" class="btn-primary-brand w-full px-8 py-3 sm:w-auto">
            {{ $isEdit ? __('app.income.update') : __('app.income.submit') }}
        </button>
    </div>
</form>
