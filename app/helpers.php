<?php

use App\Support\EthiopianCalendar;
use Carbon\Carbon;
use Illuminate\Support\Carbon as LaravelCarbon;

if (! function_exists('eth_date')) {
    function eth_date(Carbon|LaravelCarbon|string|null $value, bool $includeWeekDay = false): ?string
    {
        return EthiopianCalendar::formatDate($value, $includeWeekDay);
    }
}

if (! function_exists('eth_datetime')) {
    function eth_datetime(Carbon|LaravelCarbon|string|null $value): ?string
    {
        return EthiopianCalendar::formatDateTime($value);
    }
}

if (! function_exists('eth_time')) {
    function eth_time(?string $value): ?string
    {
        return EthiopianCalendar::formatTime($value);
    }
}

if (! function_exists('eth_year')) {
    function eth_year(): int
    {
        return EthiopianCalendar::currentYear();
    }
}

if (! function_exists('eth_clock')) {
    function eth_clock(Carbon|LaravelCarbon|null $value = null): string
    {
        return EthiopianCalendar::formatClock($value);
    }
}

if (! function_exists('front_setting')) {
    function front_setting(string $key, mixed $default = null): mixed
    {
        return app(\App\Services\Admin\FrontPageSettingService::class)->get($key, $default);
    }
}

if (! function_exists('front_url')) {
    function front_url(string $key, string $fallback = '#'): string
    {
        $url = front_setting($key);

        return filled($url) ? (string) $url : $fallback;
    }
}

if (! function_exists('security_setting')) {
    function security_setting(string $key, mixed $default = null): mixed
    {
        return app(\App\Services\Admin\SecuritySettingService::class)->get($key, $default);
    }
}

if (! function_exists('activity_log')) {
    function activity_log(
        string $event,
        ?string $description = null,
        array $metadata = [],
        ?\App\Models\User $actor = null,
    ): void {
        app(\App\Services\Admin\ActivityLogService::class)->record(
            $event,
            $description,
            $metadata,
            $actor ?? auth()->user(),
            request(),
        );
    }
}
