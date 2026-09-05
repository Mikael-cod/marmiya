@php
    $file = $editingFile ?? null;
    $isEdit = filled($file);

    $fieldValue = function (string $field) use ($file) {
        if ($field === 'inmate_intake_registration_id') {
            return old('inmate_intake_registration_id', $file?->inmate_intake_registration_id);
        }

        $value = old($field, $file?->{$field});

        if ($value instanceof \Illuminate\Support\Carbon) {
            return $value->format('Y-m-d');
        }

        return $value;
    };

    $selectedInmateId = (string) $fieldValue('inmate_intake_registration_id');
    $professionalName = old('filled_by_professional_name', $fieldValue('filled_by_professional_name') ?: auth()->user()->name);
    $signatureConfirmed = old('signature_confirmed') || ($isEdit && filled($file?->signature));
@endphp

<form
    action="{{ $isEdit ? route('user.prisoners.update', $file) : route('user.prisoners.store') }}"
    method="POST"
    id="intake-registration-form"
    class="intake-form-body intake-form-grid"
>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    @foreach (request()->only(['q', 'gender', 'from', 'to', 'per_page', 'page']) as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach

    <div class="intake-form-section-title intake-field-full">{{ __('app.prisoners.sections.inmate_selection') }}</div>

    <div class="intake-field intake-field-full">
        <label for="inmate_intake_registration_id" class="intake-label">
            {{ __('app.prisoners.fields.inmate') }}
            <span class="text-red-500">*</span>
        </label>

        @if ($isEdit)
            <input type="hidden" name="inmate_intake_registration_id" value="{{ $file->inmate_intake_registration_id }}">
            <input
                type="text"
                id="inmate_intake_registration_id"
                value="{{ $file->inmate?->full_name }}"
                readonly
                class="intake-input intake-input-readonly"
            >
        @else
            <select
                id="inmate_intake_registration_id"
                name="inmate_intake_registration_id"
                required
                class="intake-input"
                data-inmate-select
                @disabled($availableInmates === [])
            >
                <option value="">{{ __('app.prisoners.select_inmate') }}</option>
                @foreach ($availableInmates as $inmate)
                    <option
                        value="{{ $inmate['id'] }}"
                        data-court-file="{{ $inmate['court_file_number'] ?? '' }}"
                        data-institution-file="{{ $inmate['institution_file_number'] ?? '' }}"
                        @selected($selectedInmateId === (string) $inmate['id'])
                    >
                        {{ $inmate['label'] }}
                    </option>
                @endforeach
            </select>
            @if ($availableInmates === [])
                <p class="mt-1 text-xs text-red-500">{{ __('app.prisoners.no_inmates') }}</p>
            @endif
        @endif
    </div>

    <div class="intake-field">
        <label class="intake-label">{{ __('app.income.fields.court_file_number') }}</label>
        <input
            type="text"
            id="inmate-court-file"
            class="intake-input intake-input-readonly"
            readonly
            value="{{ $isEdit ? ($file->inmate?->court_file_number ?? '') : '' }}"
        >
    </div>

    <div class="intake-field">
        <label class="intake-label">{{ __('app.income.fields.institution_file_number') }}</label>
        <input
            type="text"
            id="inmate-institution-file"
            class="intake-input intake-input-readonly"
            readonly
            value="{{ $isEdit ? ($file->inmate?->institution_file_number ?? '') : '' }}"
        >
    </div>

    <div class="intake-form-section-title intake-field-full">{{ __('app.prisoners.sections.personal_info') }}</div>

    <x-eth.date-input
        name="birth_date"
        :label="__('app.prisoners.fields.birth_date')"
        :value="$fieldValue('birth_date')"
        required
    />

    <div class="intake-field">
        <label for="age" class="intake-label">{{ __('app.prisoners.fields.age') }} <span class="text-red-500">*</span></label>
        <input
            id="age"
            name="age"
            type="number"
            min="0"
            max="120"
            value="{{ $fieldValue('age') }}"
            required
            readonly
            class="intake-input intake-input-readonly"
        >
    </div>

    <div class="intake-field intake-field-full">
        <label for="mother_name" class="intake-label">{{ __('app.prisoners.fields.mother_name') }} <span class="text-red-500">*</span></label>
        <input id="mother_name" name="mother_name" type="text" value="{{ $fieldValue('mother_name') }}" required class="intake-input">
    </div>

    <div class="intake-field">
        <label for="gender" class="intake-label">{{ __('app.prisoners.fields.gender') }} <span class="text-red-500">*</span></label>
        <select id="gender" name="gender" required class="intake-input">
            <option value="">{{ __('app.prisoners.select_gender') }}</option>
            <option value="male" @selected($fieldValue('gender') === 'male')>{{ __('app.prisoners.gender_male') }}</option>
            <option value="female" @selected($fieldValue('gender') === 'female')>{{ __('app.prisoners.gender_female') }}</option>
        </select>
    </div>

    <div class="intake-form-section-title intake-field-full">{{ __('app.prisoners.sections.birth_place') }}</div>

    @foreach (['birth_region', 'birth_zone', 'birth_woreda', 'birth_kebele'] as $field)
        <div class="intake-field">
            <label for="{{ $field }}" class="intake-label">{{ __('app.prisoners.fields.'.$field) }} <span class="text-red-500">*</span></label>
            <input id="{{ $field }}" name="{{ $field }}" type="text" value="{{ $fieldValue($field) }}" required class="intake-input">
        </div>
    @endforeach

    <div class="intake-form-section-title intake-field-full">{{ __('app.prisoners.sections.current_residence') }}</div>

    @foreach (['residence_region', 'residence_zone', 'residence_woreda', 'residence_kebele'] as $field)
        <div class="intake-field">
            <label for="{{ $field }}" class="intake-label">{{ __('app.prisoners.fields.'.$field) }} <span class="text-red-500">*</span></label>
            <input id="{{ $field }}" name="{{ $field }}" type="text" value="{{ $fieldValue($field) }}" required class="intake-input">
        </div>
    @endforeach

    <div class="intake-form-section-title intake-field-full">{{ __('app.prisoners.sections.background') }}</div>

    <div class="intake-field">
        <label for="education_level" class="intake-label">{{ __('app.prisoners.fields.education_level') }} <span class="text-red-500">*</span></label>
        @php
            $selectedEducationLevel = $fieldValue('education_level');
            $educationLevels = config('prisoner_education_levels', []);

            if ($selectedEducationLevel && ! in_array($selectedEducationLevel, $educationLevels, true)) {
                $educationLevels = array_merge([$selectedEducationLevel], $educationLevels);
            }
        @endphp
        <select id="education_level" name="education_level" required class="intake-input">
            @unless($selectedEducationLevel)
                <option value="" disabled selected hidden>{{ __('app.prisoners.select_education_level') }}</option>
            @endunless
            @foreach ($educationLevels as $level)
                <option value="{{ $level }}" @selected($selectedEducationLevel === $level)>{{ $level }}</option>
            @endforeach
        </select>
    </div>

    <div class="intake-field">
        <label for="occupation" class="intake-label">{{ __('app.prisoners.fields.occupation') }} <span class="text-red-500">*</span></label>
        <input id="occupation" name="occupation" type="text" value="{{ $fieldValue('occupation') }}" required class="intake-input">
    </div>

    <div class="intake-field">
        <label for="ethnicity" class="intake-label">{{ __('app.prisoners.fields.ethnicity') }} <span class="text-red-500">*</span></label>
        <input id="ethnicity" name="ethnicity" type="text" value="{{ $fieldValue('ethnicity') }}" required class="intake-input">
    </div>

    <div class="intake-field">
        <label for="nationality" class="intake-label">{{ __('app.prisoners.fields.nationality') }} <span class="text-red-500">*</span></label>
        <input id="nationality" name="nationality" type="text" value="{{ $fieldValue('nationality') }}" required class="intake-input">
    </div>

    <div class="intake-field">
        <label for="religion" class="intake-label">{{ __('app.prisoners.fields.religion') }} <span class="text-red-500">*</span></label>
        <input id="religion" name="religion" type="text" value="{{ $fieldValue('religion') }}" required class="intake-input">
    </div>

    <div class="intake-field">
        <label for="marital_status" class="intake-label">{{ __('app.prisoners.fields.marital_status') }} <span class="text-red-500">*</span></label>
        <select id="marital_status" name="marital_status" required class="intake-input">
            <option value="">{{ __('app.prisoners.select_marital_status') }}</option>
            <option value="unmarried" @selected($fieldValue('marital_status') === 'unmarried')>{{ __('app.prisoners.marital_unmarried') }}</option>
            <option value="married" @selected($fieldValue('marital_status') === 'married')>{{ __('app.prisoners.marital_married') }}</option>
            <option value="divorced" @selected($fieldValue('marital_status') === 'divorced')>{{ __('app.prisoners.marital_divorced') }}</option>
        </select>
    </div>

    <div class="intake-form-section-title intake-field-full">{{ __('app.prisoners.sections.physical') }}</div>

    <div class="intake-field">
        <label for="height" class="intake-label">{{ __('app.prisoners.fields.height') }} <span class="text-red-500">*</span></label>
        <input id="height" name="height" type="text" value="{{ $fieldValue('height') }}" required class="intake-input">
    </div>

    @foreach (['hair_type', 'appearance', 'forehead', 'nose', 'eye_color', 'lips', 'ears'] as $field)
        @php
            $selectedValue = $fieldValue($field);
            $options = config('prisoner_physical_options.'.$field, []);

            if ($selectedValue && ! in_array($selectedValue, $options, true)) {
                $options = array_merge([$selectedValue], $options);
            }
        @endphp
        <div class="intake-field">
            <label for="{{ $field }}" class="intake-label">{{ __('app.prisoners.fields.'.$field) }} <span class="text-red-500">*</span></label>
            <select id="{{ $field }}" name="{{ $field }}" required class="intake-input">
                @unless($selectedValue)
                    <option value="" disabled selected hidden>{{ __('app.prisoners.select_option') }}</option>
                @endunless
                @foreach ($options as $option)
                    <option value="{{ $option }}" @selected($selectedValue === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </div>
    @endforeach

    <div class="intake-field">
        <label for="teeth" class="intake-label">{{ __('app.prisoners.fields.teeth') }} <span class="text-red-500">*</span></label>
        <input id="teeth" name="teeth" type="text" value="{{ $fieldValue('teeth') }}" required class="intake-input">
    </div>

    <div class="intake-field intake-field-full">
        <label for="distinguishing_mark" class="intake-label">{{ __('app.prisoners.fields.distinguishing_mark') }}</label>
        <input id="distinguishing_mark" name="distinguishing_mark" type="text" value="{{ $fieldValue('distinguishing_mark') }}" class="intake-input">
    </div>

    <div class="intake-form-section-title intake-field-full">{{ __('app.prisoners.sections.emergency_contact') }}</div>

    <div class="intake-field intake-field-full">
        <label for="emergency_contact_name" class="intake-label">{{ __('app.prisoners.fields.emergency_contact_name') }} <span class="text-red-500">*</span></label>
        <input id="emergency_contact_name" name="emergency_contact_name" type="text" value="{{ $fieldValue('emergency_contact_name') }}" required class="intake-input">
    </div>

    @foreach (['emergency_region', 'emergency_zone', 'emergency_woreda', 'emergency_kebele'] as $field)
        <div class="intake-field">
            <label for="{{ $field }}" class="intake-label">{{ __('app.prisoners.fields.'.$field) }} <span class="text-red-500">*</span></label>
            <input id="{{ $field }}" name="{{ $field }}" type="text" value="{{ $fieldValue($field) }}" required class="intake-input">
        </div>
    @endforeach

    <div class="intake-field">
        <label for="emergency_phone_landline" class="intake-label">{{ __('app.prisoners.fields.emergency_phone_landline') }}</label>
        <input id="emergency_phone_landline" name="emergency_phone_landline" type="text" value="{{ $fieldValue('emergency_phone_landline') }}" class="intake-input">
    </div>

    <div class="intake-field">
        <label for="emergency_phone_mobile" class="intake-label">{{ __('app.prisoners.fields.emergency_phone_mobile') }}</label>
        <input id="emergency_phone_mobile" name="emergency_phone_mobile" type="text" value="{{ $fieldValue('emergency_phone_mobile') }}" class="intake-input">
    </div>

    <div class="intake-form-section-title intake-field-full">{{ __('app.prisoners.sections.footer') }}</div>

    <div class="intake-field intake-field-full">
        <label for="filled_by_professional_name_display" class="intake-label">{{ __('app.prisoners.fields.filled_by_professional_name') }} <span class="text-red-500">*</span></label>
        <input type="hidden" name="filled_by_professional_name" value="{{ $professionalName }}">
        <input
            id="filled_by_professional_name_display"
            type="text"
            value="{{ $professionalName }}"
            readonly
            class="intake-input intake-input-readonly"
        >
    </div>

    <div class="intake-field intake-field-full">
        <label class="intake-checkbox-label">
            <input
                id="signature_confirmed"
                name="signature_confirmed"
                type="checkbox"
                value="1"
                class="intake-checkbox"
                @checked($signatureConfirmed)
                required
            >
            <span>{{ __('app.prisoners.signature_confirm') }}</span>
        </label>
    </div>

    <div class="intake-form-actions">
        <button type="button" class="btn-secondary-brand px-8 py-3" data-intake-modal-close>{{ __('app.prisoners.close') }}</button>
        <button type="reset" class="btn-secondary-brand px-8 py-3">{{ __('app.prisoners.reset') }}</button>
        <button type="submit" class="btn-primary-brand w-full px-8 py-3 sm:w-auto">
            {{ $isEdit ? __('app.prisoners.update') : __('app.prisoners.submit') }}
        </button>
    </div>
</form>
