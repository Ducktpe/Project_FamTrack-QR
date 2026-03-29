<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Models\User;

class PasswordResetLinkController extends Controller
{
    /**
     * Show the forgot password form.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle the forgot password form.
     *
     * Users enter their PERSONAL GMAIL — we look up their account
     * by personal_email, generate a token, and send the reset link
     * to their Gmail.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Find user by personal Gmail
        $user = User::where('personal_email', $request->email)->first();

        // Also check system email as fallback (for super admin etc.)
        if (! $user) {
            $user = User::where('email', $request->email)->first();
        }

        // Always return success message even if not found (security best practice)
        if (! $user) {
            return back()->with('status', 'If that email is registered in our system, a password reset link has been sent.');
        }

        // Delete any existing token for this user
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        // Generate a secure token
        $token = Str::random(64);

        // Store hashed token
        DB::table('password_reset_tokens')->insert([
            'email'      => $user->email,      // Store against system email
            'token'      => Hash::make($token),
            'created_at' => now(),
        ]);

        // Build the reset URL using system email + token
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], false));

        // Send email to their PERSONAL GMAIL
        try {
            Mail::send('emails.password-reset', [
                'user'     => $user,
                'resetUrl' => $resetUrl,
                'expiry'   => '60 minutes',
            ], function ($mail) use ($user) {
                $mail->to($user->personal_email ?? $user->email)
                     ->subject('[MDRRMO Naic] Password Reset Request');
            });
        } catch (\Exception $e) {
            \Log::error('Password reset mail failed: ' . $e->getMessage());
        }

        return back()->with('status', 'A password reset link has been sent to your registered Gmail address.');
    }
}