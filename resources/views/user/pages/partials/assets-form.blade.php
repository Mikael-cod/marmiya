@php
    $property = $editingProperty ?? null;
    $isEdit = filled($property);

    $fieldValue = function (string $field) use ($property) {
        if ($field === 'inmate_intake_registration_id') {
            return old('inmate_intake_registration_id', $property?->inmate_intake_registration_id);
        }

        return old($field, $property?->{$field});
    };

    $formatMoney = fn ($value) => filled($value) ? number_format((float) $value, 2) : '';
@endphp

<form
    action="{{ $isEdit ? route('user.assets.update', $property) : route('user.assets.store') }}"
    method="POST"
    id="intake-registration-form"
    class="intake-form-body intake-form-grid"
>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    @foreach (request()->only(['q', 'from', 'to', 'per_page', 'page']) as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach

    <div class="intake-field intake-field-full">
        <label for="inmate_intake_registration_id" class="intake-label">
            {{ __('app.assets.fields.inmate') }}
            <span class="text-red-500">*</span>
        </label>
        <select
            id="inmate_intake_registration_id"
            name="inmate_intake_registration_id"
            required
            class="intake-input"
            @disabled($inmates === [])
        >
            <option value="">{{ __('app.assets.select_inmate') }}</option>
            @foreach ($inmates as $inmate)
                <option value="{{ $inmate['id'] }}" @selected((string) $fieldValue('inmate_intake_registration_id') === (string) $inmate['id'])>
                    {{ $inmate['label'] }}
                </option>
            @endforeach
        </select>
        @if ($inmates === [])
            <p class="mt-1 text-xs text-red-500">{{ __('app.assets.no_inmates') }}</p>
        @endif
    </div>

    <div class="intake-field intake-field-full">
        <label for="entry_cash_amount" class="intake-label">{{ __('app.assets.fields.entry_cash_amount') }}</label>
        <input
            id="entry_cash_amount"
            name="entry_cash_amount"
            type="number"
            step="0.01"
            min="0"
            value="{{ $formatMoney($fieldValue('entry_cash_amount')) }}"
            class="intake-input"
            placeholder="0.00"
        >
    </div>

    <div class="intake-field">
        <label for="form_85_number" class="intake-label">{{ __('app.assets.fields.form_85_number') }}</label>
        <input id="form_85_number" name="form_85_number" type="text" value="{{ $fieldValue('form_85_number') }}" class="intake-input">
    </div>

    <div class="intake-field">
        <label for="deposit_amount" class="intake-label">{{ __('app.assets.fields.deposit_amount') }}</label>
        <input
            id="deposit_amount"
            name="deposit_amount"
            type="number"
            step="0.01"
            min="0"
            value="{{ $formatMoney($fieldValue('deposit_amount')) }}"
            class="intake-input"
            placeholder="0.00"
        >
    </div>

    <div class="intake-field">
        <label for="form_86_number" class="intake-label">{{ __('app.assets.fields.form_86_number') }}</label>
        <input id="form_86_number" name="form_86_number" type="text" value="{{ $fieldValue('form_86_number') }}" class="intake-input">
    </div>

    <div class="intake-field">
        <label for="withdrawal_amount" class="intake-label">{{ __('app.assets.fields.withdrawal_amount') }}</label>
        <input
            id="withdrawal_amount"
            name="withdrawal_amount"
            type="number"
            step="0.01"
            min="0"
            value="{{ $formatMoney($fieldValue('withdrawal_amount')) }}"
            class="intake-input"
            placeholder="0.00"
        >
    </div>

    <div class="intake-field intake-field-full">
        <label for="other_property_receipt_number" class="intake-label">{{ __('app.assets.fields.other_property_receipt_number') }}</label>
        <input
            id="other_property_receipt_number"
            name="other_property_receipt_number"
            type="text"
            value="{{ $fieldValue('other_property_receipt_number') }}"
            class="intake-input"
        >
    </div>

    <div class="intake-form-actions">
        <button type="button" class="btn-secondary-brand px-8 py-3" data-intake-modal-close>{{ __('app.assets.close') }}</button>
        <button type="reset" class="btn-secondary-brand px-8 py-3">{{ __('app.assets.reset') }}</button>
        <button type="submit" class="btn-primary-brand w-full px-8 py-3 sm:w-auto">
            {{ $isEdit ? __('app.assets.update') : __('app.assets.submit') }}
        </button>
    </div>
</form>
