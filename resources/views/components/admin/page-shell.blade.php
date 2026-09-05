<x-layouts.admin :title="$title">
    <section class="overflow-hidden rounded-3xl border border-brand-border bg-brand-surface shadow-auth-card">
        <div class="relative px-6 py-8 sm:px-8 sm:py-10">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-brand-teal/5 via-transparent to-brand-blue/5"></div>
            <div class="relative">
                <h1 class="text-2xl font-bold text-brand-dark sm:text-3xl">{{ $title }}</h1>
                <p class="mt-3 max-w-3xl text-sm leading-relaxed text-brand-muted sm:text-base">
                    {{ $description }}
                </p>
            </div>
        </div>
    </section>

    <section class="mt-6 card-surface shadow-auth-card">
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-teal/10 text-brand-teal">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-brand-dark">{{ __('app.admin.coming_soon') }}</p>
            <p class="mt-2 max-w-md text-sm text-brand-muted">{{ __('app.admin.coming_soon_hint') }}</p>
        </div>
    </section>
</x-layouts.admin>
