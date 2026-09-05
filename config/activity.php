<?php

return [
    'default_per_page' => 20,
    'per_page_options' => [15, 20, 50, 100],

    'categories' => [
        'auth' => 'auth',
        'admin' => 'admin',
        'security' => 'security',
        'system' => 'system',
    ],

    'events' => [
        'auth.login' => 'auth',
        'auth.logout' => 'auth',
        'auth.login_failed' => 'auth',
        'admin.user.created' => 'admin',
        'admin.user.updated' => 'admin',
        'admin.user.deleted' => 'admin',
        'admin.database.backup_created' => 'admin',
        'admin.database.backup_restored' => 'admin',
        'admin.database.backup_deleted' => 'admin',
        'admin.front_pages.updated' => 'admin',
        'admin.security.updated' => 'admin',
        'admin.backend.action' => 'admin',
        'admin.frontend_maintenance.enabled' => 'admin',
        'admin.frontend_maintenance.disabled' => 'admin',
        'admin.profile.updated' => 'admin',
        'admin.profile.password_updated' => 'admin',
    ],
];
