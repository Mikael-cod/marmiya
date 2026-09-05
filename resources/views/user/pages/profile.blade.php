<x-layouts.user :title="$title">
    @if (session('success'))
        <div class="alert-success mb-6">{{ session('success') }}</div>
    @endif

    <section class="overflow-hidden rounded-3xl border border-brand-border bg-brand-surface shadow-auth-card">
        <div class="relative px-6 py-8 sm:px-8 sm:py-10">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-brand-blue/5 via-transparent to-brand-teal/5"></div>
            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-brand-blue text-xl font-bold text-white shadow-lg shadow-brand-blue/20">
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-brand-dark sm:text-3xl">{{ $title }}</h1>
                        <p class="mt-1 text-sm text-brand-muted">{{ $description }}</p>
                    </div>
                </div>

                <div class="rounded-2xl border border-brand-border bg-brand-surface px-4 py-3 text-sm">
                    <p class="text-brand-muted">{{ __('app.profile.account_role') }}</p>
                    <p class="mt-1 font-semibold text-brand-dark">{{ $user->role->label() }}</p>
                </div>
            </div>
        </div>
    </section>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <section class="card-surface shadow-auth-card">
            <div class="border-b border-brand-border px-6 py-4">
                <h2 class="text-lg font-bold text-brand-dark">{{ __('app.profile.account_section') }}</h2>
                <p class="mt-1 text-sm text-brand-muted">{{ __('app.profile.account_section_hint') }}</p>
            </div>

            <form method="POST" action="{{ route('user.profile.update') }}" class="space-y-5 px-6 py-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="profile-name" class="intake-label">{{ __('app.profile.fields.name') }} <span class="text-red-500">*</span></label>
                    <input
                        id="profile-name"
                        name="name"
                        type="text"
                        value="{{ old('name', $user->name) }}"
                        required
                        autocomplete="name"
                        class="intake-input"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="profile-email" class="intake-label">{{ __('app.profile.fields.email') }} <span class="text-red-500">*</span></label>
                    <input
                        id="profile-email"
                        name="email"
                        type="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        autocomplete="email"
                        class="intake-input"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end border-t border-brand-border pt-4">
                    <button type="submit" class="btn-primary-brand">
                        {{ __('app.profile.save_account') }}
                    </button>
                </div>
            </form>
        </section>

        <section class="card-surface shadow-auth-card">
            <div class="border-b border-brand-border px-6 py-4">
                <h2 class="text-lg font-bold text-brand-dark">{{ __('app.profile.password_section') }}</h2>
                <p class="mt-1 text-sm text-brand-muted">{{ __('app.profile.password_section_hint') }}</p>
            </div>

            <form method="POST" action="{{ route('user.profile.password') }}" class="space-y-5 px-6 py-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="current-password" class="intake-label">{{ __('app.profile.fields.current_password') }} <span class="text-red-500">*</span></label>
                    <input
                        id="current-password"
                        name="current_password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="intake-input"
                    >
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="new-password" class="intake-label">{{ __('app.profile.fields.new_password') }} <span class="text-red-500">*</span></label>
                    <input
                        id="new-password"
                        name="password"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="intake-input"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password-confirmation" class="intake-label">{{ __('app.profile.fields.password_confirmation') }} <span class="text-red-500">*</span></label>
                    <input
                        id="password-confirmation"
                        name="password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="intake-input"
                    >
                </div>

                <div class="flex justify-end border-t border-brand-border pt-4">
                    <button type="submit" class="btn-primary-brand">
                        {{ __('app.profile.save_password') }}
                    </button>
                </div>
            </form>
        </section>
    </div>
</x-layouts.user>
