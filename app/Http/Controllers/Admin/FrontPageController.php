<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateFrontPageSettingsRequest;
use App\Services\Admin\FrontPageSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FrontPageController extends Controller
{
    public function __construct(
        private readonly FrontPageSettingService $settings,
    ) {}

    public function edit(): View
    {
        $this->settings->ensureDefaultsExist();

        return view('admin.pages.front-pages', [
            'title' => __('app.admin.nav_front_pages'),
            'description' => __('app.admin.front_pages_description'),
            'settings' => $this->settings->all(),
            'themeOptions' => config('front_pages.default_theme_options', ['light', 'dark', 'system']),
        ]);
    }

    public function update(UpdateFrontPageSettingsRequest $request): RedirectResponse
    {
        $this->settings->update($request->validated());

        activity_log('admin.front_pages.updated', __('app.admin.activity.descriptions.admin.front_pages.updated'));

        return redirect()
            ->route('admin.front-pages')
            ->with('success', __('app.admin.front_pages.updated_success'));
    }
}
