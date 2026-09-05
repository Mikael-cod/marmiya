<?php

namespace App\Providers;

use App\Services\Admin\SecuritySettingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale(config('app.locale', 'am'));
        date_default_timezone_set(config('app.timezone', 'Africa/Addis_Ababa'));

        Blade::anonymousComponentPath(resource_path('views/components/eth'), 'eth');

        $this->configureSecurityDefaults();
        $this->configureSessionFromSecuritySettings();
    }

    protected function configureSecurityDefaults(): void
    {
        Password::defaults(function (): Password {
            try {
                if (! Schema::hasTable('security_settings')) {
                    return Password::min(8)->letters()->numbers();
                }

                return app(SecuritySettingService::class)->passwordRule();
            } catch (\Throwable) {
                return Password::min(8)->letters()->numbers();
            }
        });
    }

    protected function configureSessionFromSecuritySettings(): void
    {
        try {
            if (! Schema::hasTable('security_settings')) {
                return;
            }

            $settings = app(SecuritySettingService::class);

            config([
                'session.lifetime' => (int) $settings->get('session_lifetime_minutes', 120),
                'session.expire_on_close' => (bool) $settings->get('expire_session_on_close', false),
            ]);
        } catch (\Throwable) {
            //
        }
    }
}
