<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes:
     *   ->middleware('role:admin')
     *   ->middleware('role:super_admin')
     *   ->middleware('role:admin,encoder')   ← comma-separated = any of these roles allowed
     *
     * Role hierarchy (highest to lowest):
     *   super_admin → admin → encoder / staff / auditor
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // ── 1. Must be authenticated ─────────────────────────────
        if (! $user) {
            return redirect()->route('login');
        }

        // ── 2. Account must be active ────────────────────────────
        if (! $user->isActive()) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been deactivated. Please contact the Super Administrator.']);
        }

        // ── 3. Super admin bypasses ALL role-restricted routes ───
        //    EXCEPT routes that are explicitly super_admin-only,
        //    which are guarded by ->middleware('role:super_admin').
        //    Super admin never accidentally lands on role:super_admin
        //    routes via the bypass — they are always in the allowed list.
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // ── 4. Check if user's role is in the allowed list ───────
        if (empty($roles) || in_array($user->role, $roles, true)) {
            return $next($request);
        }

        // ── 5. Unauthorized — redirect to their own dashboard ────
        $dashboardRoute = match($user->role) {
            'admin'   => 'admin.dashboard',
            'encoder' => 'encoder.dashboard',
            'staff'   => 'staff.dashboard',
            'auditor' => 'auditor.dashboard',
            default   => 'login',
        };

        return redirect()->route($dashboardRoute)
            ->withErrors(['role' => 'You do not have permission to access that page.']);
    }
}