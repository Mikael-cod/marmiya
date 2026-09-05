<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? front_setting('app_name') }}</title>

    <x-theme-init />

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-auth-page font-sans font-ethiopic">
    <div class="relative min-h-screen bg-auth-grid">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -top-24 right-0 h-72 w-72 rounded-full blur-3xl" style="background: var(--theme-glow-blue);"></div>
            <div class="absolute bottom-0 left-0 h-80 w-80 rounded-full blur-3xl" style="background: var(--theme-glow-teal);"></div>
        </div>

        <div class="relative flex min-h-screen flex-col">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
