<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecuritySetting extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'password_min_length',
        'password_require_letters',
        'password_require_mixed_case',
        'password_require_numbers',
        'password_require_symbols',
        'login_max_attempts',
        'login_lockout_minutes',
        'session_lifetime_minutes',
        'expire_session_on_close',
        'force_https',
        'security_contact_email',
        'security_guidelines',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password_require_letters' => 'boolean',
            'password_require_mixed_case' => 'boolean',
            'password_require_numbers' => 'boolean',
            'password_require_symbols' => 'boolean',
            'expire_session_on_close' => 'boolean',
            'force_https' => 'boolean',
        ];
    }
}
