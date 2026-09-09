<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSetup
{
    public function handle(Request $request, Closure $next): Response
    {
        $hasUsers = User::exists();

        // Kein Benutzer vorhanden -> Setup-Wizard erzwingen
        if (! $hasUsers && ! $request->routeIs('setup.*') && ! $request->routeIs('login')) {
            return redirect()->route('setup.show');
        }

        // Setup bereits abgeschlossen -> Wizard sperren
        if ($hasUsers && $request->routeIs('setup.*')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
