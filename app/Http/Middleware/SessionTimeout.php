<?php

namespace App\Http\Middleware;

use App\Services\SettingsService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    public function __construct(private SettingsService $settings)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $timeout = $this->settings->int('session_timeout', 120);
            $lastActivity = session('last_activity');

            if ($lastActivity && Carbon::parse($lastActivity)->addMinutes($timeout)->isPast()) {
                auth()->guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('status', __('auth.session_expired'));
            }

            session(['last_activity' => now()]);
        }

        return $next($request);
    }
}
