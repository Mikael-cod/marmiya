<?php

namespace App\Http\Requests;

class UpdateInmateIntakeRegistrationRequest extends StoreInmateIntakeRegistrationRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['photo'] = ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'];

        return $rules;
    }
}
