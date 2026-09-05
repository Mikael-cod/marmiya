<?php

return [
    'defaults' => [
        'password_min_length' => 8,
        'password_require_letters' => true,
        'password_require_mixed_case' => false,
        'password_require_numbers' => true,
        'password_require_symbols' => false,
        'login_max_attempts' => 5,
        'login_lockout_minutes' => 5,
        'session_lifetime_minutes' => 120,
        'expire_session_on_close' => false,
        'force_https' => false,
        'security_contact_email' => null,
        'security_guidelines' => null,
    ],
    'recent_failed_logins_limit' => 10,
];
