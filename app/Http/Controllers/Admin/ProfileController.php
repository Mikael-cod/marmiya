<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAdminPasswordRequest;
use App\Http\Requests\Admin\UpdateAdminProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();

        return view('admin.pages.profile', [
            'title' => __('app.admin.nav_profile'),
            'description' => __('app.admin.profile_description'),
            'user' => $user,
        ]);
    }

    public function update(UpdateAdminProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->update($request->validated());

        activity_log(
            'admin.profile.updated',
            __('app.admin.activity.descriptions.admin.profile.updated', [
                'name' => $user->name,
                'email' => $user->email,
            ]),
        );

        return redirect()
            ->route('admin.profile')
            ->with('success', __('app.profile.updated_success'));
    }

    public function updatePassword(UpdateAdminPasswordRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->update([
            'password' => $request->validated('password'),
        ]);

        activity_log(
            'admin.profile.password_updated',
            __('app.admin.activity.descriptions.admin.profile.password_updated', [
                'name' => $user->name,
            ]),
        );

        return redirect()
            ->route('admin.profile')
            ->with('success', __('app.profile.password_updated_success'));
    }
}
