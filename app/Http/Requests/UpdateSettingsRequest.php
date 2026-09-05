<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isUser() ?? false;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'per_page' => ['required', 'integer', Rule::in(config('user_settings.per_page_options', [10, 15, 25, 50]))],
            'report_eth_year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'report_eth_month' => ['nullable', 'integer', 'min:1', 'max:13'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'per_page' => __('app.settings.fields.per_page'),
            'report_eth_year' => __('app.settings.fields.report_eth_year'),
            'report_eth_month' => __('app.settings.fields.report_eth_month'),
        ];
    }
}
