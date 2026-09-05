<x-layouts.user :title="__('app.user.dashboard')">
    {{-- Page header banner --}}
    <section class="mb-8 overflow-hidden rounded-3xl border border-brand-border bg-brand-surface shadow-auth-card">
        <div class="relative px-6 py-8 sm:px-8 sm:py-10">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-brand-blue/5 via-transparent to-brand-teal/5"></div>
            <div class="relative">
                <span class="inline-flex items-center rounded-full bg-brand-blue/10 px-3 py-1 text-xs font-semibold text-brand-blue">
                    {{ auth()->user()->role->label() }}
                </span>
                <h1 class="mt-4 text-2xl font-bold text-brand-dark sm:text-3xl">
                    {{ __('app.user.welcome') }}፣ {{ auth()->user()->name }}
                </h1>
                <p class="mt-3 max-w-2xl text-sm leading-relaxed text-brand-muted sm:text-base">
                    {{ __('app.user.description') }}
                </p>
            </div>
        </div>
    </section>

    {{-- Quick access cards --}}
    <section class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        <a href="{{ route('user.income') }}" class="card-surface shadow-auth-card transition hover:-translate-y-0.5 hover:border-brand-teal/30">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-teal/10 text-brand-teal">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-brand-dark">{{ __('app.user.stat_income') }}</h2>
            <p class="mt-2 text-sm leading-relaxed text-brand-muted">{{ __('app.user.stat_income_description') }}</p>
        </a>

        <a href="{{ route('user.expense') }}" class="card-surface shadow-auth-card transition hover:-translate-y-0.5 hover:border-brand-blue/30">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-blue/10 text-brand-blue">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-brand-dark">{{ __('app.user.stat_expense') }}</h2>
            <p class="mt-2 text-sm leading-relaxed text-brand-muted">{{ __('app.user.stat_expense_description') }}</p>
        </a>

        <a href="{{ route('user.prisoners') }}" class="card-surface shadow-auth-card transition hover:-translate-y-0.5 hover:border-brand-gold/30 md:col-span-2 xl:col-span-1">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-gold/15 text-brand-gold">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-brand-dark">{{ __('app.user.stat_prisoners') }}</h2>
            <p class="mt-2 text-sm leading-relaxed text-brand-muted">{{ __('app.user.stat_prisoners_description') }}</p>
        </a>
    </section>
</x-layouts.user>
