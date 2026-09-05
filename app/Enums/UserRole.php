<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case User = 'user';

    public function label(): string
    {
        return match ($this) {
            self::Admin => __('app.roles.admin'),
            self::User => __('app.roles.user'),
        };
    }
}
