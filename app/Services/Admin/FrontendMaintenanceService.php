<?php

namespace App\Services\Admin;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use RuntimeException;

class FrontendMaintenanceService
{
    public function isEnabled(): bool
    {
        return $this->status()['enabled'];
    }

    /**
     * @return array{enabled: bool, enabled_at: ?Carbon}
     */
    public function status(): array
    {
        $path = $this->flagPath();

        if (! is_file($path)) {
            return [
                'enabled' => false,
                'enabled_at' => null,
            ];
        }

        /** @var array<string, mixed>|null $data */
        $data = json_decode((string) file_get_contents($path), true);

        if (! is_array($data) || ! ($data['enabled'] ?? false)) {
            return [
                'enabled' => false,
                'enabled_at' => null,
            ];
        }

        $enabledAt = filled($data['enabled_at'] ?? null)
            ? Carbon::parse((string) $data['enabled_at'])
            : null;

        return [
            'enabled' => true,
            'enabled_at' => $enabledAt,
        ];
    }

    public function enable(): void
    {
        $this->ensureApplicationIsUp();

        $payload = [
            'enabled' => true,
            'enabled_at' => now()->toIso8601String(),
        ];

        if (file_put_contents($this->flagPath(), json_encode($payload, JSON_PRETTY_PRINT)) === false) {
            throw new RuntimeException(__('app.admin.backend.maintenance_enable_failed'));
        }
    }

    public function disable(): void
    {
        $path = $this->flagPath();

        if (is_file($path) && ! unlink($path)) {
            throw new RuntimeException(__('app.admin.backend.maintenance_disable_failed'));
        }

        $this->ensureApplicationIsUp();
    }

    protected function ensureApplicationIsUp(): void
    {
        if (! app()->isDownForMaintenance()) {
            return;
        }

        Artisan::call('up');
    }

    protected function flagPath(): string
    {
        return (string) config('frontend_maintenance.flag_path');
    }
}
