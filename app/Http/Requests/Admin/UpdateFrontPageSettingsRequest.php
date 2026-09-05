<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFrontPageSettingsRequest extends FormRequest
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
            'app_name' => ['required', 'string', 'max:255'],
            'institute' => ['required', 'string', 'max:255'],
            'subtitle' => ['required', 'string', 'max:255'],
            'login_description' => ['required', 'string', 'max:1000'],
            'secure_platform' => ['required', 'string', 'max:255'],
            'welcome_back' => ['required', 'string', 'max:255'],
            'enter_credentials' => ['required', 'string', 'max:255'],
            'contact_support' => ['required', 'string', 'max:255'],
            'contact_support_url' => ['nullable', 'url', 'max:255'],
            'contact_administrator_url' => ['nullable', 'url', 'max:255'],
            'help_center_url' => ['nullable', 'url', 'max:255'],
            'copyright' => ['required', 'string', 'max:255'],
            'show_secure_badge' => ['sometimes', 'boolean'],
            'default_theme' => ['required', Rule::in(config('front_pages.default_theme_options', ['light', 'dark', 'system']))],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'show_secure_badge' => $this->boolean('show_secure_badge'),
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'app_name' => __('app.admin.front_pages.fields.app_name'),
            'institute' => __('app.admin.front_pages.fields.institute'),
            'subtitle' => __('app.admin.front_pages.fields.subtitle'),
            'login_description' => __('app.admin.front_pages.fields.login_description'),
            'secure_platform' => __('app.admin.front_pages.fields.secure_platform'),
            'welcome_back' => __('app.admin.front_pages.fields.welcome_back'),
            'enter_credentials' => __('app.admin.front_pages.fields.enter_credentials'),
            'contact_support' => __('app.admin.front_pages.fields.contact_support'),
            'contact_support_url' => __('app.admin.front_pages.fields.contact_support_url'),
            'contact_administrator_url' => __('app.admin.front_pages.fields.contact_administrator_url'),
            'help_center_url' => __('app.admin.front_pages.fields.help_center_url'),
            'copyright' => __('app.admin.front_pages.fields.copyright'),
            'show_secure_badge' => __('app.admin.front_pages.fields.show_secure_badge'),
            'default_theme' => __('app.admin.front_pages.fields.default_theme'),
        ];
    }
}
