@php
    $value = fn (string $key) => old($key, $settings[$key] ?? '');
@endphp

<x-layouts.admin :title="$title">
    @if (session('success'))
        <div class="alert-success mb-6">{{ session('success') }}</div>
    @endif

    <section class="overflow-hidden rounded-3xl border border-brand-border bg-brand-surface shadow-auth-card">
        <div class="relative px-6 py-8 sm:px-8 sm:py-10">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-brand-teal/5 via-transparent to-brand-blue/5"></div>
            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <div class="min-w-0">
                    <h1 class="text-2xl font-bold text-brand-dark sm:text-3xl">{{ $title }}</h1>
                    <p class="mt-3 max-w-3xl text-sm leading-relaxed text-brand-muted sm:text-base">{{ $description }}</p>
                </div>

                <a href="{{ route('login') }}" target="_blank" rel="noopener" class="btn-secondary-brand intake-register-btn">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{ __('app.admin.front_pages.preview_login') }}
                </a>
            </div>
        </div>
    </section>

    <form method="POST" action="{{ route('admin.front-pages.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('PUT')

        <section class="card-surface shadow-auth-card">
            <div class="border-b border-brand-border px-6 py-4">
                <h2 class="text-lg font-bold text-brand-dark">{{ __('app.admin.front_pages.sections.branding') }}</h2>
                <p class="mt-1 text-sm text-brand-muted">{{ __('app.admin.front_pages.sections.branding_hint') }}</p>
            </div>

            <div class="grid gap-5 px-6 py-6 lg:grid-cols-2">
                <div class="lg:col-span-2">
                    <label for="app_name" class="intake-label">{{ __('app.admin.front_pages.fields.app_name') }} <span class="text-red-500">*</span></label>
                    <input id="app_name" name="app_name" type="text" value="{{ $value('app_name') }}" required class="intake-input">
                    @error('app_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="institute" class="intake-label">{{ __('app.admin.front_pages.fields.institute') }} <span class="text-red-500">*</span></label>
                    <input id="institute" name="institute" type="text" value="{{ $value('institute') }}" required class="intake-input">
                    @error('institute')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="subtitle" class="intake-label">{{ __('app.admin.front_pages.fields.subtitle') }} <span class="text-red-500">*</span></label>
                    <input id="subtitle" name="subtitle" type="text" value="{{ $value('subtitle') }}" required class="intake-input">
                    @error('subtitle')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="lg:col-span-2">
                    <label for="copyright" class="intake-label">{{ __('app.admin.front_pages.fields.copyright') }} <span class="text-red-500">*</span></label>
                    <input id="copyright" name="copyright" type="text" value="{{ $value('copyright') }}" required class="intake-input">
                    @error('copyright')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="card-surface shadow-auth-card">
            <div class="border-b border-brand-border px-6 py-4">
                <h2 class="text-lg font-bold text-brand-dark">{{ __('app.admin.front_pages.sections.login') }}</h2>
                <p class="mt-1 text-sm text-brand-muted">{{ __('app.admin.front_pages.sections.login_hint') }}</p>
            </div>

            <div class="space-y-5 px-6 py-6">
                <div>
                    <label for="login_description" class="intake-label">{{ __('app.admin.front_pages.fields.login_description') }} <span class="text-red-500">*</span></label>
                    <textarea id="login_description" name="login_description" rows="3" required class="intake-input">{{ $value('login_description') }}</textarea>
                    @error('login_description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid gap-5 lg:grid-cols-2">
                    <div>
                        <label for="secure_platform" class="intake-label">{{ __('app.admin.front_pages.fields.secure_platform') }} <span class="text-red-500">*</span></label>
                        <input id="secure_platform" name="secure_platform" type="text" value="{{ $value('secure_platform') }}" required class="intake-input">
                        @error('secure_platform')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-end">
                        <label class="intake-checkbox-label w-full">
                            <input type="hidden" name="show_secure_badge" value="0">
                            <input type="checkbox" name="show_secure_badge" value="1" class="intake-checkbox" @checked(old('show_secure_badge', $settings['show_secure_badge'] ?? true))>
                            <span>{{ __('app.admin.front_pages.fields.show_secure_badge') }}</span>
                        </label>
                    </div>

                    <div>
                        <label for="welcome_back" class="intake-label">{{ __('app.admin.front_pages.fields.welcome_back') }} <span class="text-red-500">*</span></label>
                        <input id="welcome_back" name="welcome_back" type="text" value="{{ $value('welcome_back') }}" required class="intake-input">
                        @error('welcome_back')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="enter_credentials" class="intake-label">{{ __('app.admin.front_pages.fields.enter_credentials') }} <span class="text-red-500">*</span></label>
                        <input id="enter_credentials" name="enter_credentials" type="text" value="{{ $value('enter_credentials') }}" required class="intake-input">
                        @error('enter_credentials')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </section>

        <section class="card-surface shadow-auth-card">
            <div class="border-b border-brand-border px-6 py-4">
                <h2 class="text-lg font-bold text-brand-dark">{{ __('app.admin.front_pages.sections.links') }}</h2>
                <p class="mt-1 text-sm text-brand-muted">{{ __('app.admin.front_pages.sections.links_hint') }}</p>
            </div>

            <div class="grid gap-5 px-6 py-6 lg:grid-cols-2">
                <div>
                    <label for="contact_support" class="intake-label">{{ __('app.admin.front_pages.fields.contact_support') }} <span class="text-red-500">*</span></label>
                    <input id="contact_support" name="contact_support" type="text" value="{{ $value('contact_support') }}" required class="intake-input">
                    @error('contact_support')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="contact_support_url" class="intake-label">{{ __('app.admin.front_pages.fields.contact_support_url') }}</label>
                    <input id="contact_support_url" name="contact_support_url" type="url" value="{{ $value('contact_support_url') }}" placeholder="https://..." class="intake-input">
                    @error('contact_support_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="lg:col-span-2">
                    <label for="contact_administrator_url" class="intake-label">{{ __('app.admin.front_pages.fields.contact_administrator_url') }}</label>
                    <input id="contact_administrator_url" name="contact_administrator_url" type="url" value="{{ $value('contact_administrator_url') }}" placeholder="https://..." class="intake-input">
                    @error('contact_administrator_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="lg:col-span-2">
                    <label for="help_center_url" class="intake-label">{{ __('app.admin.front_pages.fields.help_center_url') }}</label>
                    <input id="help_center_url" name="help_center_url" type="url" value="{{ $value('help_center_url') }}" placeholder="https://..." class="intake-input">
                    @error('help_center_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="card-surface shadow-auth-card">
            <div class="border-b border-brand-border px-6 py-4">
                <h2 class="text-lg font-bold text-brand-dark">{{ __('app.admin.front_pages.sections.appearance') }}</h2>
                <p class="mt-1 text-sm text-brand-muted">{{ __('app.admin.front_pages.sections.appearance_hint') }}</p>
            </div>

            <div class="px-6 py-6">
                <label for="default_theme" class="intake-label">{{ __('app.admin.front_pages.fields.default_theme') }} <span class="text-red-500">*</span></label>
                <select id="default_theme" name="default_theme" required class="intake-input max-w-sm">
                    @foreach ($themeOptions as $theme)
                        <option value="{{ $theme }}" @selected($value('default_theme') === $theme)>
                            {{ __('app.admin.front_pages.theme_'.$theme) }}
                        </option>
                    @endforeach
                </select>
                @error('default_theme')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </section>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary-brand intake-register-btn">
                {{ __('app.admin.front_pages.save') }}
            </button>
        </div>
    </form>
</x-layouts.admin>
