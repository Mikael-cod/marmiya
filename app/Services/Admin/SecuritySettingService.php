<?php

namespace App\Services\Admin;

use App\Models\SecuritySetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rules\Password;

class SecuritySettingService
{
    private const CACHE_KEY = 'security_settings';

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function (): array {
            $record = SecuritySetting::query()->first();

            if ($record === null) {
                return $this->defaults();
            }

            return array_merge($this->defaults(), $record->only(array_keys($this->defaults())));
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->all(), $key, $default);
    }

    public function passwordRule(): Password
    {
        $rule = Password::min(max(6, (int) $this->get('password_min_length', 8)));

        if ($this->get('password_require_letters')) {
            $rule = $rule->letters();
        }

        if ($this->get('password_require_mixed_case')) {
            $rule = $rule->mixedCase();
        }

        if ($this->get('password_require_numbers')) {
            $rule = $rule->numbers();
        }

        if ($this->get('password_require_symbols')) {
            $rule = $rule->symbols();
        }

        return $rule;
    }

    /**
     * @return list<string>
     */
    public function passwordPolicySummary(): array
    {
        $summary = [
            __('app.admin.security.policy_min_length', ['count' => (int) $this->get('password_min_length', 8)]),
        ];

        if ($this->get('password_require_letters')) {
            $summary[] = __('app.admin.security.policy_letters');
        }

        if ($this->get('password_require_mixed_case')) {
            $summary[] = __('app.admin.security.policy_mixed_case');
        }

        if ($this->get('password_require_numbers')) {
            $summary[] = __('app.admin.security.policy_numbers');
        }

        if ($this->get('password_require_symbols')) {
            $summary[] = __('app.admin.security.policy_symbols');
        }

        return $summary;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(array $data): SecuritySetting
    {
        $record = SecuritySetting::query()->first();

        if ($record === null) {
            $record = SecuritySetting::query()->create(array_merge($this->defaults(), $data));
        } else {
            $record->update($data);
        }

        Cache::forget(self::CACHE_KEY);

        return $record->refresh();
    }

    public function ensureDefaultsExist(): SecuritySetting
    {
        $record = SecuritySetting::query()->first();

        if ($record !== null) {
            return $record;
        }

        $record = SecuritySetting::query()->create($this->defaults());
        Cache::forget(self::CACHE_KEY);

        return $record;
    }

    /**
     * @return array<string, mixed>
     */
    public function defaults(): array
    {
        return config('security.defaults', []);
    }
}
