<?php

namespace App\Http\Middleware;

use App\Services\SettingsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceTwoFactor
{
    public function __construct(private SettingsService $settings)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $this->settings->bool('two_factor_enforced', false) && ! $user->hasEnabledTwoFactor()) {
            if (! $request->routeIs('profile.show', 'two-factor.*')) {
                return redirect()->route('profile.show')->with('status', 'two-factor-required');
            }
        }

        return $next($request);
    }
}
