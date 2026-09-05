<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInmatePropertyRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isUser() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'inmate_intake_registration_id' => [
                'required',
                'integer',
                Rule::exists('inmate_intake_registrations', 'id'),
            ],
            'entry_cash_amount' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'form_85_number' => ['nullable', 'string', 'max:100'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'form_86_number' => ['nullable', 'string', 'max:100'],
            'withdrawal_amount' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'other_property_receipt_number' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'inmate_intake_registration_id' => __('app.assets.fields.inmate'),
            'entry_cash_amount' => __('app.assets.fields.entry_cash_amount'),
            'form_85_number' => __('app.assets.fields.form_85_number'),
            'deposit_amount' => __('app.assets.fields.deposit_amount'),
            'form_86_number' => __('app.assets.fields.form_86_number'),
            'withdrawal_amount' => __('app.assets.fields.withdrawal_amount'),
            'other_property_receipt_number' => __('app.assets.fields.other_property_receipt_number'),
        ];
    }
}
