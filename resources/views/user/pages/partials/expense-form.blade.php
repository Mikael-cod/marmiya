@php
    $expense = $editingExpense ?? null;
    $isEdit = filled($expense);

    $fieldValue = function (string $field) use ($expense) {
        if ($field === 'inmate_intake_registration_id') {
            return old('inmate_intake_registration_id', $expense?->inmate_intake_registration_id);
        }

        $value = old($field, $expense?->{$field});

        if ($value instanceof \Illuminate\Support\Carbon) {
            return $value->format('Y-m-d');
        }

        return $value;
    };

    $selectedInmateId = (string) $fieldValue('inmate_intake_registration_id');
    $officialName = old('official_name', $fieldValue('official_name') ?: auth()->user()->name);
    $signatureConfirmed = old('signature_confirmed') || ($isEdit && filled($expense?->signature));

    $copiedFields = [
        'full_name',
        'gender',
        'age',
        'religion',
        'nationality',
        'country_of_birth',
        'admission_date',
        'sentencing_court',
        'sentence_duration',
        'crime_type',
        'court_file_number',
        'institution_id_number',
        'education_skill_before',
    ];

    $copiedValues = [];
    foreach ($copiedFields as $field) {
        $copiedValues[$field] = $isEdit ? ($expense?->{$field} ?? '') : '';
    }

    $photoUrl = $isEdit ? $expense?->inmate?->photoUrl() : null;
@endphp

<form
    action="{{ $isEdit ? route('user.expense.update', $expense) : route('user.expense.store') }}"
    method="POST"
    id="intake-registration-form"
    class="intake-form-body intake-form-grid"
    data-expense-form
    data-inmate-data-url="{{ url('/expense/inmate') }}"
