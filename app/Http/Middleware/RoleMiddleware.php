<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->status === 'inactive') {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been deactivated.']);
        }

        if (!in_array($user->role, $roles)) {
            return redirect()->route($user->role . '.dashboard')
                ->withErrors(['access' => 'You do not have permission to access that page.']);
        }

        return $next($request);
    }
}