<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RunBackendActionRequest;
use App\Services\Admin\BackendManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class BackendController extends Controller
{
    public function __construct(
        private readonly BackendManagementService $backend,
    ) {}

    public function index(): View
    {
        return view('admin.pages.backend', [
            'title' => __('app.admin.nav_backend'),
            'description' => __('app.admin.backend_description'),
            'overview' => $this->backend->overview(),
            'actions' => array_keys(config('backend_management.actions', [])),
        ]);
    }

    public function runAction(RunBackendActionRequest $request): RedirectResponse
    {
        $action = (string) $request->validated('action');

        try {
            $this->backend->runAction($action);

            if ($action === 'maintenance_down') {
                activity_log('admin.frontend_maintenance.enabled', __('app.admin.activity.descriptions.admin.frontend_maintenance.enabled'));
            } elseif ($action === 'maintenance_up') {
                activity_log('admin.frontend_maintenance.disabled', __('app.admin.activity.descriptions.admin.frontend_maintenance.disabled'));
            } else {
                activity_log(
                    'admin.backend.action',
                    __('app.admin.activity.descriptions.admin.backend.action', ['action' => $action]),
                    ['action' => $action],
                );
            }

            return redirect()
                ->route('admin.backend')
                ->with('success', __("app.admin.backend.actions.{$action}.success"));
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('admin.backend')
                ->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            Log::error('Backend action failed', [
                'action' => $action,
                'exception' => $exception,
            ]);

            return redirect()
                ->route('admin.backend')
                ->with('error', __('app.admin.backend.action_failed'));
        }
    }
}
