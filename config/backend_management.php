<?php

return [
    'log_tail_lines' => 25,

    'actions' => [
        'clear_cache' => [
            'command' => 'cache:clear',
        ],
        'clear_config' => [
            'command' => 'config:clear',
        ],
        'clear_route' => [
            'command' => 'route:clear',
        ],
        'clear_view' => [
            'command' => 'view:clear',
        ],
        'optimize_clear' => [
            'command' => 'optimize:clear',
        ],
        'optimize' => [
            'command' => 'optimize',
        ],
        'maintenance_down' => [
            'requires_confirmation' => true,
        ],
        'maintenance_up' => [
        ],
    ],
];
