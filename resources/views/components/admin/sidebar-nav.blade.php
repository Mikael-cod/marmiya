@php
    $navItems = [
        [
            'route' => 'admin.dashboard',
            'label' => __('app.admin.nav_home'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
        ],
        [
            'route' => 'admin.users',
            'label' => __('app.admin.nav_users'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
        ],
        [
            'route' => 'admin.database',
            'label' => __('app.admin.nav_database'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7z"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 7c0 2 1 3 3 3h10c2 0 3-1 3-3M4 12c0 2 1 3 3 3h10c2 0 3-1 3-3"/>',
        ],
        [
            'route' => 'admin.front-pages',
            'activeRoutes' => ['admin.front-pages', 'admin.front-pages.update'],
            'label' => __('app.admin.nav_front_pages'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
        ],
        [
            'route' => 'admin.backend',
            'activeRoutes' => ['admin.backend', 'admin.backend.actions'],
            'label' => __('app.admin.nav_backend'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>',
        ],
        [
            'route' => 'admin.security',
            'activeRoutes' => ['admin.security', 'admin.security.update'],
            'label' => __('app.admin.nav_security'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>',
        ],
        [
            'route' => 'admin.activity',
            'activeRoutes' => ['admin.activity'],
            'label' => __('app.admin.nav_activity'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        ],
        [
            'route' => 'admin.profile',
            'activeRoutes' => ['admin.profile', 'admin.profile.update', 'admin.profile.password'],
            'label' => __('app.admin.nav_profile'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
        ],
    ];
@endphp

<div class="space-y-1">
    @foreach ($navItems as $item)
        <a
            href="{{ route($item['route']) }}"
            @class([
                'flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition',
                'nav-link-active' => request()->routeIs($item['activeRoutes'] ?? $item['route']),
                'nav-link-inactive' => ! request()->routeIs($item['activeRoutes'] ?? $item['route']),
            ])
        >
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                {!! $item['icon'] !!}
            </svg>
            <span>{{ $item['label'] }}</span>
        </a>
    @endforeach
</div>
