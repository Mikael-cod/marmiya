<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrontPageSetting extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'app_name',
        'institute',
        'subtitle',
        'login_description',
        'secure_platform',
        'welcome_back',
        'enter_credentials',
        'contact_support',
        'contact_support_url',
        'contact_administrator_url',
        'help_center_url',
        'copyright',
        'show_secure_badge',
        'default_theme',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'show_secure_badge' => 'boolean',
        ];
    }
}
