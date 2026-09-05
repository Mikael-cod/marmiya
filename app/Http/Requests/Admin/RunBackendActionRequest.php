<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RunBackendActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $action = (string) $this->input('action');
        $requiresConfirmation = (bool) data_get(config("backend_management.actions.{$action}"), 'requires_confirmation', false);

        return [
            'action' => ['required', 'string', Rule::in(array_keys(config('backend_management.actions', [])))],
            'confirm_action' => [$requiresConfirmation ? 'accepted' : 'nullable'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'action' => __('app.admin.backend.fields.action'),
            'confirm_action' => __('app.admin.backend.fields.confirm_action'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'confirm_action.accepted' => __('app.admin.backend.confirm_required'),
        ];
    }
}
