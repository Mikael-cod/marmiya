<?php

namespace App\Services\Admin;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Process\Process;

class DatabaseBackupService
{
    public function connectionName(): string
    {
        return (string) config('database.default');
    }

    public function driver(): string
    {
        return (string) config('database.connections.'.$this->connectionName().'.driver');
    }

    /**
     * @return array{
     *     driver: string,
     *     connection: string,
     *     database: string,
     *     host: string|null,
     *     size_bytes: int|null,
     *     table_count: int|null,
     * }
     */
    public function databaseInfo(): array
    {
        $connection = $this->connectionName();
        $config = config('database.connections.'.$connection);
        $driver = (string) ($config['driver'] ?? $this->driver());
        $database = (string) ($config['database'] ?? '');

        $info = [
            'driver' => $driver,
            'connection' => $connection,
            'database' => $database,
            'host' => $config['host'] ?? null,
            'size_bytes' => null,
            'table_count' => null,
        ];

        if ($driver === 'sqlite') {
            $path = $database;

            if (is_file($path)) {
                $info['size_bytes'] = filesize($path) ?: null;
            }

            $info['table_count'] = count(DB::select("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'"));
        }

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $summary = DB::selectOne(
                'SELECT COUNT(*) AS table_count, COALESCE(SUM(data_length + index_length), 0) AS size_bytes
                 FROM information_schema.tables
                 WHERE table_schema = ?',
                [$database],
            );

            $info['table_count'] = (int) ($summary->table_count ?? 0);
            $info['size_bytes'] = (int) ($summary->size_bytes ?? 0);
        }

