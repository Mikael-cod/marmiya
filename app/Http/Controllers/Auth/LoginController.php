<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SecurityLoginAttempt;
use App\Services\Admin\FrontendMaintenanceService;
use App\Services\Admin\SecuritySettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        private readonly FrontendMaintenanceService $frontendMaintenance,
        private readonly SecuritySettingService $securitySettings,
    ) {}

    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            if ($this->frontendMaintenance->isEnabled() && Auth::user()->isUser()) {
                Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();

                return view('auth.login', [
                    'frontendMaintenance' => true,
                ]);
            }

            return redirect()->route(Auth::user()->dashboardRoute());
        }

        return view('auth.login', [
            'frontendMaintenance' => $this->frontendMaintenance->isEnabled(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureIsNotRateLimited($request);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            if ($this->frontendMaintenance->isEnabled() && ! Auth::user()->isAdmin()) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                SecurityLoginAttempt::record(
                    (string) $credentials['email'],
                    (string) $request->ip(),
                    false,
                    (string) $request->userAgent(),
                );

                return back()
                    ->withInput($request->only('email', 'remember'))
                    ->with('frontend_maintenance', true)
                    ->withErrors(['email' => __('app.admin.backend.maintenance_user_blocked')]);
            }

            RateLimiter::clear($this->throttleKey($request));

            SecurityLoginAttempt::record(
                (string) $credentials['email'],
                (string) $request->ip(),
                true,
                (string) $request->userAgent(),
            );

            activity_log(
                'auth.login',
                __('app.admin.activity.descriptions.auth.login', [
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ]),
            );

            $request->session()->regenerate();

            return redirect()->intended(route(Auth::user()->dashboardRoute()));
        }

        RateLimiter::hit(
            $this->throttleKey($request),
            $this->lockoutSeconds(),
        );

        SecurityLoginAttempt::record(
            (string) $credentials['email'],
            (string) $request->ip(),
            false,
            (string) $request->userAgent(),
        );

        activity_log(
            'auth.login_failed',
            __('app.admin.activity.descriptions.auth.login_failed', [
                'email' => (string) $credentials['email'],
            ]),
            ['email' => (string) $credentials['email']],
        );

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => __('auth.failed')]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user !== null) {
            activity_log(
                'auth.logout',
                __('app.admin.activity.descriptions.auth.logout', [
                    'name' => $user->name,
                    'email' => $user->email,
                ]),
                actor: $user,
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function ensureIsNotRateLimited(Request $request): void
    {
        $key = $this->throttleKey($request);
        $maxAttempts = max(3, (int) $this->securitySettings->get('login_max_attempts', 5));

        if (! RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return;
        }

        $seconds = RateLimiter::availableIn($key);

        throw ValidationException::withMessages([
            'email' => [__('app.admin.security.throttle', [
                'minutes' => max(1, (int) ceil($seconds / 60)),
            ])],
        ]);
    }

    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower((string) $request->input('email')).'|'.(string) $request->ip());
    }

    protected function lockoutSeconds(): int
    {
        return max(60, (int) $this->securitySettings->get('login_lockout_minutes', 5) * 60);
    }
}
