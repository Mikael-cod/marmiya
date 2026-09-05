<?php

namespace App\Services\Admin;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Symfony\Component\Console\Output\BufferedOutput;
use Throwable;

class BackendManagementService
{
    public function __construct(
        private readonly FrontendMaintenanceService $frontendMaintenance,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function overview(): array
    {
        $databaseStatus = 'connected';
        $databaseMessage = null;

        try {
            DB::connection()->getPdo();
        } catch (Throwable $exception) {
            $databaseStatus = 'failed';
            $databaseMessage = $exception->getMessage();
        }

        $logPath = $this->latestLogPath();

        return [
            'app' => [
                'name' => config('app.name'),
                'environment' => config('app.env'),
                'debug' => (bool) config('app.debug'),
                'url' => config('app.url'),
                'timezone' => config('app.timezone'),
                'locale' => config('app.locale'),
                'laravel_version' => app()->version(),
                'php_version' => PHP_VERSION,
            ],
            'drivers' => [
                'database' => config('database.default'),
                'cache' => config('cache.default'),
                'session' => config('session.driver'),
                'queue' => config('queue.default'),
                'mail' => config('mail.default'),
                'filesystem' => config('filesystems.default'),
            ],
            'database' => [
                'status' => $databaseStatus,
                'message' => $databaseMessage,
                'name' => config('database.connections.'.config('database.default').'.database'),
            ],
            'queue' => $this->queueStats(),
            'storage' => $this->storageStats(),
            'maintenance' => $this->maintenanceStatus(),
            'log' => $this->logStats($logPath),
            'log_tail' => $this->logTail($logPath),
        ];
    }

    public function runAction(string $action): string
    {
        if ($action === 'maintenance_down') {
            $this->frontendMaintenance->enable();

            return __('app.admin.backend.action_completed');
        }

        if ($action === 'maintenance_up') {
            $this->frontendMaintenance->disable();

            return __('app.admin.backend.action_completed');
        }

        /** @var array<string, mixed>|null $definition */
        $definition = config("backend_management.actions.{$action}");

        if ($definition === null) {
            throw new RuntimeException(__('app.admin.backend.invalid_action'));
        }

        $command = (string) ($definition['command'] ?? '');
        $parameters = (array) ($definition['parameters'] ?? []);

        $output = new BufferedOutput;

        $exitCode = Artisan::call($command, $parameters, $output);

        if ($exitCode !== 0) {
            throw new RuntimeException(trim($output->fetch()) ?: __('app.admin.backend.action_failed'));
        }

        return trim($output->fetch()) ?: __('app.admin.backend.action_completed');
    }

    public function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }

        if ($bytes < 1024 * 1024) {
            return number_format($bytes / 1024, 1).' KB';
        }

        if ($bytes < 1024 * 1024 * 1024) {
            return number_format($bytes / (1024 * 1024), 2).' MB';
        }

        return number_format($bytes / (1024 * 1024 * 1024), 2).' GB';
    }

    /**
     * @return array<string, int|null>
     */
    protected function queueStats(): array
    {
        $stats = [
            'pending' => null,
            'failed' => null,
        ];

        if (Schema::hasTable('jobs')) {
            $stats['pending'] = (int) DB::table('jobs')->count();
        }

        if (Schema::hasTable('failed_jobs')) {
            $stats['failed'] = (int) DB::table('failed_jobs')->count();
        }

        return $stats;
    }

    /**
     * @return array<string, mixed>
     */
    protected function storageStats(): array
    {
        $paths = [
            'storage' => storage_path(),
            'framework' => storage_path('framework'),
            'logs' => storage_path('logs'),
            'app_private' => storage_path('app/private'),
            'app_public' => storage_path('app/public'),
        ];

        $stats = [];

        foreach ($paths as $key => $path) {
            $sizeBytes = $this->directorySize($path);

            $stats[$key] = [
                'path' => $path,
                'size_bytes' => $sizeBytes,
                'size_label' => $this->formatBytes($sizeBytes),
            ];
        }

        return $stats;
    }

    /**
     * @return array<string, mixed>
     */
    protected function maintenanceStatus(): array
    {
        $status = $this->frontendMaintenance->status();

        return [
            'enabled' => $status['enabled'],
            'enabled_at' => $status['enabled_at'],
            'scope' => 'frontend',
            'laravel_global' => app()->isDownForMaintenance(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function logStats(?string $logPath): array
    {
        if ($logPath === null || ! is_file($logPath)) {
            return [
                'path' => null,
                'size_bytes' => 0,
                'size_label' => '—',
                'updated_at' => null,
            ];
        }

        return [
            'path' => $logPath,
            'size_bytes' => (int) filesize($logPath),
            'size_label' => $this->formatBytes((int) filesize($logPath)),
            'updated_at' => Carbon::createFromTimestamp((int) filemtime($logPath)),
        ];
    }

    protected function latestLogPath(): ?string
    {
        $logsDirectory = storage_path('logs');

        if (! is_dir($logsDirectory)) {
            return null;
        }

        $files = collect(glob($logsDirectory.'/*.log'))
            ->filter(fn (string $path): bool => is_file($path))
            ->sortByDesc(fn (string $path): int => (int) filemtime($path))
            ->values();

        return $files->first();
    }

    /**
     * @return list<string>
     */
    protected function logTail(?string $logPath): array
    {
        if ($logPath === null || ! is_file($logPath)) {
            return [];
        }

        $lines = file($logPath, FILE_IGNORE_NEW_LINES);

        if ($lines === false) {
            return [];
        }

        $limit = (int) config('backend_management.log_tail_lines', 25);

        return array_slice($lines, -1 * max(1, $limit));
    }

    protected function directorySize(string $path): int
    {
        if (! is_dir($path)) {
            return is_file($path) ? (int) filesize($path) : 0;
        }

        $size = 0;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += (int) $file->getSize();
            }
        }

        return $size;
    }
}
