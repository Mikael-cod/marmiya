<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateInmateFileRecordRequest extends StoreInmateFileRecordRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['inmate_intake_registration_id'] = [
            'required',
            'integer',
            Rule::exists('inmate_intake_registrations', 'id'),
            Rule::unique('inmate_file_records', 'inmate_intake_registration_id')
                ->ignore($this->route('inmate_file_record')),
        ];

        return $rules;
    }
}
