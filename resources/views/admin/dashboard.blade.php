<x-layouts.admin :title="__('app.admin.dashboard')">
    <section class="mb-8 overflow-hidden rounded-3xl border border-brand-border bg-brand-surface shadow-auth-card">
        <div class="relative px-6 py-8 sm:px-8 sm:py-10">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-brand-teal/5 via-transparent to-brand-blue/5"></div>
            <div class="relative">
                <span class="inline-flex items-center rounded-full bg-brand-teal/10 px-3 py-1 text-xs font-semibold text-brand-teal">
                    {{ auth()->user()->role->label() }}
                </span>
                <h1 class="mt-4 text-2xl font-bold text-brand-dark sm:text-3xl">
                    {{ __('app.admin.welcome') }}፣ {{ auth()->user()->name }}
                </h1>
                <p class="mt-3 max-w-2xl text-sm leading-relaxed text-brand-muted sm:text-base">
                    {{ __('app.admin.description') }}
                </p>
            </div>
        </div>
    </section>

    <section class="mb-8 grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.total_users') }}</p>
            <p class="mt-2 text-3xl font-bold text-brand-dark">{{ $totalUsers }}</p>
        </div>

        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.administrators') }}</p>
            <p class="mt-2 text-3xl font-bold text-brand-teal">{{ $adminCount }}</p>
        </div>

        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.standard_users') }}</p>
            <p class="mt-2 text-3xl font-bold text-brand-blue">{{ $userCount }}</p>
        </div>
    </section>

    <section class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        <a href="{{ route('admin.users') }}" class="card-surface shadow-auth-card transition hover:-translate-y-0.5 hover:border-brand-teal/30">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-teal/10 text-brand-teal">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-brand-dark">{{ __('app.admin.nav_users') }}</h2>
            <p class="mt-2 text-sm leading-relaxed text-brand-muted">{{ __('app.admin.users_description') }}</p>
        </a>

        <a href="{{ route('admin.database') }}" class="card-surface shadow-auth-card transition hover:-translate-y-0.5 hover:border-brand-blue/30">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-blue/10 text-brand-blue">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7z"/>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-brand-dark">{{ __('app.admin.nav_database') }}</h2>
            <p class="mt-2 text-sm leading-relaxed text-brand-muted">{{ __('app.admin.database_description') }}</p>
        </a>

        <a href="{{ route('admin.security') }}" class="card-surface shadow-auth-card transition hover:-translate-y-0.5 hover:border-brand-gold/30 md:col-span-2 xl:col-span-1">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-gold/15 text-brand-gold">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-brand-dark">{{ __('app.admin.nav_security') }}</h2>
            <p class="mt-2 text-sm leading-relaxed text-brand-muted">{{ __('app.admin.security_description') }}</p>
        </a>
    </section>

    <section class="card-surface shadow-auth-card mt-8">
        <h2 class="text-lg font-semibold text-brand-dark">{{ __('app.admin.access_title') }}</h2>
        <p class="mt-2 text-sm leading-relaxed text-brand-muted">
            {{ __('app.admin.access_description') }}
        </p>
    </section>
</x-layouts.admin>
