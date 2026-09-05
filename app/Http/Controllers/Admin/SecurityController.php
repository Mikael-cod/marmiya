<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSecuritySettingsRequest;
use App\Services\Admin\SecurityOverviewService;
use App\Services\Admin\SecuritySettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SecurityController extends Controller
{
    public function __construct(
        private readonly SecuritySettingService $settings,
        private readonly SecurityOverviewService $overview,
    ) {}

    public function edit(Request $request): View
    {
        $this->settings->ensureDefaultsExist();

        return view('admin.pages.security', [
            'title' => __('app.admin.nav_security'),
            'description' => __('app.admin.security_description'),
            'settings' => $this->settings->all(),
            'overview' => $this->overview->overview($request),
        ]);
    }

    public function update(UpdateSecuritySettingsRequest $request): RedirectResponse
    {
        $this->settings->update($request->validated());

        activity_log('admin.security.updated', __('app.admin.activity.descriptions.admin.security.updated'));

        return redirect()
            ->route('admin.security')
            ->with('success', __('app.admin.security.updated_success'));
    }
}
