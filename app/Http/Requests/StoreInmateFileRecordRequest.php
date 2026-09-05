<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class StoreInmateFileRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isUser() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $payload = [
            'filled_by_professional_name' => $this->user()?->name,
        ];

        if ($this->filled('birth_date')) {
            $payload['age'] = Carbon::parse($this->input('birth_date'), 'Africa/Addis_Ababa')->age;
        }

        $this->merge($payload);
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'inmate_intake_registration_id' => [
                'required',
                'integer',
                Rule::exists('inmate_intake_registrations', 'id'),
                Rule::unique('inmate_file_records', 'inmate_intake_registration_id'),
            ],
            'birth_date' => ['required', 'date'],
            'age' => ['required', 'integer', 'min:0', 'max:120'],
            'mother_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'birth_region' => ['required', 'string', 'max:100'],
            'birth_zone' => ['required', 'string', 'max:100'],
            'birth_woreda' => ['required', 'string', 'max:100'],
            'birth_kebele' => ['required', 'string', 'max:100'],
            'residence_region' => ['required', 'string', 'max:100'],
            'residence_zone' => ['required', 'string', 'max:100'],
            'residence_woreda' => ['required', 'string', 'max:100'],
            'residence_kebele' => ['required', 'string', 'max:100'],
            'education_level' => ['required', 'string', Rule::in(config('prisoner_education_levels'))],
            'occupation' => ['required', 'string', 'max:255'],
            'ethnicity' => ['required', 'string', 'max:100'],
            'nationality' => ['required', 'string', 'max:100'],
            'religion' => ['required', 'string', 'max:100'],
            'marital_status' => ['required', 'string', Rule::in(['unmarried', 'married', 'divorced'])],
            'height' => ['required', 'string', 'max:50'],
            'hair_type' => ['required', 'string', Rule::in(config('prisoner_physical_options.hair_type'))],
            'appearance' => ['required', 'string', Rule::in(config('prisoner_physical_options.appearance'))],
            'forehead' => ['required', 'string', Rule::in(config('prisoner_physical_options.forehead'))],
            'nose' => ['required', 'string', Rule::in(config('prisoner_physical_options.nose'))],
            'eye_color' => ['required', 'string', Rule::in(config('prisoner_physical_options.eye_color'))],
            'teeth' => ['required', 'string', 'max:100'],
            'lips' => ['required', 'string', Rule::in(config('prisoner_physical_options.lips'))],
            'ears' => ['required', 'string', Rule::in(config('prisoner_physical_options.ears'))],
            'distinguishing_mark' => ['nullable', 'string', 'max:255'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_region' => ['required', 'string', 'max:100'],
            'emergency_zone' => ['required', 'string', 'max:100'],
            'emergency_woreda' => ['required', 'string', 'max:100'],
            'emergency_kebele' => ['required', 'string', 'max:100'],
            'emergency_phone_landline' => ['nullable', 'string', 'max:50'],
            'emergency_phone_mobile' => ['nullable', 'string', 'max:50'],
            'filled_by_professional_name' => ['required', 'string', 'max:255'],
            'signature_confirmed' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $fields = __('app.prisoners.fields');

        return is_array($fields) ? $fields : [];
    }
}
