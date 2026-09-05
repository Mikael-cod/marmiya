<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInmateExpenseRegistrationRequest extends FormRequest
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
                Rule::unique('inmate_expense_registrations', 'inmate_intake_registration_id'),
            ],
            'certificate_date' => ['required', 'date'],
            'certificate_number' => ['nullable', 'string', 'max:100'],
            'previous_profession' => ['nullable', 'string', 'max:2000'],
            'work_training_provided' => ['nullable', 'string', 'max:2000'],
            'education_period_provided' => ['nullable', 'string', 'max:255'],
            'work_experience_during' => ['nullable', 'string', 'max:255'],
            'release_reason' => ['required', 'string', Rule::in(array_values(config('release_reasons', [])))],
            'release_date' => ['required', 'date'],
            'health_condition' => ['required', 'string', 'max:2000'],
            'signature_confirmed' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'inmate_intake_registration_id' => __('app.expense.fields.inmate'),
            'certificate_date' => __('app.expense.fields.certificate_date'),
            'certificate_number' => __('app.expense.fields.certificate_number'),
            'previous_profession' => __('app.expense.fields.learned_in_institution'),
            'work_training_provided' => __('app.expense.fields.conduct_in_institution'),
            'education_period_provided' => __('app.expense.fields.education_period_provided'),
            'work_experience_during' => __('app.expense.fields.sentence_served'),
            'release_reason' => __('app.expense.fields.release_reason'),
            'release_date' => __('app.expense.fields.release_date'),
            'health_condition' => __('app.expense.fields.health_condition'),
            'signature_confirmed' => __('app.expense.fields.signature_confirmed'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'signature_confirmed.accepted' => __('app.expense.signature_required'),
        ];
    }
}
