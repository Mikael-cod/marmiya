@php
    $driverLabels = [
        'sqlite' => __('app.admin.database.driver_sqlite'),
        'mysql' => __('app.admin.database.driver_mysql'),
        'mariadb' => __('app.admin.database.driver_mariadb'),
    ];
    $driverLabel = $driverLabels[$databaseInfo['driver']] ?? $databaseInfo['driver'];
    $selectedRestore = filled($restoreBackup)
        ? $backups->firstWhere('filename', $restoreBackup)
        : null;
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
            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <div class="min-w-0">
                    <h1 class="text-2xl font-bold text-brand-dark sm:text-3xl">{{ $title }}</h1>
                    <p class="mt-3 max-w-3xl text-sm leading-relaxed text-brand-muted sm:text-base">{{ $description }}</p>
                </div>

                <form method="POST" action="{{ route('admin.database.store') }}">
                    @csrf
                    <button type="submit" class="btn-primary-brand intake-register-btn">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 12V4m0 0L8 8m4-4l4 4"/>
                        </svg>
                        {{ __('app.admin.database.create') }}
                    </button>
                </form>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.database.info_driver') }}</p>
            <p class="mt-2 text-lg font-bold text-brand-dark">{{ $driverLabel }}</p>
        </div>

        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.database.info_database') }}</p>
            <p class="mt-2 break-all text-lg font-bold text-brand-dark">{{ $databaseInfo['database'] }}</p>
        </div>

        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.database.info_size') }}</p>
            <p class="mt-2 text-lg font-bold text-brand-dark">{{ $databaseInfo['size_label'] ?? '—' }}</p>
        </div>

        <div class="card-surface shadow-auth-card">
            <p class="text-sm font-medium text-brand-muted">{{ __('app.admin.database.info_tables') }}</p>
            <p class="mt-2 text-lg font-bold text-brand-dark">{{ $databaseInfo['table_count'] ?? '—' }}</p>
        </div>
    </section>

    @if (filled($databaseInfo['host']))
        <section class="card-surface mt-6 shadow-auth-card">
            <p class="text-sm text-brand-muted">
                {{ __('app.admin.database.info_host') }}:
                <span class="font-semibold text-brand-dark">{{ $databaseInfo['host'] }}</span>
            </p>
        </section>
    @endif

    <section class="card-surface mt-6 shadow-auth-card">
        <div class="border-b border-brand-border px-6 py-4">
            <h2 class="text-lg font-bold text-brand-dark">{{ __('app.admin.database.list_title') }}</h2>
            <p class="mt-1 text-sm text-brand-muted">{{ __('app.admin.database.list_subtitle') }}</p>
        </div>

        @if ($backups->isEmpty())
            <div class="px-6 py-14 text-center">
                <p class="text-sm text-brand-muted">{{ __('app.admin.database.no_backups') }}</p>
                <form method="POST" action="{{ route('admin.database.store') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="btn-primary-brand intake-register-btn">
                        {{ __('app.admin.database.create_first') }}
                    </button>
                </form>
            </div>
        @else
            <div class="overflow-x-auto px-2 pb-2 pt-2 sm:px-4">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-brand-border text-start text-brand-muted">
                            <th class="px-3 py-3 font-medium">#</th>
                            <th class="px-3 py-3 font-medium">{{ __('app.admin.database.columns.filename') }}</th>
                            <th class="px-3 py-3 font-medium">{{ __('app.admin.database.columns.driver') }}</th>
                            <th class="px-3 py-3 font-medium">{{ __('app.admin.database.columns.size') }}</th>
                            <th class="px-3 py-3 font-medium">{{ __('app.admin.database.columns.created_at') }}</th>
                            <th class="px-3 py-3 font-medium text-end">{{ __('app.admin.database.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($backups as $backup)
                            <tr class="border-b border-brand-border/70 last:border-0">
                                <td class="px-3 py-3 text-brand-muted">{{ $loop->iteration }}</td>
                                <td class="px-3 py-3 font-medium text-brand-dark">{{ $backup['filename'] }}</td>
                                <td class="px-3 py-3 text-brand-muted">
                                    {{ $driverLabels[$backup['driver']] ?? $backup['driver'] }}
                                </td>
                                <td class="px-3 py-3 text-brand-muted">{{ $backup['size_label'] }}</td>
                                <td class="px-3 py-3 text-brand-muted"><x-eth.datetime :value="$backup['created_at']" /></td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('admin.database.download', $backup['filename']) }}"
                                            class="intake-action-btn intake-action-view"
                                            title="{{ __('app.admin.database.download') }}"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2"/>
                                            </svg>
                                            <span class="hidden sm:inline">{{ __('app.admin.database.download') }}</span>
                                        </a>

                                        <a
                                            href="{{ route('admin.database', ['restore' => $backup['filename']]) }}"
                                            class="intake-action-btn intake-action-edit"
                                            title="{{ __('app.admin.database.restore') }}"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582M4.582 9A7.003 7.003 0 0012 17a7.003 7.003 0 006.418-4M20 20v-5h-.581M15.418 15A7.003 7.003 0 0112 7a7.003 7.003 0 00-6.418 4"/>
                                            </svg>
                                            <span class="hidden sm:inline">{{ __('app.admin.database.restore') }}</span>
                                        </a>

                                        <form
                                            action="{{ route('admin.database.destroy', $backup['filename']) }}"
                                            method="POST"
                                            class="inline"
                                            onsubmit="return confirm(@js(__('app.admin.database.delete_confirm')))"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="intake-action-btn intake-action-delete" title="{{ __('app.admin.database.delete') }}">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0h8l-1-3H9l-1 3z"/>
                                                </svg>
                                                <span class="hidden sm:inline">{{ __('app.admin.database.delete') }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <section class="card-surface mt-6 border-amber-200/60 bg-amber-50/40 shadow-auth-card dark:border-amber-500/20 dark:bg-amber-500/5">
        <div class="px-6 py-5">
            <h2 class="text-base font-bold text-brand-dark">{{ __('app.admin.database.warning_title') }}</h2>
            <p class="mt-2 text-sm leading-relaxed text-brand-muted">{{ __('app.admin.database.warning_body') }}</p>
        </div>
    </section>

    @if ($selectedRestore)
        <div
            id="database-restore-modal"
            class="intake-modal is-open"
            aria-hidden="false"
        >
            <div class="intake-modal-backdrop" data-database-restore-close aria-hidden="true"></div>

            <div class="intake-modal-panel" role="dialog" aria-modal="true" aria-labelledby="database-restore-title">
                <div class="intake-modal-header">
                    <div class="intake-form-emblem">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500/10 text-amber-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h2 id="database-restore-title" class="text-lg font-bold text-brand-dark">{{ __('app.admin.database.restore_title') }}</h2>
                        <p class="mt-1 text-sm text-brand-muted">{{ __('app.admin.database.restore_subtitle') }}</p>
                    </div>

                    <a
                        href="{{ route('admin.database') }}"
                        class="intake-modal-close btn-icon-brand shrink-0"
                        data-database-restore-close
                        aria-label="{{ __('app.admin.database.close') }}"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                </div>

                <div class="intake-modal-body">
                    <div class="rounded-2xl border border-amber-200/70 bg-amber-50/50 px-4 py-3 text-sm leading-relaxed text-brand-dark dark:border-amber-500/20 dark:bg-amber-500/5">
                        {{ __('app.admin.database.restore_warning', ['filename' => $selectedRestore['filename']]) }}
                    </div>

                    <form method="POST" action="{{ route('admin.database.restore', $selectedRestore['filename']) }}" class="mt-6 space-y-5">
                        @csrf

                        <label class="intake-checkbox-label">
                            <input
                                type="checkbox"
                                name="confirm_restore"
                                value="1"
                                class="intake-checkbox"
                                @checked(old('confirm_restore'))
                                required
                            >
                            <span>{{ __('app.admin.database.restore_confirm_label') }}</span>
                        </label>
                        @error('confirm_restore')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <div class="flex justify-end gap-3 border-t border-brand-border pt-4">
                            <a href="{{ route('admin.database') }}" class="btn-secondary-brand">{{ __('app.admin.database.cancel') }}</a>
                            <button type="submit" class="btn-primary-brand intake-register-btn !bg-amber-600 hover:!bg-amber-700">
                                {{ __('app.admin.database.restore_submit') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.body.classList.add('intake-modal-open');
            document.body.style.overflow = 'hidden';
            document.querySelectorAll('[data-database-restore-close]').forEach((button) => {
                button.addEventListener('click', () => {
                    document.body.classList.remove('intake-modal-open');
                    document.body.style.overflow = '';
                });
            });
        </script>
    @endif
</x-layouts.admin>
