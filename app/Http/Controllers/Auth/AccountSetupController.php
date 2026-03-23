<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AccountSetupController extends Controller
{
    /**
     * Show the account setup form.
     * URL: /account-setup/{id}/{token}
     */
    public function show(int $id, string $token): View|RedirectResponse
    {
        $user = User::findOrFail($id);

        // Already completed setup
        if ($user->is_setup_complete) {
            return redirect()->route('login')
                ->withErrors(['email' => 'This account has already been set up. Please log in.']);
        }

        // Invalid or expired token
        if (! $user->hasValidInviteToken($token)) {
            return view('auth.invite-expired', ['user' => $user]);
        }

        return view('auth.account-setup', [
            'user'       => $user,
            'token'      => $token,
            'loginEmail' => $user->email,
            'roleLabel'  => $user->roleLabel(),
        ]);
    }

    /**
     * Handle the account setup form submission.
     */
    public function store(Request $request, int $id, string $token): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Guard against replay after setup
        if ($user->is_setup_complete) {
            return redirect()->route('login')
                ->withErrors(['email' => 'This account has already been set up.']);
        }

        // Re-validate token on POST
        if (! $user->hasValidInviteToken($token)) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Your invite link has expired. Please contact the Super Administrator.']);
        }

        $validated = $request->validate([
            'name'                  => ['required', 'string', 'min:2', 'max:255'],
            'password'              => ['required', 'confirmed', Password::min(8)
                ->mixedCase()
                ->numbers()],
        ]);

        // Finalise the account
        $user->update([
            'name'              => $validated['name'],
            'password'          => Hash::make($validated['password']),
            'status'            => 'active',
            'is_setup_complete' => true,
            'invite_token'      => null,      // Invalidate token after use
            'invite_expires_at' => null,
        ]);

        // Audit log
        AuditLog::log('account_setup_complete', [
            'model'         => 'User',
            'record_id'     => $user->id,
            'affected_name' => $user->name,
            'category'      => 'auth',
            'severity'      => 'medium',
            'description'   => "{$user->name} completed account setup ({$user->role})",
        ]);

        return redirect()->route('login')
            ->with('success', 'Your account has been set up successfully! You can now log in using your system email: ' . $user->email);
    }
}