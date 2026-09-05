<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? __('app.user.dashboard') }} — {{ __('app.name') }}</title>

    <script>
        (function () {
            const theme = localStorage.getItem('maremiya-theme') ?? 'light';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-theme-page font-sans font-ethiopic antialiased">
    <div class="min-h-screen lg:flex">
        <aside class="border-b border-brand-border bg-theme-sidebar lg:fixed lg:inset-y-0 lg:w-64 lg:border-b-0 lg:border-r">
            <div class="flex h-16 items-center gap-3 border-b border-brand-border px-6">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-blue text-white">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold leading-tight text-brand-dark">{{ __('app.subtitle') }}</p>
                    <p class="text-xs text-brand-muted">{{ auth()->user()->role->label() }}</p>
                </div>
            </div>

            <nav class="space-y-1 p-4">
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" @class([
                        'flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition',
                        'nav-link-active' => request()->routeIs('admin.dashboard'),
                        'nav-link-inactive' => ! request()->routeIs('admin.dashboard'),
                    ])>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        {{ __('app.admin.dashboard') }}
                    </a>
                @else
                    <a href="{{ route('user.dashboard') }}" @class([
                        'flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition',
                        'nav-link-active' => request()->routeIs('user.dashboard'),
                        'nav-link-inactive' => ! request()->routeIs('user.dashboard'),
                    ])>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        {{ __('app.user.dashboard') }}
                    </a>
                @endif
            </nav>
        </aside>

        <div class="flex min-h-screen flex-1 flex-col lg:pl-64">
            <header class="sticky top-0 z-10 flex h-16 items-center justify-between border-b border-brand-border bg-theme-header px-6 backdrop-blur">
                <div>
                    <p class="text-sm font-semibold text-brand-dark">{{ __('app.institute') }}</p>
                    <p class="text-xs text-brand-muted">{{ $title ?? __('app.user.dashboard') }}</p>
                </div>

                <div class="flex items-center gap-3 sm:gap-4">
                    <x-eth.clock class="hidden md:inline" />

                    <x-theme-toggle />

                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-medium text-brand-dark">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-brand-muted">{{ auth()->user()->email }}</p>
                    </div>

                    <span @class([
                        'rounded-full px-3 py-1 text-xs font-semibold',
                        'bg-brand-teal/10 text-brand-teal' => auth()->user()->isAdmin(),
                        'bg-brand-blue/10 text-brand-blue' => auth()->user()->isUser(),
                    ])>
                        {{ auth()->user()->role->label() }}
                    </span>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-secondary-brand text-sm">
                            {{ __('app.sign_out') }}
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
