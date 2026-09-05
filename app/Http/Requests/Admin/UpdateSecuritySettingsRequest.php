<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSecuritySettingsRequest extends FormRequest
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
        return [
            'password_min_length' => ['required', 'integer', 'min:6', 'max:64'],
            'password_require_letters' => ['sometimes', 'boolean'],
            'password_require_mixed_case' => ['sometimes', 'boolean'],
            'password_require_numbers' => ['sometimes', 'boolean'],
            'password_require_symbols' => ['sometimes', 'boolean'],
            'login_max_attempts' => ['required', 'integer', 'min:3', 'max:20'],
            'login_lockout_minutes' => ['required', 'integer', 'min:1', 'max:120'],
            'session_lifetime_minutes' => ['required', 'integer', 'min:15', 'max:1440'],
            'expire_session_on_close' => ['sometimes', 'boolean'],
            'force_https' => ['sometimes', 'boolean'],
            'security_contact_email' => ['nullable', 'email', 'max:255'],
            'security_guidelines' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'password_require_letters' => $this->boolean('password_require_letters'),
            'password_require_mixed_case' => $this->boolean('password_require_mixed_case'),
            'password_require_numbers' => $this->boolean('password_require_numbers'),
            'password_require_symbols' => $this->boolean('password_require_symbols'),
            'expire_session_on_close' => $this->boolean('expire_session_on_close'),
            'force_https' => $this->boolean('force_https'),
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'password_min_length' => __('app.admin.security.fields.password_min_length'),
            'password_require_letters' => __('app.admin.security.fields.password_require_letters'),
            'password_require_mixed_case' => __('app.admin.security.fields.password_require_mixed_case'),
            'password_require_numbers' => __('app.admin.security.fields.password_require_numbers'),
            'password_require_symbols' => __('app.admin.security.fields.password_require_symbols'),
            'login_max_attempts' => __('app.admin.security.fields.login_max_attempts'),
            'login_lockout_minutes' => __('app.admin.security.fields.login_lockout_minutes'),
            'session_lifetime_minutes' => __('app.admin.security.fields.session_lifetime_minutes'),
            'expire_session_on_close' => __('app.admin.security.fields.expire_session_on_close'),
            'force_https' => __('app.admin.security.fields.force_https'),
            'security_contact_email' => __('app.admin.security.fields.security_contact_email'),
            'security_guidelines' => __('app.admin.security.fields.security_guidelines'),
        ];
    }
}
