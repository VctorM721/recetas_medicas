<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Redirección por rol
                $user = Auth::user();
                if ($user && $user->role === 'admin') {
                    return redirect()->route('doctores.index');
                }
                return redirect()->route('patients.index');
                // Alternativa base: return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}