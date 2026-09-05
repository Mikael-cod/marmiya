<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateInmateExpenseRegistrationRequest extends StoreInmateExpenseRegistrationRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $expense = $this->route('inmate_expense_registration');
        $allowedReasons = array_values(config('release_reasons', []));

        if (filled($expense?->release_reason) && ! in_array($expense->release_reason, $allowedReasons, true)) {
            $allowedReasons[] = $expense->release_reason;
        }

        return [
            'inmate_intake_registration_id' => [
                'required',
                'integer',
                Rule::exists('inmate_intake_registrations', 'id'),
                Rule::unique('inmate_expense_registrations', 'inmate_intake_registration_id')
                    ->ignore($expense?->id),
            ],
            'certificate_date' => ['required', 'date'],
            'certificate_number' => ['nullable', 'string', 'max:100'],
            'previous_profession' => ['nullable', 'string', 'max:2000'],
            'work_training_provided' => ['nullable', 'string', 'max:2000'],
            'education_period_provided' => ['nullable', 'string', 'max:255'],
            'work_experience_during' => ['nullable', 'string', 'max:255'],
            'release_reason' => ['required', 'string', Rule::in($allowedReasons)],
            'release_date' => ['required', 'date'],
            'health_condition' => ['required', 'string', 'max:2000'],
            'signature_confirmed' => ['accepted'],
        ];
    }
}
