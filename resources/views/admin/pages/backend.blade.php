@php
    $app = $overview['app'];
    $drivers = $overview['drivers'];
    $database = $overview['database'];
    $queue = $overview['queue'];
    $storage = $overview['storage'];
    $maintenance = $overview['maintenance'];
    $log = $overview['log'];
    $logTail = $overview['log_tail'];

    $dangerousActions = ['optimize_clear', 'maintenance_down'];
@endphp

<x-layouts.admin :title="$title">
    @if (session('success'))
        <div class="alert-success mb-6">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert-error mb-6">{{ session('error') }}</div>
    @endif

    <section class="overflow-hidden rounded-3xl border border-brand-border bg-brand-surface shadow-auth-card">
        <div class="relative px-6 py-8 sm:px-8 sm:py-10">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-brand-teal/5 via-transparent to-brand-blue/5"></div>
            <div class="relative">
                <h1 class="text-2xl font-bold text-brand-dark sm:text-3xl">{{ $title }}</h1>
                <p class="mt-3 max-w-3xl text-sm leading-relaxed text-brand-muted sm:text-base">{{ $description }}</p>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.backend.info_environment') }}</p>
            <p class="mt-2 text-lg font-bold text-brand-dark">{{ strtoupper($app['environment']) }}</p>
        </div>

        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.backend.info_php') }}</p>
            <p class="mt-2 text-lg font-bold text-brand-dark">{{ $app['php_version'] }}</p>
        </div>

        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.backend.info_laravel') }}</p>
            <p class="mt-2 text-lg font-bold text-brand-dark">{{ $app['laravel_version'] }}</p>
        </div>

        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.backend.info_debug') }}</p>
            <p class="mt-2 text-lg font-bold {{ $app['debug'] ? 'text-amber-600' : 'text-emerald-600' }}">
                {{ $app['debug'] ? __('app.admin.backend.debug_on') : __('app.admin.backend.debug_off') }}
            </p>
        </div>
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="card-surface shadow-auth-card">
            <div class="border-b border-brand-border px-6 py-4">
                <h2 class="text-lg font-bold text-brand-dark">{{ __('app.admin.backend.app_title') }}</h2>
                <p class="mt-1 text-sm text-brand-muted">{{ __('app.admin.backend.app_subtitle') }}</p>
            </div>
            <dl class="divide-y divide-brand-border px-6">
                <div class="flex items-start justify-between gap-4 py-3">
                    <dt class="text-sm text-brand-muted">{{ __('app.admin.backend.fields.app_name') }}</dt>
                    <dd class="text-right text-sm font-semibold text-brand-dark">{{ $app['name'] }}</dd>
                </div>
                <div class="flex items-start justify-between gap-4 py-3">
                    <dt class="text-sm text-brand-muted">{{ __('app.admin.backend.fields.url') }}</dt>
                    <dd class="max-w-[14rem] break-all text-right text-sm font-semibold text-brand-dark">{{ $app['url'] }}</dd>
                </div>
                <div class="flex items-start justify-between gap-4 py-3">
                    <dt class="text-sm text-brand-muted">{{ __('app.admin.backend.fields.timezone') }}</dt>
                    <dd class="text-right text-sm font-semibold text-brand-dark">{{ $app['timezone'] }}</dd>
                </div>
                <div class="flex items-start justify-between gap-4 py-3">
                    <dt class="text-sm text-brand-muted">{{ __('app.admin.backend.fields.locale') }}</dt>
                    <dd class="text-right text-sm font-semibold text-brand-dark">{{ $app['locale'] }}</dd>
                </div>
            </dl>
        </div>

        <div class="card-surface shadow-auth-card">
            <div class="border-b border-brand-border px-6 py-4">
                <h2 class="text-lg font-bold text-brand-dark">{{ __('app.admin.backend.drivers_title') }}</h2>
                <p class="mt-1 text-sm text-brand-muted">{{ __('app.admin.backend.drivers_subtitle') }}</p>
            </div>
            <dl class="divide-y divide-brand-border px-6">
                @foreach ($drivers as $key => $value)
                    <div class="flex items-start justify-between gap-4 py-3">
                        <dt class="text-sm text-brand-muted">{{ __("app.admin.backend.driver_labels.{$key}") }}</dt>
                        <dd class="text-right text-sm font-semibold text-brand-dark">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    <section class="mt-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.backend.info_database_status') }}</p>
            <p class="mt-2 text-lg font-bold {{ $database['status'] === 'connected' ? 'text-emerald-600' : 'text-red-600' }}">
                {{ $database['status'] === 'connected' ? __('app.admin.backend.database_connected') : __('app.admin.backend.database_failed') }}
            </p>
            @if (filled($database['name']))
                <p class="mt-1 break-all text-xs text-brand-muted">{{ $database['name'] }}</p>
            @endif
        </div>

        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.backend.info_queue_pending') }}</p>
            <p class="mt-2 text-lg font-bold text-brand-dark">{{ $queue['pending'] ?? '—' }}</p>
        </div>

        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.backend.info_queue_failed') }}</p>
            <p class="mt-2 text-lg font-bold {{ ($queue['failed'] ?? 0) > 0 ? 'text-amber-600' : 'text-brand-dark' }}">
                {{ $queue['failed'] ?? '—' }}
            </p>
        </div>

        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.backend.info_maintenance') }}</p>
            <p class="mt-2 text-lg font-bold {{ $maintenance['enabled'] ? 'text-amber-600' : 'text-emerald-600' }}">
                {{ $maintenance['enabled'] ? __('app.admin.backend.maintenance_on') : __('app.admin.backend.maintenance_off') }}
            </p>
        </div>
    </section>

    @if ($maintenance['enabled'])
        <section class="card-surface mt-6 border-amber-200/60 bg-amber-50/40 shadow-auth-card dark:border-amber-500/20 dark:bg-amber-500/5">
            <div class="px-6 py-5">
                <p class="font-semibold text-brand-dark">{{ __('app.admin.backend.maintenance_active_title') }}</p>
                <p class="mt-1 text-sm text-brand-muted">{{ __('app.admin.backend.maintenance_active_body') }}</p>
                @if ($maintenance['enabled_at'])
                    <p class="mt-2 text-xs text-brand-muted">
                        {{ __('app.admin.backend.maintenance_enabled_at') }}:
                        {{ $maintenance['enabled_at']->format('Y-m-d H:i') }}
                    </p>
                @endif
            </div>
        </section>
    @endif

    <section class="card-surface mt-6 shadow-auth-card">
        <div class="border-b border-brand-border px-6 py-4">
            <h2 class="text-lg font-bold text-brand-dark">{{ __('app.admin.backend.storage_title') }}</h2>
            <p class="mt-1 text-sm text-brand-muted">{{ __('app.admin.backend.storage_subtitle') }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-brand-border bg-brand-surface-alt/60">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-brand-muted">{{ __('app.admin.backend.columns.location') }}</th>
                        <th class="px-6 py-3 text-left font-semibold text-brand-muted">{{ __('app.admin.backend.columns.path') }}</th>
                        <th class="px-6 py-3 text-right font-semibold text-brand-muted">{{ __('app.admin.backend.columns.size') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border">
                    @foreach ($storage as $key => $item)
                        <tr>
                            <td class="px-6 py-3 font-medium text-brand-dark">{{ __("app.admin.backend.storage_labels.{$key}") }}</td>
                            <td class="px-6 py-3 font-mono text-xs text-brand-muted">{{ $item['path'] }}</td>
                            <td class="px-6 py-3 text-right font-semibold text-brand-dark">{{ $item['size_label'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="card-surface mt-6 shadow-auth-card">
        <div class="border-b border-brand-border px-6 py-4">
            <h2 class="text-lg font-bold text-brand-dark">{{ __('app.admin.backend.actions_title') }}</h2>
            <p class="mt-1 text-sm text-brand-muted">{{ __('app.admin.backend.actions_subtitle') }}</p>
        </div>

        <div class="grid gap-4 p-6 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($actions as $action)
                @php
                    $isDangerous = in_array($action, $dangerousActions, true);
                    $requiresConfirm = (bool) data_get(config("backend_management.actions.{$action}"), 'requires_confirmation', false);
                    $confirmMessage = __("app.admin.backend.actions.{$action}.confirm");
                @endphp

                <div class="rounded-2xl border border-brand-border bg-brand-surface-alt/40 p-4">
                    <h3 class="font-semibold text-brand-dark">{{ __("app.admin.backend.actions.{$action}.title") }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-brand-muted">{{ __("app.admin.backend.actions.{$action}.description") }}</p>

                    <form
                        method="POST"
                        action="{{ route('admin.backend.actions') }}"
                        class="mt-4"
                        @if ($requiresConfirm)
                            onsubmit="return confirm(@js($confirmMessage))"
                        @elseif ($isDangerous)
                            onsubmit="return confirm(@js(__('app.admin.backend.confirm_dangerous')))"
                        @endif
                    >
                        @csrf
                        <input type="hidden" name="action" value="{{ $action }}">

                        @if ($requiresConfirm)
                            <label class="mb-3 flex items-start gap-2 text-xs text-brand-muted">
                                <input type="checkbox" name="confirm_action" value="1" class="mt-0.5 rounded border-brand-border">
                                <span>{{ __('app.admin.backend.confirm_checkbox') }}</span>
                            </label>
                        @endif

                        <button
                            type="submit"
                            class="{{ $isDangerous || $requiresConfirm ? 'btn-secondary-brand' : 'btn-primary-brand' }} w-full"
                        >
                            {{ __("app.admin.backend.actions.{$action}.button") }}
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </section>

    <section class="card-surface mt-6 overflow-hidden p-0 shadow-auth-card">
        <div class="border-b border-brand-border px-6 py-4">
            <h2 class="text-lg font-bold text-brand-dark">{{ __('app.admin.backend.log_title') }}</h2>
            <p class="mt-1 text-sm text-brand-muted">
                {{ __('app.admin.backend.log_subtitle') }}
                @if (filled($log['path']))
                    · {{ $log['size_label'] }}
                    @if ($log['updated_at'])
                        · {{ $log['updated_at']->format('Y-m-d H:i') }}
                    @endif
                @endif
            </p>
        </div>

        @if (count($logTail) === 0)
            <p class="px-6 py-8 text-sm text-brand-muted">{{ __('app.admin.backend.log_empty') }}</p>
        @else
            <div class="admin-log-viewer">
                <pre class="admin-log-viewer__content">{{ implode("\n", $logTail) }}</pre>
            </div>
        @endif
    </section>
</x-layouts.admin>
