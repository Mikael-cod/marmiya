<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInmateFilePageRequest extends FormRequest
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
            'pages' => ['required', 'array', 'min:1'],
            'pages.*' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'pages' => __('app.prisoners.documents_files'),
            'pages.*' => __('app.prisoners.documents_page'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'pages.required' => __('app.prisoners.documents_files_required'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        /** @var \App\Models\InmateFileRecord $record */
        $record = $this->route('inmate_file_record');

        return route('user.prisoners', array_merge(
            $this->only(['q', 'gender', 'from', 'to', 'per_page', 'page']),
            ['documents' => $record->id],
        ));
    }
}