>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    @foreach (request()->only(['q', 'from', 'to', 'per_page', 'page']) as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach

    <div class="intake-form-section-title intake-field-full">{{ __('app.expense.sections.certificate') }}</div>

    <x-eth.date-input
        name="certificate_date"
        :label="__('app.expense.fields.certificate_date')"
        :value="$fieldValue('certificate_date')"
        required
    />

    <div class="intake-field">
        <label for="certificate_number" class="intake-label">{{ __('app.expense.fields.certificate_number') }}</label>
        <input id="certificate_number" name="certificate_number" type="text" value="{{ $fieldValue('certificate_number') }}" class="intake-input" placeholder="0011180">
    </div>

    <div class="intake-form-section-title intake-field-full">{{ __('app.expense.sections.inmate_selection') }}</div>

    <div class="intake-field intake-field-full">
        <label for="inmate_intake_registration_id" class="intake-label">
            {{ __('app.expense.fields.inmate') }}
            <span class="text-red-500">*</span>
        </label>

        @if ($isEdit)
            <input type="hidden" name="inmate_intake_registration_id" value="{{ $expense->inmate_intake_registration_id }}">
            <input
                type="text"
                id="inmate_intake_registration_id"
                value="{{ $expense->full_name }}"
                readonly
                class="intake-input intake-input-readonly"
            >
        @else
            <select
                id="inmate_intake_registration_id"
                name="inmate_intake_registration_id"
                required
                class="intake-input"
                data-expense-inmate-select
                @disabled($availableInmates === [])
            >
                <option value="" disabled @selected(! $selectedInmateId) hidden>{{ __('app.expense.select_inmate') }}</option>
                @foreach ($availableInmates as $inmate)
                    <option value="{{ $inmate['id'] }}" @selected($selectedInmateId === (string) $inmate['id'])>
                        {{ $inmate['label'] }}
                    </option>
                @endforeach
            </select>
            @if ($availableInmates === [])
                <p class="mt-1 text-xs text-red-500">{{ __('app.expense.no_inmates') }}</p>
            @endif
        @endif
    </div>

    <div class="intake-field intake-field-full" id="expense-photo-wrap" @unless($photoUrl) hidden @endunless>
        <span class="intake-label">{{ __('app.expense.fields.photo') }}</span>
        <div class="intake-photo-upload">
            <img
                src="{{ $photoUrl ?? '' }}"
                alt=""
                class="intake-photo-preview"
                id="expense-photo-preview"
            >
        </div>
    </div>

    <div class="intake-form-section-title intake-field-full">
        {{ __('app.expense.sections.copied_info') }}
        <span class="ms-2 text-xs font-normal text-brand-muted">({{ __('app.expense.inmate_data_hint') }})</span>
    </div>

    <div class="intake-field intake-field-span-2">
        <label class="intake-label">{{ __('app.expense.fields.full_name') }}</label>
        <input type="text" id="expense-full-name" class="intake-input intake-input-readonly" readonly value="{{ $copiedValues['full_name'] }}">
    </div>

    <div class="intake-field">
        <label class="intake-label">{{ __('app.expense.fields.age') }}</label>
        <input type="text" id="expense-age" class="intake-input intake-input-readonly" readonly value="{{ $copiedValues['age'] }}">
    </div>

    <div class="intake-field">
        <label class="intake-label">{{ __('app.expense.fields.gender') }}</label>
        <input type="text" id="expense-gender" class="intake-input intake-input-readonly" readonly value="{{ $copiedValues['gender'] }}">
    </div>

    <div class="intake-field">
        <label class="intake-label">{{ __('app.expense.fields.religion') }}</label>
        <input type="text" id="expense-religion" class="intake-input intake-input-readonly" readonly value="{{ $copiedValues['religion'] }}">
    </div>

    <div class="intake-field">
        <label class="intake-label">{{ __('app.expense.fields.nationality') }}</label>
        <input type="text" id="expense-nationality" class="intake-input intake-input-readonly" readonly value="{{ $copiedValues['nationality'] }}">
    </div>

    <div class="intake-field">
        <label class="intake-label">{{ __('app.expense.fields.country_of_birth') }}</label>
        <input type="text" id="expense-country-of-birth" class="intake-input intake-input-readonly" readonly value="{{ $copiedValues['country_of_birth'] }}">
    </div>

    <div class="intake-field intake-field-full">
        <label class="intake-label">{{ __('app.expense.fields.admission_date') }}</label>
        <input type="text" id="expense-admission-date" class="intake-input intake-input-readonly" readonly value="{{ $copiedValues['admission_date'] }}">
    </div>

    <div class="intake-field intake-field-full">
        <label class="intake-label">{{ __('app.expense.fields.sentencing_court') }}</label>
        <input type="text" id="expense-sentencing-court" class="intake-input intake-input-readonly" readonly value="{{ $copiedValues['sentencing_court'] }}">
    </div>

    <div class="intake-field intake-field-full">
        <label class="intake-label">{{ __('app.expense.fields.sentence_duration') }}</label>
        <input type="text" id="expense-sentence-duration" class="intake-input intake-input-readonly" readonly value="{{ $copiedValues['sentence_duration'] }}">
    </div>

    <div class="intake-field intake-field-full">
        <label class="intake-label">{{ __('app.expense.fields.crime_type') }}</label>
        <input type="text" id="expense-crime-type" class="intake-input intake-input-readonly" readonly value="{{ $copiedValues['crime_type'] }}">
    </div>

    <div class="intake-field">
        <label class="intake-label">{{ __('app.expense.fields.court_file_number') }}</label>
        <input type="text" id="expense-court-file-number" class="intake-input intake-input-readonly" readonly value="{{ $copiedValues['court_file_number'] }}">
    </div>

    <div class="intake-field">
        <label class="intake-label">{{ __('app.expense.fields.institution_id_number') }}</label>
        <input type="text" id="expense-institution-id-number" class="intake-input intake-input-readonly" readonly value="{{ $copiedValues['institution_id_number'] }}">
    </div>

    <div class="intake-field intake-field-full">
        <label class="intake-label">{{ __('app.expense.fields.education_skill_before') }}</label>
        <input type="text" id="expense-education-skill-before" class="intake-input intake-input-readonly" readonly value="{{ $copiedValues['education_skill_before'] }}">
    </div>

    <div class="intake-form-section-title intake-field-full">{{ __('app.expense.sections.release_info') }}</div>

    <div class="intake-field intake-field-full">
        <label for="previous_profession" class="intake-label">{{ __('app.expense.fields.learned_in_institution') }}</label>
        <textarea id="previous_profession" name="previous_profession" rows="2" class="intake-input intake-textarea">{{ $fieldValue('previous_profession') }}</textarea>
    </div>

    <div class="intake-field intake-field-full">
        <label for="work_training_provided" class="intake-label">{{ __('app.expense.fields.conduct_in_institution') }}</label>
        <textarea id="work_training_provided" name="work_training_provided" rows="2" class="intake-input intake-textarea">{{ $fieldValue('work_training_provided') }}</textarea>
    </div>

    <div class="intake-field">
        <label for="education_period_provided" class="intake-label">{{ __('app.expense.fields.education_period_provided') }}</label>
        <input id="education_period_provided" name="education_period_provided" type="text" value="{{ $fieldValue('education_period_provided') }}" class="intake-input">
    </div>

    <div class="intake-field">
        <label for="work_experience_during" class="intake-label">{{ __('app.expense.fields.sentence_served') }}</label>
        <input id="work_experience_during" name="work_experience_during" type="text" value="{{ $fieldValue('work_experience_during') }}" class="intake-input">
    </div>

    <x-eth.date-input
        name="release_date"
        :label="__('app.expense.fields.release_date')"
        :value="$fieldValue('release_date')"
        required
    />

    <div class="intake-field intake-field-full">
        <label for="release_reason" class="intake-label">
            {{ __('app.expense.fields.release_reason') }}
            <span class="text-red-500">*</span>
        </label>
        @php
            $releaseReasons = config('release_reasons', []);
            $selectedReleaseReason = $fieldValue('release_reason');
        @endphp
        <select id="release_reason" name="release_reason" required class="intake-input">
            <option value="" disabled @selected(! filled($selectedReleaseReason)) hidden>{{ __('app.expense.select_release_reason') }}</option>
            @if (filled($selectedReleaseReason) && ! in_array($selectedReleaseReason, $releaseReasons, true))
                <option value="{{ $selectedReleaseReason }}" selected>{{ $selectedReleaseReason }}</option>
            @endif
            @foreach ($releaseReasons as $reason)
                <option value="{{ $reason }}" @selected($selectedReleaseReason === $reason)>{{ $reason }}</option>
            @endforeach
        </select>
    </div>

    <div class="intake-field intake-field-full">
        <label for="health_condition" class="intake-label">
            {{ __('app.expense.fields.health_condition') }}
            <span class="text-red-500">*</span>
        </label>
        <textarea id="health_condition" name="health_condition" rows="3" required class="intake-input intake-textarea">{{ $fieldValue('health_condition') }}</textarea>
    </div>

    <div class="intake-form-section-title intake-field-full">{{ __('app.expense.sections.footer') }}</div>

    <div class="intake-field intake-field-full">
        <label for="official_name" class="intake-label">{{ __('app.expense.fields.official_name') }}</label>
        <input id="official_name" type="text" value="{{ $officialName }}" readonly class="intake-input intake-input-readonly">
    </div>

    <div class="intake-field intake-field-full">
        <label class="intake-status-option">
            <input type="checkbox" name="signature_confirmed" value="1" @checked($signatureConfirmed) required>
            {{ __('app.expense.signature_confirm') }}
        </label>
    </div>

    <div class="intake-form-actions">
        <button type="button" class="btn-secondary-brand px-8 py-3" data-intake-modal-close>{{ __('app.expense.close') }}</button>
        <button type="reset" class="btn-secondary-brand px-8 py-3">{{ __('app.expense.reset') }}</button>
        <button type="submit" class="btn-primary-brand w-full px-8 py-3 sm:w-auto">
            {{ $isEdit ? __('app.expense.update') : __('app.expense.submit') }}
        </button>
    </div>
</form>
