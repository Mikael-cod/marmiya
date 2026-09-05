<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
use App\Support\EthiopianCalendar;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();

        return view('user.pages.settings', [
            'title' => __('app.user.nav_settings'),
            'description' => __('app.user.settings_description'),
            'user' => $user,
            'perPage' => (int) ($user->preference('per_page') ?? config('user_settings.default_per_page', 15)),
            'reportEthYear' => old('report_eth_year', $user->preference('report_eth_year')),
            'reportEthMonth' => old('report_eth_month', $user->preference('report_eth_month')),
            'perPageOptions' => config('user_settings.per_page_options', [10, 15, 25, 50]),
            'ethYears' => range(EthiopianCalendar::currentYear() - 5, EthiopianCalendar::currentYear() + 1),
            'ethMonths' => EthiopianCalendar::monthNames(),
            'timezone' => EthiopianCalendar::timezone()->getName(),
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $preferences = array_merge($request->user()->preferences ?? [], [
            'per_page' => (int) $validated['per_page'],
            'report_eth_year' => filled($validated['report_eth_year'] ?? null)
                ? (int) $validated['report_eth_year']
                : null,
            'report_eth_month' => filled($validated['report_eth_month'] ?? null)
                ? (int) $validated['report_eth_month']
                : null,
        ]);

        $request->user()->update(['preferences' => $preferences]);

        return redirect()
            ->route('user.settings')
            ->with('success', __('app.settings.updated_success'));
    }
}
