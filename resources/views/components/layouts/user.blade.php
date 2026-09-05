<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? __('app.user.dashboard') }} — {{ front_setting('app_name') }}</title>

    <x-theme-init />

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-theme-page font-sans font-ethiopic antialiased">
    {{-- Mobile backdrop --}}
    <div id="sidebar-backdrop" class="sidebar-backdrop fixed inset-0 z-40 bg-black/50 backdrop-blur-sm lg:hidden" aria-hidden="true"></div>

    {{-- Sidebar --}}
    <aside
        id="dashboard-sidebar"
        class="dashboard-sidebar dashboard-sidebar-panel fixed inset-y-0 start-0 z-50 flex flex-col border-e border-brand-border bg-theme-sidebar"
        aria-label="{{ __('app.layout.navigation') }}"
    >
        {{-- Sidebar header --}}
        <div class="flex h-16 shrink-0 items-center justify-between gap-3 border-b border-brand-border px-5">
            <div class="flex min-w-0 items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-blue text-white shadow-lg shadow-brand-blue/20">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="truncate text-sm font-bold text-brand-dark">{{ front_setting('institute') }}</p>
                    <p class="truncate text-xs text-brand-muted">{{ front_setting('subtitle') }}</p>
                </div>
            </div>

            <button
                type="button"
                data-sidebar-close
                class="sidebar-close-btn btn-icon-brand"
                aria-label="{{ __('app.layout.close_menu') }}"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Sidebar navigation --}}
        <nav class="flex-1 space-y-6 overflow-y-auto px-4 py-5">
            <div>
                <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-brand-muted">{{ __('app.layout.navigation') }}</p>
                <x-user.sidebar-nav />
            </div>
        </nav>

        {{-- Sidebar footer --}}
        <div class="shrink-0 border-t border-brand-border bg-theme-sidebar p-4">
            <div class="rounded-2xl border border-brand-border bg-brand-surface p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand-blue/10 text-sm font-bold text-brand-blue">
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-brand-dark">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-brand-muted">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-between gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-teal/10 px-2.5 py-1 text-xs font-semibold text-brand-teal">
                        <span class="h-1.5 w-1.5 rounded-full bg-brand-teal"></span>
                        {{ __('app.layout.online') }}
                    </span>
                    <span class="rounded-full bg-brand-blue/10 px-2.5 py-1 text-xs font-semibold text-brand-blue">
                        {{ auth()->user()->role->label() }}
                    </span>
                </div>

                <div class="mt-4 flex items-center justify-between border-t border-brand-border pt-3 text-xs text-brand-muted">
                    <span>{{ __('app.layout.system_version') }}</span>
                    <span class="font-medium text-brand-dark">{{ __('app.layout.version_number') }}</span>
                </div>
            </div>
        </div>
    </aside>

    {{-- Main wrapper --}}
    <div class="dashboard-main flex min-h-screen flex-col">
        {{-- Header --}}
        <header class="sticky top-0 z-30 border-b border-brand-border bg-theme-header backdrop-blur">
            <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <div class="flex min-w-0 items-center gap-3">
                    <button
                        type="button"
                        data-sidebar-toggle
                        class="sidebar-toggle-btn btn-icon-brand"
                        aria-label="{{ __('app.layout.menu') }}"
                        aria-expanded="true"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-brand-dark lg:text-base">{{ $title ?? __('app.user.dashboard') }}</p>
                        <p class="truncate text-xs text-brand-muted">{{ __('app.layout.main_section') }}</p>
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                    <x-eth.clock class="hidden lg:inline" />

                    <x-theme-toggle />

                    <div class="hidden items-center gap-3 rounded-xl border border-brand-border bg-brand-surface px-3 py-2 md:flex">
                        <div class="text-end">
                            <p class="text-sm font-medium text-brand-dark">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-brand-muted">{{ __('app.layout.signed_in_as') }} {{ auth()->user()->role->label() }}</p>
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-secondary-brand whitespace-nowrap px-4 py-2 text-sm sm:px-5">
                            <span class="hidden sm:inline">{{ __('app.sign_out') }}</span>
                            <span class="sm:hidden">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        {{-- Main content --}}
        <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            <div class="mx-auto max-w-7xl">
                {{ $slot }}
            </div>
        </main>

        {{-- Page footer --}}
        <footer class="mt-auto border-t border-brand-border bg-theme-footer px-4 py-5 sm:px-6 lg:px-8">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 sm:flex-row">
                <p class="text-center text-xs text-brand-muted sm:text-start">
                    &copy; {{ eth_year() }} {{ front_setting('institute') }}. {{ front_setting('copyright') }}
                </p>

                <div class="flex flex-wrap items-center justify-center gap-4 text-xs text-brand-muted">
                    <x-eth.clock />
                    <a href="{{ front_url('help_center_url') }}" class="transition hover:text-brand-blue">{{ __('app.layout.help_center') }}</a>
                    <span class="hidden h-3 w-px bg-brand-border sm:block"></span>
                    <span>{{ __('app.layout.system_version') }} {{ __('app.layout.version_number') }}</span>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
