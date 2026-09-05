<x-layouts.user :title="$title">
    <div data-settings-page>
        @if (session('success'))
            <div class="alert-success mb-6">{{ session('success') }}</div>
        @endif

        <section class="overflow-hidden rounded-3xl border border-brand-border bg-brand-surface shadow-auth-card">
            <div class="relative px-6 py-8 sm:px-8 sm:py-10">
                <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-brand-blue/5 via-transparent to-brand-teal/5"></div>
                <div class="relative">
                    <h1 class="text-2xl font-bold text-brand-dark sm:text-3xl">{{ $title }}</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-relaxed text-brand-muted sm:text-base">{{ $description }}</p>
                </div>
            </div>
        </section>

        <div class="mt-6 grid gap-6 xl:grid-cols-2">
            <section class="card-surface shadow-auth-card">
                <div class="border-b border-brand-border px-6 py-4">
                    <h2 class="text-lg font-bold text-brand-dark">{{ __('app.settings.appearance_section') }}</h2>
                    <p class="mt-1 text-sm text-brand-muted">{{ __('app.settings.appearance_section_hint') }}</p>
                </div>

                <div class="space-y-6 px-6 py-6">
                    <div>
                        <p class="intake-label">{{ __('app.settings.fields.theme') }}</p>
                        <div class="mt-3 grid gap-3 sm:grid-cols-2">
                            <label class="settings-option-card">
                                <input type="radio" name="theme" value="light" data-theme-option class="settings-option-input">
                                <span class="settings-option-body">
                                    <span class="settings-option-title">{{ __('app.settings.theme_light') }}</span>
                                    <span class="settings-option-desc">{{ __('app.settings.theme_light_hint') }}</span>
                                </span>
                            </label>
                            <label class="settings-option-card">
                                <input type="radio" name="theme" value="dark" data-theme-option class="settings-option-input">
                                <span class="settings-option-body">
                                    <span class="settings-option-title">{{ __('app.settings.theme_dark') }}</span>
                                    <span class="settings-option-desc">{{ __('app.settings.theme_dark_hint') }}</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <p class="intake-label">{{ __('app.settings.fields.sidebar') }}</p>
                        <div class="mt-3 grid gap-3 sm:grid-cols-2">
                            <label class="settings-option-card">
                                <input type="radio" name="sidebar_collapsed" value="0" data-sidebar-collapsed-option class="settings-option-input">
                                <span class="settings-option-body">
                                    <span class="settings-option-title">{{ __('app.settings.sidebar_expanded') }}</span>
                                </span>
                            </label>
                            <label class="settings-option-card">
                                <input type="radio" name="sidebar_collapsed" value="1" data-sidebar-collapsed-option class="settings-option-input">
                                <span class="settings-option-body">
                                    <span class="settings-option-title">{{ __('app.settings.sidebar_collapsed') }}</span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </section>

            <section class="card-surface shadow-auth-card">
                <div class="border-b border-brand-border px-6 py-4">
                    <h2 class="text-lg font-bold text-brand-dark">{{ __('app.settings.system_section') }}</h2>
                    <p class="mt-1 text-sm text-brand-muted">{{ __('app.settings.system_section_hint') }}</p>
                </div>

                <dl class="divide-y divide-brand-border px-6 py-2">
                    <div class="grid gap-1 py-4 sm:grid-cols-[10rem_1fr]">
                        <dt class="text-sm font-medium text-brand-muted">{{ __('app.settings.fields.institute') }}</dt>
                        <dd class="text-sm font-semibold text-brand-dark">{{ __('app.institute') }}</dd>
                    </div>
                    <div class="grid gap-1 py-4 sm:grid-cols-[10rem_1fr]">
                        <dt class="text-sm font-medium text-brand-muted">{{ __('app.settings.fields.app_name') }}</dt>
                        <dd class="text-sm font-semibold text-brand-dark">{{ __('app.name') }}</dd>
                    </div>
                    <div class="grid gap-1 py-4 sm:grid-cols-[10rem_1fr]">
                        <dt class="text-sm font-medium text-brand-muted">{{ __('app.settings.fields.version') }}</dt>
                        <dd class="text-sm font-semibold text-brand-dark">{{ __('app.layout.version_number') }}</dd>
                    </div>
                    <div class="grid gap-1 py-4 sm:grid-cols-[10rem_1fr]">
                        <dt class="text-sm font-medium text-brand-muted">{{ __('app.settings.fields.timezone') }}</dt>
                        <dd class="text-sm font-semibold text-brand-dark">{{ $timezone }}</dd>
                    </div>
                    <div class="grid gap-1 py-4 sm:grid-cols-[10rem_1fr]">
                        <dt class="text-sm font-medium text-brand-muted">{{ __('app.settings.fields.signed_in_as') }}</dt>
                        <dd class="text-sm font-semibold text-brand-dark">{{ $user->name }} ({{ $user->role->label() }})</dd>
                    </div>
                </dl>

                <div class="border-t border-brand-border px-6 py-4">
                    <a href="{{ route('user.profile') }}" class="text-sm font-semibold text-brand-blue transition hover:text-brand-teal">
                        {{ __('app.settings.manage_profile') }} →
                    </a>
                </div>
            </section>
        </div>

        <form method="POST" action="{{ route('user.settings.update') }}" class="mt-6 card-surface shadow-auth-card">
            @csrf
            @method('PUT')

            <div class="border-b border-brand-border px-6 py-4">
                <h2 class="text-lg font-bold text-brand-dark">{{ __('app.settings.workspace_section') }}</h2>
                <p class="mt-1 text-sm text-brand-muted">{{ __('app.settings.workspace_section_hint') }}</p>
            </div>

            <div class="grid gap-6 px-6 py-6 lg:grid-cols-3">
                <div>
                    <label for="per_page" class="intake-label">{{ __('app.settings.fields.per_page') }} <span class="text-red-500">*</span></label>
                    <select id="per_page" name="per_page" required class="intake-input mt-2">
                        @foreach ($perPageOptions as $option)
                            <option value="{{ $option }}" @selected((int) old('per_page', $perPage) === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-brand-muted">{{ __('app.settings.per_page_hint') }}</p>
                    @error('per_page')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="report_eth_year" class="intake-label">{{ __('app.settings.fields.report_eth_year') }}</label>
                    <select id="report_eth_year" name="report_eth_year" class="intake-input mt-2">
                        <option value="" @selected(blank(old('report_eth_year', $reportEthYear)))>{{ __('app.settings.report_default_current') }}</option>
                        @foreach ($ethYears as $year)
                            <option value="{{ $year }}" @selected((string) old('report_eth_year', $reportEthYear) === (string) $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                    @error('report_eth_year')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="report_eth_month" class="intake-label">{{ __('app.settings.fields.report_eth_month') }}</label>
                    <select id="report_eth_month" name="report_eth_month" class="intake-input mt-2">
                        <option value="" @selected(blank(old('report_eth_month', $reportEthMonth)))>{{ __('app.settings.report_default_current') }}</option>
                        @foreach ($ethMonths as $monthNumber => $monthName)
                            <option value="{{ $monthNumber }}" @selected((string) old('report_eth_month', $reportEthMonth) === (string) $monthNumber)>{{ $monthName }}</option>
                        @endforeach
                    </select>
                    @error('report_eth_month')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end border-t border-brand-border px-6 py-4">
                <button type="submit" class="btn-primary-brand">{{ __('app.settings.save') }}</button>
            </div>
        </form>
    </div>
</x-layouts.user>
