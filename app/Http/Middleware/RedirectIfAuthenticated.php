<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * If the user is already authenticated and tries to visit /login,
     * redirect them to their own dashboard instead.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // Redirect to the correct dashboard based on role
                return match($user->role) {
                    'super_admin' => redirect()->route('superadmin.dashboard'),
                    'admin'       => redirect()->route('admin.dashboard'),
                    'encoder'     => redirect()->route('encoder.dashboard'),
                    'staff'       => redirect()->route('staff.dashboard'),
                    'auditor'     => redirect()->route('auditor.dashboard'),
                    default       => redirect('/'),
                };
            }
        }

        return $next($request);
    }
}