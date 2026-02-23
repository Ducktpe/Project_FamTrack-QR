<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // Block inactive accounts
        if ($user->status === 'inactive') {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Your account has been deactivated.',
            ]);
        }

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Log the login
        \App\Models\AuditLog::create([
            'user_id'    => $user->id,
            'user_name'  => $user->name,
            'action'     => 'login',
            'model'      => 'User',
            'record_id'  => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Redirect by role
        return match($user->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'encoder' => redirect()->route('encoder.dashboard'),
            'staff'   => redirect()->route('staff.dashboard'),
            'auditor' => redirect()->route('auditor.dashboard'),
            default   => redirect('/'),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
