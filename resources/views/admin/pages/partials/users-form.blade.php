@php
    $managedUser = $editingUser ?? null;
    $isEdit = filled($managedUser);

    $fieldValue = function (string $field) use ($managedUser) {
        $value = old($field, $managedUser?->{$field});

        if ($value instanceof \App\Enums\UserRole) {
            return $value->value;
        }

        return $value;
    };
@endphp

<form
    action="{{ $isEdit ? route('admin.users.update', $managedUser) : route('admin.users.store') }}"
    method="POST"
    id="admin-user-form"
    class="intake-form-body space-y-5"
>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    @foreach (request()->only(['q', 'role', 'per_page', 'page']) as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach

    <div>
        <label for="admin-user-name" class="intake-label">
            {{ __('app.admin.users.fields.name') }}
            <span class="text-red-500">*</span>
        </label>
        <input
            id="admin-user-name"
            name="name"
            type="text"
            value="{{ $fieldValue('name') }}"
            required
            autocomplete="name"
            class="intake-input"
        >
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="admin-user-email" class="intake-label">
            {{ __('app.admin.users.fields.email') }}
            <span class="text-red-500">*</span>
        </label>
        <input
            id="admin-user-email"
            name="email"
            type="email"
            value="{{ $fieldValue('email') }}"
            required
            autocomplete="email"
            class="intake-input"
        >
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="admin-user-role" class="intake-label">
            {{ __('app.admin.users.fields.role') }}
            <span class="text-red-500">*</span>
        </label>
        <select id="admin-user-role" name="role" required class="intake-input">
            <option value="">{{ __('app.admin.users.select_role') }}</option>
            @foreach (\App\Enums\UserRole::cases() as $roleOption)
                <option value="{{ $roleOption->value }}" @selected($fieldValue('role') === $roleOption->value)>
                    {{ $roleOption->label() }}
                </option>
            @endforeach
        </select>
        @error('role')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="rounded-2xl border border-brand-border bg-brand-surface p-4">
        <h3 class="text-sm font-bold text-brand-dark">
            {{ $isEdit ? __('app.admin.users.password_section_edit') : __('app.admin.users.password_section_create') }}
        </h3>
        <p class="mt-1 text-xs leading-relaxed text-brand-muted">
            {{ $isEdit ? __('app.admin.users.password_section_edit_hint') : __('app.admin.users.password_section_create_hint') }}
        </p>

        <div class="mt-4 space-y-4">
            <div>
                <label for="admin-user-password" class="intake-label">
                    {{ __('app.admin.users.fields.password') }}
                    @unless($isEdit)
                        <span class="text-red-500">*</span>
                    @endunless
                </label>
                <input
                    id="admin-user-password"
                    name="password"
                    type="password"
                    @unless($isEdit) required @endunless
                    autocomplete="{{ $isEdit ? 'new-password' : 'new-password' }}"
                    class="intake-input"
                >
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="admin-user-password-confirmation" class="intake-label">
                    {{ __('app.admin.users.fields.password_confirmation') }}
                    @unless($isEdit)
                        <span class="text-red-500">*</span>
                    @endunless
                </label>
                <input
                    id="admin-user-password-confirmation"
                    name="password_confirmation"
                    type="password"
                    @unless($isEdit) required @endunless
                    autocomplete="new-password"
                    class="intake-input"
                >
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3 border-t border-brand-border pt-4">
        <a href="{{ route('admin.users', request()->only(['q', 'role', 'per_page', 'page'])) }}" data-intake-modal-close class="btn-secondary-brand">
            {{ __('app.admin.users.close') }}
        </a>
        <button type="submit" class="btn-primary-brand intake-register-btn">
            {{ $isEdit ? __('app.admin.users.update') : __('app.admin.users.submit') }}
        </button>
    </div>
</form>
