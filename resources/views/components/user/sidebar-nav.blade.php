@php
    $dedicatedReportRoutes = [
        'crime-type' => 'user.reports.crime-type',
        'education-age' => 'user.reports.education-age',
        'sentence-type' => 'user.reports.sentence-type',
        'new-intake' => 'user.reports.new-intake',
        'released' => 'user.reports.released',
        'under-18' => 'user.reports.under-18',
        'parole-released' => 'user.reports.parole-released',
        'children-with-mother' => 'user.reports.children-with-mother',
        'death-sentenced' => 'user.reports.death-sentenced',
        'recidivist' => 'user.reports.recidivist',
    ];

    $reportItems = collect(config('reports'))->map(fn (array $report, string $slug): array => [
        'slug' => $slug,
        'route' => $dedicatedReportRoutes[$slug] ?? 'user.reports.show',
        'label' => __($report['label']),
    ])->values()->all();

    $reportsActive = request()->routeIs(
        'user.reports',
        'user.reports.show',
        'user.reports.crime-type',
        'user.reports.crime-type.export',
        'user.reports.education-age',
        'user.reports.education-age.export',
        'user.reports.sentence-type',
        'user.reports.sentence-type.export',
        'user.reports.new-intake',
        'user.reports.new-intake.export',
        'user.reports.released',
        'user.reports.released.export',
        'user.reports.under-18',
        'user.reports.under-18.export',
        'user.reports.parole-released',
        'user.reports.parole-released.export',
        'user.reports.children-with-mother',
        'user.reports.children-with-mother.export',
        'user.reports.death-sentenced',
        'user.reports.death-sentenced.export',
        'user.reports.recidivist',
        'user.reports.recidivist.export',
    );

    $isReportNavActive = function (string $slug) use ($dedicatedReportRoutes): bool {
        if (isset($dedicatedReportRoutes[$slug])) {
            $routeName = $dedicatedReportRoutes[$slug];

            return request()->routeIs($routeName, $routeName.'.export');
        }

        return request()->routeIs('user.reports.show') && request()->route('report') === $slug;
    };
@endphp

@php
    $navItems = [
        [
            'route' => 'user.dashboard',
            'label' => __('app.user.nav_home'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
        ],
        [
            'route' => 'user.income',
            'label' => __('app.user.nav_income'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        ],
        [
            'route' => 'user.assets',
            'label' => __('app.user.nav_assets'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
        ],
        [
            'route' => 'user.expense',
            'label' => __('app.user.nav_expense'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        ],
        [
            'route' => 'user.recommendations',
            'label' => __('app.user.nav_recommendations'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>',
        ],
        [
            'route' => 'user.prisoners',
            'label' => __('app.user.nav_prisoners'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>',
        ],
        [
            'type' => 'group',
            'label' => __('app.user.nav_reports'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
            'children' => $reportItems,
            'open' => $reportsActive,
        ],
        [
            'route' => 'user.profile',
            'activeRoutes' => ['user.profile', 'user.profile.update', 'user.profile.password'],
            'label' => __('app.user.nav_profile'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
        ],
        [
            'route' => 'user.settings',
            'activeRoutes' => ['user.settings', 'user.settings.update'],
            'label' => __('app.user.nav_settings'),
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
        ],
    ];
@endphp

<div class="space-y-1">
    @foreach ($navItems as $item)
        @if (($item['type'] ?? null) === 'group')
            <div
                class="nav-group"
                data-nav-group
                @if ($item['open'] ?? false) data-nav-group-open @endif
            >
                <button
                    type="button"
                    class="nav-group-toggle flex w-full items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition @if($reportsActive) nav-link-active @else nav-link-inactive @endif"
                    data-nav-group-toggle
                    aria-expanded="{{ ($item['open'] ?? false) ? 'true' : 'false' }}"
                >
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        {!! $item['icon'] !!}
                    </svg>
                    <span class="min-w-0 flex-1 text-start">{{ $item['label'] }}</span>
                    <svg class="nav-group-chevron h-4 w-4 shrink-0 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div class="nav-group-items ms-2 mt-1 space-y-1 border-s border-brand-border/70 ps-3" @unless($item['open'] ?? false) hidden @endunless>
                    @foreach ($item['children'] as $child)
                        <a
                            href="{{ isset($dedicatedReportRoutes[$child['slug']]) ? route($child['route']) : route($child['route'], $child['slug']) }}"
                            @class([
                                'flex items-center rounded-lg px-3 py-2 text-sm transition',
                                'nav-link-active' => $isReportNavActive($child['slug']),
                                'nav-link-inactive' => ! $isReportNavActive($child['slug']),
                            ])
                        >
                            <span>{{ $child['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @else
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
        @endif
    @endforeach
</div>
