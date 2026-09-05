<?php

namespace App\Services\Admin;

use App\Enums\UserRole;
use App\Models\SecurityLoginAttempt;
use App\Models\User;
use Illuminate\Http\Request;

class SecurityOverviewService
{
    public function __construct(
        private readonly SecuritySettingService $settings,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function overview(Request $request): array
    {
        $settings = $this->settings->all();

        return [
            'users' => [
                'total' => User::query()->count(),
                'admins' => User::query()->where('role', UserRole::Admin)->count(),
                'standard' => User::query()->where('role', UserRole::User)->count(),
            ],
            'environment' => [
                'app_env' => config('app.env'),
                'debug' => (bool) config('app.debug'),
                'https' => $request->isSecure(),
                'csrf_enabled' => true,
            ],
            'session' => [
                'driver' => config('session.driver'),
                'lifetime_minutes' => (int) $settings['session_lifetime_minutes'],
                'expire_on_close' => (bool) $settings['expire_session_on_close'],
            ],
            'login_protection' => [
                'max_attempts' => (int) $settings['login_max_attempts'],
                'lockout_minutes' => (int) $settings['login_lockout_minutes'],
            ],
            'password_policy' => $this->settings->passwordPolicySummary(),
            'failed_last_24h' => SecurityLoginAttempt::query()
                ->failed()
                ->where('attempted_at', '>=', now()->subDay())
                ->count(),
            'recent_failed_logins' => SecurityLoginAttempt::query()
                ->failed()
                ->orderByDesc('attempted_at')
                ->limit((int) config('security.recent_failed_logins_limit', 10))
                ->get(),
        ];
    }
}
