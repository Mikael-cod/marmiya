<?php

namespace App\Http\Middleware;

use App\Services\Admin\SecuritySettingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttpsWhenRequired
{
    public function __construct(
        private readonly SecuritySettingService $settings,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (
            ! $this->settings->get('force_https', false)
            || app()->environment('local')
            || $request->isSecure()
        ) {
            return $next($request);
        }

        return redirect()->secure($request->getRequestUri(), 301);
    }
}