        return $info;
    }

    /**
     * @return Collection<int, array{
     *     filename: string,
     *     driver: string,
     *     size_bytes: int,
     *     created_at: Carbon,
     * }>
     */
    public function listBackups(): Collection
    {
        $this->ensureDirectoryExists();

        return collect(Storage::disk($this->disk())->files($this->directory()))
            ->filter(fn (string $path): bool => $this->isBackupPath($path))
            ->map(function (string $path): array {
                $filename = basename($path);
                $absolute = Storage::disk($this->disk())->path($path);

                return [
                    'filename' => $filename,
                    'driver' => $this->driverFromFilename($filename),
                    'size_bytes' => is_file($absolute) ? (int) filesize($absolute) : 0,
                    'created_at' => Carbon::createFromTimestamp((int) filemtime($absolute)),
                ];
            })
            ->sortByDesc(fn (array $backup): int => $backup['created_at']->timestamp)
            ->values();
    }

    /**
     * @return array{filename: string, path: string, size_bytes: int}
     */
    public function createBackup(): array
    {
        $this->ensureDirectoryExists();

        $driver = $this->driver();
        $database = $this->databaseName();
        $timestamp = now()->format('Y-m-d_His');
        $extension = $driver === 'sqlite' ? 'sqlite' : 'sql';
        $filename = sprintf('%s_%s_%s.%s', $this->sanitizeSegment($database), $timestamp, $driver, $extension);
        $relativePath = $this->directory().'/'.$filename;
        $absolutePath = Storage::disk($this->disk())->path($relativePath);

        match ($driver) {
            'sqlite' => $this->createSqliteBackup($absolutePath),
            'mysql', 'mariadb' => $this->createMysqlBackup($absolutePath),
            default => throw new RuntimeException(__('app.admin.database.unsupported_driver')),
        };

        if (! is_file($absolutePath) || filesize($absolutePath) === 0) {
            @unlink($absolutePath);
            throw new RuntimeException(__('app.admin.database.create_failed'));
        }

        return [
            'filename' => $filename,
            'path' => $absolutePath,
            'size_bytes' => (int) filesize($absolutePath),
        ];
    }

    public function absolutePathFor(string $filename): string
    {
        $filename = basename($filename);

        if (! $this->isValidFilename($filename)) {
            throw new RuntimeException(__('app.admin.database.invalid_backup'));
        }

        $relativePath = $this->directory().'/'.$filename;
        $disk = Storage::disk($this->disk());

        if (! $disk->exists($relativePath)) {
            throw new RuntimeException(__('app.admin.database.backup_not_found'));
        }

        $absolute = $disk->path($relativePath);
        $backupRoot = realpath($disk->path($this->directory()));
        $resolved = realpath($absolute);

        if ($backupRoot === false || $resolved === false || ! str_starts_with($resolved, $backupRoot)) {
            throw new RuntimeException(__('app.admin.database.invalid_backup'));
        }

        return $resolved;
    }

    public function deleteBackup(string $filename): void
    {
        $relativePath = $this->directory().'/'.basename($filename);
        $this->absolutePathFor($filename);
        Storage::disk($this->disk())->delete($relativePath);
    }

    public function restoreBackup(string $filename): void
    {
        $path = $this->absolutePathFor($filename);
        $driver = $this->driverFromFilename(basename($filename));

        if ($driver !== $this->driver()) {
            throw new RuntimeException(__('app.admin.database.restore_driver_mismatch'));
        }

        Artisan::call('down', ['--retry' => 60]);

        try {
            match ($driver) {
                'sqlite' => $this->restoreSqliteBackup($path),
                'mysql', 'mariadb' => $this->restoreMysqlBackup($path),
                default => throw new RuntimeException(__('app.admin.database.unsupported_driver')),
            };
        } finally {
            Artisan::call('up');
        }
    }

    public function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }

        if ($bytes < 1024 * 1024) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return number_format($bytes / (1024 * 1024), 2).' MB';
    }

    protected function createSqliteBackup(string $destination): void
    {
        $database = (string) config('database.connections.'.$this->connectionName().'.database');

        if (! is_file($database)) {
            throw new RuntimeException(__('app.admin.database.source_missing'));
        }

        if (is_file($destination)) {
            @unlink($destination);
        }

        $pdo = DB::connection()->getPdo();
        $escaped = str_replace("'", "''", $destination);
        $pdo->exec("VACUUM INTO '{$escaped}'");
    }

    protected function createMysqlBackup(string $destination): void
    {
        $binary = $this->resolveBinary((string) config('database_backup.mysqldump_binary', 'mysqldump'));

        if ($binary !== null) {
            $this->createMysqlBackupViaBinary($destination, $binary);

            return;
        }

        $this->createMysqlBackupViaPhp($destination);
    }

    protected function createMysqlBackupViaBinary(string $destination, string $binary): void
    {
        $config = config('database.connections.'.$this->connectionName());

        $process = new Process([
            $binary,
            '--host='.($config['host'] ?? '127.0.0.1'),
            '--port='.($config['port'] ?? '3306'),
            '--user='.($config['username'] ?? 'root'),
            '--password='.($config['password'] ?? ''),
            '--single-transaction',
            '--routines',
            '--triggers',
            (string) ($config['database'] ?? ''),
        ]);
        $process->setTimeout(300);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException(trim($process->getErrorOutput()) ?: __('app.admin.database.create_failed'));
        }

        file_put_contents($destination, $process->getOutput());
    }

    protected function createMysqlBackupViaPhp(string $destination): void
    {
        $connection = DB::connection($this->connectionName());
        $database = $this->databaseName();
        $lines = [
            '-- Maremiya database backup',
            '-- Generated at '.now()->toDateTimeString(),
            'SET FOREIGN_KEY_CHECKS=0;',
            '',
        ];

        $tables = $connection->select('SHOW TABLES');
        $tableKey = 'Tables_in_'.$database;

        foreach ($tables as $tableRow) {
            $table = (string) ($tableRow->{$tableKey} ?? array_values((array) $tableRow)[0]);
            $create = $connection->selectOne('SHOW CREATE TABLE `'.$table.'`');
            $createSql = (string) ($create->{'Create Table'} ?? '');

            $lines[] = 'DROP TABLE IF EXISTS `'.$table.'`;';
            $lines[] = $createSql.';';
            $lines[] = '';

            foreach ($connection->cursor('SELECT * FROM `'.$table.'`') as $row) {
                $values = array_map(function ($value) use ($connection): string {
                    if ($value === null) {
                        return 'NULL';
                    }

                    return $connection->getPdo()->quote((string) $value);
                }, array_values((array) $row));

                $columns = array_map(fn (string $column): string => '`'.$column.'`', array_keys((array) $row));
                $lines[] = 'INSERT INTO `'.$table.'` ('.implode(', ', $columns).') VALUES ('.implode(', ', $values).');';
            }

            $lines[] = '';
        }

        $lines[] = 'SET FOREIGN_KEY_CHECKS=1;';

        file_put_contents($destination, implode(PHP_EOL, $lines));
    }

    protected function restoreSqliteBackup(string $backupPath): void
    {
        $database = (string) config('database.connections.'.$this->connectionName().'.database');
        DB::disconnect($this->connectionName());

        if (! copy($backupPath, $database)) {
            throw new RuntimeException(__('app.admin.database.restore_failed'));
        }
    }

    protected function restoreMysqlBackup(string $backupPath): void
    {
        $binary = $this->resolveBinary((string) config('database_backup.mysql_binary', 'mysql'));

        if ($binary !== null) {
            $this->restoreMysqlBackupViaBinary($backupPath, $binary);

            return;
        }

        $this->restoreMysqlBackupViaPhp($backupPath);
    }

    protected function restoreMysqlBackupViaBinary(string $backupPath, string $binary): void
    {
        $config = config('database.connections.'.$this->connectionName());

        $process = new Process([
            $binary,
            '--host='.($config['host'] ?? '127.0.0.1'),
            '--port='.($config['port'] ?? '3306'),
            '--user='.($config['username'] ?? 'root'),
            '--password='.($config['password'] ?? ''),
            (string) ($config['database'] ?? ''),
        ]);
        $process->setTimeout(300);
        $process->setInput(file_get_contents($backupPath) ?: '');
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException(trim($process->getErrorOutput()) ?: __('app.admin.database.restore_failed'));
        }
    }

    protected function restoreMysqlBackupViaPhp(string $backupPath): void
    {
        $connection = DB::connection($this->connectionName());
        $sql = file_get_contents($backupPath);

        if ($sql === false || trim($sql) === '') {
            throw new RuntimeException(__('app.admin.database.restore_failed'));
        }

        $connection->statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($this->splitSqlStatements($sql) as $statement) {
            $trimmed = trim($statement);

            if ($trimmed === '' || str_starts_with($trimmed, '--')) {
                continue;
            }

            $connection->unprepared($trimmed);
        }

        $connection->statement('SET FOREIGN_KEY_CHECKS=1');
    }

    protected function resolveBinary(string $binary): ?string
    {
        $candidates = array_unique([
            $binary,
            '/opt/homebrew/bin/'.$binary,
            '/usr/local/bin/'.$binary,
        ]);

        foreach ($candidates as $candidate) {
            if ($this->isExecutableBinary($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    protected function isExecutableBinary(string $path): bool
    {
        if ($path === '' || str_contains($path, '/')) {
            return is_file($path) && is_executable($path);
        }

        $process = new Process([$path, '--version']);
        $process->run();

        return $process->isSuccessful();
    }

    /**
     * @return list<string>
     */
    protected function splitSqlStatements(string $sql): array
    {
        return preg_split('/;\s*(?:\r?\n|$)/', $sql) ?: [];
    }

    protected function ensureDirectoryExists(): void
    {
        $path = Storage::disk($this->disk())->path($this->directory());

        if (! is_dir($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    protected function disk(): string
    {
        return (string) config('database_backup.disk', 'local');
    }

    protected function directory(): string
    {
        return trim((string) config('database_backup.directory', 'database-backups'), '/');
    }

    protected function databaseName(): string
    {
        return (string) config('database.connections.'.$this->connectionName().'.database');
    }

    protected function sanitizeSegment(string $value): string
    {
        $sanitized = preg_replace('/[^A-Za-z0-9_-]+/', '_', $value) ?? 'database';

        return trim($sanitized, '_') ?: 'database';
    }

    protected function isValidFilename(string $filename): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9_-]+\.(sql|sqlite)$/', $filename);
    }

    protected function isBackupPath(string $path): bool
    {
        return $this->isValidFilename(basename($path));
    }

    protected function driverFromFilename(string $filename): string
    {
        if (str_ends_with($filename, '.sqlite')) {
            return 'sqlite';
        }

        if (preg_match('/_([a-z]+)\.sql$/', $filename, $matches)) {
            return $matches[1];
        }

        return $this->driver();
    }
}
