<x-layouts.guest>
    <header class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-6 lg:px-8">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-blue text-white shadow-lg shadow-brand-blue/25">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 11h8M8 15h5"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-brand-muted">{{ front_setting('institute') }}</p>
                <p class="text-sm font-semibold text-brand-dark">{{ front_setting('subtitle') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <x-theme-toggle />

            <a href="{{ front_url('contact_support_url') }}" class="btn-secondary-brand hidden sm:inline-flex">
                <svg class="mr-2 h-4 w-4 text-brand-blue" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                {{ front_setting('contact_support') }}
            </a>
        </div>
    </header>

    <main class="mx-auto flex w-full max-w-6xl flex-1 flex-col items-center justify-center px-6 pb-12 pt-4 lg:px-8">
        <div class="mb-8 max-w-3xl text-center">
            @if (front_setting('show_secure_badge'))
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-brand-blue/15 bg-brand-blue/5 px-4 py-2 text-sm font-medium text-brand-blue">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ front_setting('secure_platform') }}
                </div>
            @endif

            <h1 class="text-3xl font-bold leading-tight sm:text-4xl lg:text-5xl">
                <span class="text-gradient-brand">{{ front_setting('institute') }}</span>
                <span class="mt-2 block text-brand-dark">{{ front_setting('subtitle') }}</span>
            </h1>

            <p class="mx-auto mt-4 max-w-xl text-base leading-relaxed text-brand-muted">
                {{ front_setting('login_description') }}
            </p>
        </div>

        <div class="w-full max-w-md">
            <div class="shadow-auth-card rounded-3xl border border-brand-border bg-brand-surface/95 p-8 backdrop-blur-sm sm:p-10">
                @php
                    $frontendMaintenance = ($frontendMaintenance ?? false) || session('frontend_maintenance');
                @endphp

                @if ($frontendMaintenance)
                    <div class="mb-6 rounded-2xl border border-amber-200/70 bg-amber-50/80 px-4 py-4 text-sm leading-relaxed text-amber-900 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-100">
                        <p class="font-semibold">{{ __('app.admin.backend.maintenance_login_title') }}</p>
                        <p class="mt-1">{{ __('app.admin.backend.maintenance_login_body') }}</p>
                    </div>
                @endif

                <div class="mb-8 text-center">
                    <h2 class="text-xl font-semibold text-brand-dark">{{ front_setting('welcome_back') }}</h2>
                    <p class="mt-2 text-sm text-brand-muted">{{ front_setting('enter_credentials') }}</p>
                </div>

                @if ($errors->any())
                    <div class="alert-error mb-6">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-brand-dark">{{ __('app.email') }}</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            placeholder="{{ __('app.email_placeholder') }}"
                            class="input-auth"
                        >
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-brand-dark">{{ __('app.password') }}</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            placeholder="{{ __('app.password_placeholder') }}"
                            class="input-auth"
                        >
                    </div>

                    <div class="flex items-center gap-3">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            class="h-4 w-4 rounded border-brand-border text-brand-blue focus:ring-brand-blue/20"
                        >
                        <label for="remember" class="text-sm text-brand-muted">{{ __('app.remember_me') }}</label>
                    </div>

                    <button type="submit" class="btn-primary-brand group">
                        {{ __('app.sign_in') }}
                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </button>
                </form>

                <div class="mt-8 border-t border-brand-border pt-6 text-center">
                    <p class="text-sm text-brand-muted">
                        {{ __('app.need_access') }}
                        <a href="{{ front_url('contact_administrator_url') }}" class="font-semibold text-brand-blue transition hover:text-brand-teal">{{ __('app.contact_administrator') }}</a>
                    </p>
                </div>
            </div>

            <p class="mt-8 text-center text-xs text-brand-muted">
                <x-eth.clock class="mb-2 block" />
                &copy; {{ eth_year() }} {{ front_setting('institute') }}. {{ front_setting('copyright') }}
            </p>
        </div>
    </main>
</x-layouts.guest>
