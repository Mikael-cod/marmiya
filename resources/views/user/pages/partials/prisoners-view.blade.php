@php
    $inmate = $file->inmate;

    $display = fn ($value) => filled($value) ? $value : '—';

    $genderLabel = match ($file->gender) {
        'male' => __('app.prisoners.gender_male'),
        'female' => __('app.prisoners.gender_female'),
        default => '—',
    };

    $maritalLabel = match ($file->marital_status) {
        'unmarried' => __('app.prisoners.marital_unmarried'),
        'married' => __('app.prisoners.marital_married'),
        'divorced' => __('app.prisoners.marital_divorced'),
        default => $display($file->marital_status),
    };

    $sentenceLabel = $inmate?->sentence_status === 'convicted'
        ? __('app.income.status_convicted')
        : ($inmate?->sentence_status === 'remand' ? __('app.income.status_remand') : '—');
@endphp

<div class="prisoner-view">
    <div class="prisoner-view-header">
        @if ($inmate?->photo_path)
            <img
                src="{{ $inmate->photoUrl() }}"
                alt="{{ $inmate->full_name }}"
                class="prisoner-view-photo"
            >
        @else
            <div class="prisoner-view-photo prisoner-view-photo-placeholder" aria-hidden="true">
                <svg class="h-10 w-10" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0v.75H4.5v-.75z"/>
                </svg>
            </div>
        @endif

        <div class="min-w-0">
            <h3 class="prisoner-view-name">{{ $inmate?->full_name ?? '—' }}</h3>
            <p class="prisoner-view-meta">
                {{ __('app.income.fields.court_file_number') }}: {{ $display($inmate?->court_file_number) }}
                · {{ __('app.income.fields.institution_file_number') }}: {{ $display($inmate?->institution_file_number) }}
            </p>
        </div>
    </div>

    <div class="prisoner-view-section">
        <h4 class="prisoner-view-section-title">{{ __('app.prisoners.sections.inmate_selection') }}</h4>
        <dl class="prisoner-view-grid">
            <div><dt>{{ __('app.prisoners.fields.inmate') }}</dt><dd>{{ $display($inmate?->full_name) }}</dd></div>
            <div><dt>{{ __('app.income.fields.court_file_number') }}</dt><dd>{{ $display($inmate?->court_file_number) }}</dd></div>
            <div><dt>{{ __('app.income.fields.institution_file_number') }}</dt><dd>{{ $display($inmate?->institution_file_number) }}</dd></div>
            <div><dt>{{ __('app.income.fields.cell_number') }}</dt><dd>{{ $display($inmate?->cell_number) }}</dd></div>
            <div><dt>{{ __('app.income.fields.crime_type') }}</dt><dd>{{ $display($inmate?->crime_type) }}</dd></div>
            <div><dt>{{ __('app.income.fields.sentence_status') }}</dt><dd>{{ $sentenceLabel }}</dd></div>
            <div><dt>{{ __('app.income.fields.admission_date') }}</dt><dd>@if($inmate?->admission_date)<x-eth.date :value="$inmate->admission_date" />@else—@endif</dd></div>
        </dl>
    </div>

    <div class="prisoner-view-section">
        <h4 class="prisoner-view-section-title">{{ __('app.prisoners.sections.personal_info') }}</h4>
        <dl class="prisoner-view-grid">
            <div><dt>{{ __('app.prisoners.fields.birth_date') }}</dt><dd>@if($file->birth_date)<x-eth.date :value="$file->birth_date" />@else—@endif</dd></div>
            <div><dt>{{ __('app.prisoners.fields.age') }}</dt><dd>{{ $display($file->age) }}</dd></div>
            <div><dt>{{ __('app.prisoners.fields.mother_name') }}</dt><dd>{{ $display($file->mother_name) }}</dd></div>
            <div><dt>{{ __('app.prisoners.fields.gender') }}</dt><dd>{{ $genderLabel }}</dd></div>
        </dl>
    </div>

    <div class="prisoner-view-section">
        <h4 class="prisoner-view-section-title">{{ __('app.prisoners.sections.birth_place') }}</h4>
        <dl class="prisoner-view-grid">
            @foreach (['birth_region', 'birth_zone', 'birth_woreda', 'birth_kebele'] as $field)
                <div><dt>{{ __('app.prisoners.fields.'.$field) }}</dt><dd>{{ $display($file->{$field}) }}</dd></div>
            @endforeach
        </dl>
    </div>

    <div class="prisoner-view-section">
        <h4 class="prisoner-view-section-title">{{ __('app.prisoners.sections.current_residence') }}</h4>
        <dl class="prisoner-view-grid">
            @foreach (['residence_region', 'residence_zone', 'residence_woreda', 'residence_kebele'] as $field)
                <div><dt>{{ __('app.prisoners.fields.'.$field) }}</dt><dd>{{ $display($file->{$field}) }}</dd></div>
            @endforeach
        </dl>
    </div>

    <div class="prisoner-view-section">
        <h4 class="prisoner-view-section-title">{{ __('app.prisoners.sections.background') }}</h4>
        <dl class="prisoner-view-grid">
            <div><dt>{{ __('app.prisoners.fields.education_level') }}</dt><dd>{{ $display($file->education_level) }}</dd></div>
            <div><dt>{{ __('app.prisoners.fields.occupation') }}</dt><dd>{{ $display($file->occupation) }}</dd></div>
            <div><dt>{{ __('app.prisoners.fields.ethnicity') }}</dt><dd>{{ $display($file->ethnicity) }}</dd></div>
            <div><dt>{{ __('app.prisoners.fields.nationality') }}</dt><dd>{{ $display($file->nationality) }}</dd></div>
            <div><dt>{{ __('app.prisoners.fields.religion') }}</dt><dd>{{ $display($file->religion) }}</dd></div>
            <div><dt>{{ __('app.prisoners.fields.marital_status') }}</dt><dd>{{ $maritalLabel }}</dd></div>
        </dl>
    </div>

    <div class="prisoner-view-section">
        <h4 class="prisoner-view-section-title">{{ __('app.prisoners.sections.physical') }}</h4>
        <dl class="prisoner-view-grid">
            @foreach (['height', 'hair_type', 'appearance', 'forehead', 'nose', 'eye_color', 'teeth', 'lips', 'ears', 'distinguishing_mark'] as $field)
                <div><dt>{{ __('app.prisoners.fields.'.$field) }}</dt><dd>{{ $display($file->{$field}) }}</dd></div>
            @endforeach
        </dl>
    </div>

    <div class="prisoner-view-section">
        <h4 class="prisoner-view-section-title">{{ __('app.prisoners.sections.emergency_contact') }}</h4>
        <dl class="prisoner-view-grid">
            <div><dt>{{ __('app.prisoners.fields.emergency_contact_name') }}</dt><dd>{{ $display($file->emergency_contact_name) }}</dd></div>
            @foreach (['emergency_region', 'emergency_zone', 'emergency_woreda', 'emergency_kebele'] as $field)
                <div><dt>{{ __('app.prisoners.fields.'.$field) }}</dt><dd>{{ $display($file->{$field}) }}</dd></div>
            @endforeach
            <div><dt>{{ __('app.prisoners.fields.emergency_phone_landline') }}</dt><dd>{{ $display($file->emergency_phone_landline) }}</dd></div>
            <div><dt>{{ __('app.prisoners.fields.emergency_phone_mobile') }}</dt><dd>{{ $display($file->emergency_phone_mobile) }}</dd></div>
        </dl>
    </div>

    <div class="prisoner-view-section">
        <h4 class="prisoner-view-section-title">{{ __('app.prisoners.sections.footer') }}</h4>
        <dl class="prisoner-view-grid">
            <div><dt>{{ __('app.prisoners.fields.filled_by_professional_name') }}</dt><dd>{{ $display($file->filled_by_professional_name) }}</dd></div>
            <div><dt>{{ __('app.prisoners.fields.signature') }}</dt><dd>{{ $display($file->signature) }}</dd></div>
            <div><dt>{{ __('app.prisoners.registered_at') }}</dt><dd><x-eth.datetime :value="$file->created_at" /></dd></div>
        </dl>
    </div>
</div>
