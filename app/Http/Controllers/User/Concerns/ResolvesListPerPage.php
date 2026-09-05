<?php

namespace App\Http\Controllers\User\Concerns;

use Illuminate\Http\Request;

trait ResolvesListPerPage
{
    protected function resolvePerPage(Request $request): int
    {
        $default = (int) ($request->user()?->preference('per_page')
            ?? config('user_settings.default_per_page', 15));

        $perPage = (int) $request->input('per_page', $default);
        $allowed = config('user_settings.per_page_options', [10, 15, 25, 50]);

        return in_array($perPage, $allowed, true)
            ? $perPage
            : config('user_settings.default_per_page', 15);
    }
}
