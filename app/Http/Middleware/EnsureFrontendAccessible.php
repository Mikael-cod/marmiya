<?php

namespace App\Http\Middleware;

use App\Services\Admin\FrontendMaintenanceService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureFrontendAccessible
{
    public function __construct(
        private readonly FrontendMaintenanceService $frontendMaintenance,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->frontendMaintenance->isEnabled()) {
            return $next($request);
        }

        if ($request->user()?->isAdmin()) {
            return $next($request);
        }

        if ($request->user()?->isUser()) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with('frontend_maintenance', true);
        }

        return $next($request);
    }
}
