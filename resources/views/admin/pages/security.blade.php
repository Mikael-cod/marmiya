@php
    $value = fn (string $key) => old($key, $settings[$key] ?? '');
    $checked = fn (string $key) => (bool) old($key, $settings[$key] ?? false);
    $users = $overview['users'];
    $environment = $overview['environment'];
    $session = $overview['session'];
    $recentFailed = $overview['recent_failed_logins'];
@endphp

<x-layouts.admin :title="$title">
    @if (session('success'))
        <div class="alert-success mb-6">{{ session('success') }}</div>
    @endif

    <section class="overflow-hidden rounded-3xl border border-brand-border bg-brand-surface shadow-auth-card">
        <div class="relative px-6 py-8 sm:px-8 sm:py-10">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-brand-gold/5 via-transparent to-brand-teal/5"></div>
            <div class="relative">
                <h1 class="text-2xl font-bold text-brand-dark sm:text-3xl">{{ $title }}</h1>
                <p class="mt-3 max-w-3xl text-sm leading-relaxed text-brand-muted sm:text-base">{{ $description }}</p>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.security.info_failed_24h') }}</p>
            <p class="mt-2 text-lg font-bold {{ ($overview['failed_last_24h'] ?? 0) > 0 ? 'text-amber-600' : 'text-brand-dark' }}">
                {{ $overview['failed_last_24h'] ?? 0 }}
            </p>
        </div>

        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.security.info_admins') }}</p>
            <p class="mt-2 text-lg font-bold text-brand-dark">{{ $users['admins'] ?? 0 }}</p>
        </div>

        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.security.info_debug') }}</p>
            <p class="mt-2 text-lg font-bold {{ $environment['debug'] ? 'text-amber-600' : 'text-emerald-600' }}">
                {{ $environment['debug'] ? __('app.admin.security.debug_on') : __('app.admin.security.debug_off') }}
            </p>
        </div>

        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.security.info_https') }}</p>
            <p class="mt-2 text-lg font-bold {{ $environment['https'] ? 'text-emerald-600' : 'text-amber-600' }}">
                {{ $environment['https'] ? __('app.admin.security.https_on') : __('app.admin.security.https_off') }}
            </p>
        </div>
    </section>

    <section class="card-surface mt-6 shadow-auth-card">
        <div class="border-b border-brand-border px-6 py-4">
            <h2 class="text-lg font-bold text-brand-dark">{{ __('app.admin.security.policy_title') }}</h2>
            <p class="mt-1 text-sm text-brand-muted">{{ __('app.admin.security.policy_subtitle') }}</p>
        </div>
        <ul class="flex flex-wrap gap-2 px-6 py-5">
            @foreach ($overview['password_policy'] as $item)
                <li class="rounded-full bg-brand-teal/10 px-3 py-1 text-xs font-semibold text-brand-teal">{{ $item }}</li>
            @endforeach
        </ul>
    </section>

    <form method="POST" action="{{ route('admin.security.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('PUT')

        <section class="card-surface shadow-auth-card">
            <div class="border-b border-brand-border px-6 py-4">
                <h2 class="text-lg font-bold text-brand-dark">{{ __('app.admin.security.sections.password') }}</h2>
                <p class="mt-1 text-sm text-brand-muted">{{ __('app.admin.security.sections.password_hint') }}</p>
            </div>

            <div class="grid gap-5 px-6 py-6 lg:grid-cols-2">
                <div>
                    <label for="password_min_length" class="intake-label">{{ __('app.admin.security.fields.password_min_length') }} <span class="text-red-500">*</span></label>
                    <input id="password_min_length" name="password_min_length" type="number" min="6" max="64" value="{{ $value('password_min_length') }}" required class="intake-input">
                    @error('password_min_length')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:col-span-1 lg:grid-cols-1">
                    @foreach ([
                        'password_require_letters' => __('app.admin.security.fields.password_require_letters'),
                        'password_require_mixed_case' => __('app.admin.security.fields.password_require_mixed_case'),
                        'password_require_numbers' => __('app.admin.security.fields.password_require_numbers'),
                        'password_require_symbols' => __('app.admin.security.fields.password_require_symbols'),
                    ] as $field => $label)
                        <label class="intake-checkbox-label">
                            <input type="hidden" name="{{ $field }}" value="0">
                            <input type="checkbox" name="{{ $field }}" value="1" class="intake-checkbox" @checked($checked($field))>
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="card-surface shadow-auth-card">
            <div class="border-b border-brand-border px-6 py-4">
                <h2 class="text-lg font-bold text-brand-dark">{{ __('app.admin.security.sections.login') }}</h2>
                <p class="mt-1 text-sm text-brand-muted">{{ __('app.admin.security.sections.login_hint') }}</p>
            </div>

            <div class="grid gap-5 px-6 py-6 lg:grid-cols-2">
                <div>
                    <label for="login_max_attempts" class="intake-label">{{ __('app.admin.security.fields.login_max_attempts') }} <span class="text-red-500">*</span></label>
                    <input id="login_max_attempts" name="login_max_attempts" type="number" min="3" max="20" value="{{ $value('login_max_attempts') }}" required class="intake-input">
                    @error('login_max_attempts')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="login_lockout_minutes" class="intake-label">{{ __('app.admin.security.fields.login_lockout_minutes') }} <span class="text-red-500">*</span></label>
                    <input id="login_lockout_minutes" name="login_lockout_minutes" type="number" min="1" max="120" value="{{ $value('login_lockout_minutes') }}" required class="intake-input">
                    @error('login_lockout_minutes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="card-surface shadow-auth-card">
            <div class="border-b border-brand-border px-6 py-4">
                <h2 class="text-lg font-bold text-brand-dark">{{ __('app.admin.security.sections.session') }}</h2>
                <p class="mt-1 text-sm text-brand-muted">{{ __('app.admin.security.sections.session_hint') }}</p>
            </div>

            <div class="grid gap-5 px-6 py-6 lg:grid-cols-2">
                <div>
                    <label for="session_lifetime_minutes" class="intake-label">{{ __('app.admin.security.fields.session_lifetime_minutes') }} <span class="text-red-500">*</span></label>
                    <input id="session_lifetime_minutes" name="session_lifetime_minutes" type="number" min="15" max="1440" value="{{ $value('session_lifetime_minutes') }}" required class="intake-input">
                    @error('session_lifetime_minutes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-end">
                    <label class="intake-checkbox-label w-full">
                        <input type="hidden" name="expire_session_on_close" value="0">
                        <input type="checkbox" name="expire_session_on_close" value="1" class="intake-checkbox" @checked($checked('expire_session_on_close'))>
                        <span>{{ __('app.admin.security.fields.expire_session_on_close') }}</span>
                    </label>
                </div>
            </div>
        </section>

        <section class="card-surface shadow-auth-card">
            <div class="border-b border-brand-border px-6 py-4">
                <h2 class="text-lg font-bold text-brand-dark">{{ __('app.admin.security.sections.network') }}</h2>
                <p class="mt-1 text-sm text-brand-muted">{{ __('app.admin.security.sections.network_hint') }}</p>
            </div>

            <div class="space-y-5 px-6 py-6">
                <label class="intake-checkbox-label">
                    <input type="hidden" name="force_https" value="0">
                    <input type="checkbox" name="force_https" value="1" class="intake-checkbox" @checked($checked('force_https'))>
                    <span>{{ __('app.admin.security.fields.force_https') }}</span>
                </label>

                <div>
                    <label for="security_contact_email" class="intake-label">{{ __('app.admin.security.fields.security_contact_email') }}</label>
                    <input id="security_contact_email" name="security_contact_email" type="email" value="{{ $value('security_contact_email') }}" class="intake-input" placeholder="security@institute.edu.et">
                    @error('security_contact_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="security_guidelines" class="intake-label">{{ __('app.admin.security.fields.security_guidelines') }}</label>
                    <textarea id="security_guidelines" name="security_guidelines" rows="4" class="intake-input">{{ $value('security_guidelines') }}</textarea>
                    @error('security_guidelines')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary-brand intake-register-btn">
                {{ __('app.admin.security.save') }}
            </button>
        </div>
    </form>

    <section class="card-surface mt-6 overflow-hidden p-0 shadow-auth-card">
        <div class="border-b border-brand-border px-6 py-4">
            <h2 class="text-lg font-bold text-brand-dark">{{ __('app.admin.security.recent_failed_title') }}</h2>
            <p class="mt-1 text-sm text-brand-muted">{{ __('app.admin.security.recent_failed_subtitle') }}</p>
        </div>

        @if ($recentFailed->isEmpty())
            <p class="px-6 py-8 text-sm text-brand-muted">{{ __('app.admin.security.no_failed_logins') }}</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-brand-border bg-brand-surface-alt/60">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-brand-muted">{{ __('app.admin.security.columns.email') }}</th>
                            <th class="px-6 py-3 text-left font-semibold text-brand-muted">{{ __('app.admin.security.columns.ip_address') }}</th>
                            <th class="px-6 py-3 text-right font-semibold text-brand-muted">{{ __('app.admin.security.columns.attempted_at') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-border">
                        @foreach ($recentFailed as $attempt)
                            <tr>
                                <td class="px-6 py-3 font-medium text-brand-dark">{{ $attempt->email }}</td>
                                <td class="px-6 py-3 font-mono text-xs text-brand-muted">{{ $attempt->ip_address }}</td>
                                <td class="px-6 py-3 text-right text-brand-muted">{{ $attempt->attempted_at?->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-layouts.admin>
