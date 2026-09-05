@props([
    'name',
    'id' => null,
    'label' => null,
    'value' => null,
    'required' => false,
    'autoCalculated' => false,
    'hint' => null,
])

@php
    $fieldId = $id ?? $name;
    $gregorianValue = old($name, $value);
    $displayValue = $gregorianValue ? eth_date($gregorianValue) : '';
@endphp

<div {{ $attributes->class(['intake-field']) }}>
    @if ($label)
        <label for="{{ $fieldId }}_display" class="intake-label">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <input
        id="{{ $fieldId }}_display"
        type="text"
        readonly
        data-eth-date-display="{{ $fieldId }}"
        @if ($autoCalculated) data-eth-date-auto-calculated @endif
        value="{{ $displayValue }}"
        placeholder="{{ __('app.calendar.select_date') }}"
        @class([
            'intake-input',
            'intake-input-readonly cursor-default' => $autoCalculated,
            'cursor-pointer' => ! $autoCalculated,
        ])
        @if ($required) aria-required="true" @endif
    >

    <input
        id="{{ $fieldId }}"
        name="{{ $name }}"
        type="hidden"
        data-eth-date-input
        value="{{ $gregorianValue }}"
        @if ($required) required @endif
    >

    @if ($hint)
        <p class="mt-1 text-xs text-brand-muted">{{ $hint }}</p>
    @endif
</div>
