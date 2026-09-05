<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RestoreDatabaseBackupRequest;
use App\Services\Admin\DatabaseBackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DatabaseBackupController extends Controller
{
    public function __construct(
        private readonly DatabaseBackupService $backups,
    ) {}

    public function index(): View
    {
        $databaseInfo = $this->backups->databaseInfo();
        $backupItems = $this->backups->listBackups()->map(function (array $backup): array {
            $backup['size_label'] = $this->backups->formatBytes($backup['size_bytes']);

            return $backup;
        });

        if ($databaseInfo['size_bytes'] !== null) {
            $databaseInfo['size_label'] = $this->backups->formatBytes((int) $databaseInfo['size_bytes']);
        }

        return view('admin.pages.database', [
            'title' => __('app.admin.nav_database'),
            'description' => __('app.admin.database_description'),
            'databaseInfo' => $databaseInfo,
            'backups' => $backupItems,
            'restoreBackup' => request()->string('restore')->toString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (! $request->user()?->isAdmin()) {
            abort(403);
        }

        try {
            $backup = $this->backups->createBackup();

            activity_log(
                'admin.database.backup_created',
                __('app.admin.activity.descriptions.admin.database.backup_created', [
                    'filename' => $backup['filename'],
                ]),
                ['filename' => $backup['filename']],
            );

            return redirect()
                ->route('admin.database')
                ->with('success', __('app.admin.database.created_success', [
                    'filename' => $backup['filename'],
                    'size' => $this->backups->formatBytes($backup['size_bytes']),
                ]));
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('admin.database')
                ->with('error', $exception->getMessage());
        } catch (\Throwable $exception) {
            Log::error('Database backup failed', ['exception' => $exception]);

            return redirect()
                ->route('admin.database')
                ->with('error', __('app.admin.database.create_failed'));
        }
    }

    public function download(string $backup): BinaryFileResponse|RedirectResponse
    {
        try {
            $path = $this->backups->absolutePathFor($backup);

            return response()->download($path, basename($path));
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('admin.database')
                ->with('error', $exception->getMessage());
        }
    }

    public function destroy(string $backup): RedirectResponse
    {
        try {
            $this->backups->deleteBackup($backup);

            activity_log(
                'admin.database.backup_deleted',
                __('app.admin.activity.descriptions.admin.database.backup_deleted', [
                    'filename' => basename($backup),
                ]),
                ['filename' => basename($backup)],
            );

            return redirect()
                ->route('admin.database')
                ->with('success', __('app.admin.database.deleted_success'));
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('admin.database')
                ->with('error', $exception->getMessage());
        }
    }

    public function restore(RestoreDatabaseBackupRequest $request, string $backup): RedirectResponse
    {
        try {
            $this->backups->restoreBackup($backup);

            activity_log(
                'admin.database.backup_restored',
                __('app.admin.activity.descriptions.admin.database.backup_restored', [
                    'filename' => basename($backup),
                ]),
                ['filename' => basename($backup)],
            );

            return redirect()
                ->route('admin.database')
                ->with('success', __('app.admin.database.restored_success'));
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('admin.database', ['restore' => basename($backup)])
                ->with('error', $exception->getMessage());
        } catch (\Throwable $exception) {
            Log::error('Database restore failed', ['exception' => $exception]);

            return redirect()
                ->route('admin.database', ['restore' => basename($backup)])
                ->with('error', __('app.admin.database.restore_failed'));
        }
    }
}
