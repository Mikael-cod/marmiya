<?php

namespace App\Services\Admin;

use App\Models\FrontPageSetting;
use Illuminate\Support\Facades\Cache;

class FrontPageSettingService
{
    private const CACHE_KEY = 'front_page_settings';

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function (): array {
            $record = FrontPageSetting::query()->first();

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

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(array $data): FrontPageSetting
    {
        $record = FrontPageSetting::query()->first();

        if ($record === null) {
            $record = FrontPageSetting::query()->create(array_merge($this->defaults(), $data));
        } else {
            $record->update($data);
        }

        Cache::forget(self::CACHE_KEY);

        return $record->refresh();
    }

    public function ensureDefaultsExist(): FrontPageSetting
    {
        $record = FrontPageSetting::query()->first();

        if ($record !== null) {
            return $record;
        }

        $record = FrontPageSetting::query()->create($this->defaults());
        Cache::forget(self::CACHE_KEY);

        return $record;
    }

    /**
     * @return array<string, mixed>
     */
    public function defaults(): array
    {
        return [
            'app_name' => 'አርባምንጭ ማረሚያ ተቋም ዳታ ቤዝ አስተዳደር',
            'institute' => 'አርባምንጭ ማረሚያ ተቋም',
            'subtitle' => 'የታራሚዎች ገቢና ወጪ አስተዳደር',
            'login_description' => 'የአርባምንጭ ማረሚያ ተቋም የታራሚዎች ገቢ፣ ወጪ እና አጠቃላይ ሁኔታ ለማስተዳደር ወደ ስርዓቱ ለመግባት ይግቡ።',
            'secure_platform' => 'ደህንነቱ የተጠበቀ የታራሚዎች ገቢና ወጪ አስተዳደር መድረክ',
            'welcome_back' => 'እንኳን ደህና መጡ',
            'enter_credentials' => 'ለመቀጠል የመግቢያ መረጃዎን ያስገቡ',
            'contact_support' => 'ድጋፍ ያግኙ',
            'contact_support_url' => null,
            'contact_administrator_url' => null,
            'help_center_url' => null,
            'copyright' => 'ሁሉም መብቶች የተጠበቁ ናቸው።',
            'show_secure_badge' => true,
            'default_theme' => 'light',
        ];
    }
}
