@props([
    'name',
    'id' => null,
    'label' => null,
    'value' => null,
    'required' => false,
])

@php
    $fieldId = $id ?? $name;
    $gregorianValue = old($name, $value);
    $displayValue = $gregorianValue ? eth_time($gregorianValue) : '';
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
        data-eth-time-display="{{ $fieldId }}"
        value="{{ $displayValue }}"
        placeholder="{{ __('app.calendar.select_time') }}"
        class="intake-input cursor-pointer"
        @if ($required) aria-required="true" @endif
    >

    <input
        id="{{ $fieldId }}"
        name="{{ $name }}"
        type="hidden"
        data-eth-time-input
        value="{{ $gregorianValue }}"
        @if ($required) required @endif
    >
</div>
