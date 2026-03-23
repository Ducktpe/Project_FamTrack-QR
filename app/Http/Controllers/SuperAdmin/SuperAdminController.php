<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Mail\AccountInviteMail;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SuperAdminController extends Controller
{
    // ── DASHBOARD ────────────────────────────────────────────

    public function dashboard()
    {
        $stats = [
            'total'    => User::whereNotIn('role', ['super_admin'])->count(),
            'active'   => User::whereNotIn('role', ['super_admin'])->where('status', 'active')->count(),
            'inactive' => User::whereNotIn('role', ['super_admin'])->where('status', 'inactive')->count(),
            'pending'  => User::whereNotIn('role', ['super_admin'])->where('is_setup_complete', false)->count(),
            'deleted'  => User::withTrashed()->whereNotIn('role', ['super_admin'])->whereNotNull('deleted_at')->count(),
            'by_role'  => User::whereNotIn('role', ['super_admin'])
                ->selectRaw('role, count(*) as count')
                ->groupBy('role')
                ->pluck('count', 'role'),
        ];

        $recentUsers = User::whereNotIn('role', ['super_admin'])
            ->with('creator')->latest()->take(5)->get();

        return view('superadmin.dashboard', compact('stats', 'recentUsers'));
    }

    // ── ACCOUNT LIST ─────────────────────────────────────────

    public function accountsIndex(Request $request)
    {
        $query = User::whereNotIn('role', ['super_admin'])->with('creator');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('personal_email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role'))   $query->where('role', $request->role);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('setup'))  $query->where('is_setup_complete', $request->setup === 'complete');

        $users        = $query->latest()->paginate(15)->withQueryString();
        $pendingUsers = User::whereNotIn('role', ['super_admin'])->where('is_setup_complete', false)->count();

        return view('superadmin.accounts.index', compact('users', 'pendingUsers'));
    }

    // ── VIEW USER DETAILS ────────────────────────────────────

    public function accountsShow(User $user)
    {
        $pendingUsers = User::whereNotIn('role', ['super_admin'])->where('is_setup_complete', false)->count();
        $user->load('creator');

        $auditLogs = AuditLog::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere(function ($q2) use ($user) {
                      $q2->where('model', 'User')->where('record_id', $user->id);
                  });
            })
            ->latest()
            ->take(10)
            ->get();

        return view('superadmin.accounts.show', compact('user', 'pendingUsers', 'auditLogs'));
    }

    // ── CREATE ACCOUNT FORM ──────────────────────────────────

    public function accountsCreate()
    {
        $pendingUsers = User::whereNotIn('role', ['super_admin'])->where('is_setup_complete', false)->count();
        return view('superadmin.accounts.create', compact('pendingUsers'));
    }

    // ── STORE — INVITE FLOW ──────────────────────────────────

    public function accountsStore(Request $request)
    {
        $validated = $request->validate([
            'personal_email' => ['required', 'email', 'unique:users,personal_email'],
            'role'           => ['required', 'in:admin,encoder,staff,auditor'],
        ]);

        $code        = User::generateAccountCode($validated['role']);
        $systemEmail = User::generateSystemEmail($validated['role'], $code);

        $user = User::create([
            'name'              => 'Pending Setup',
            'email'             => $systemEmail,
            'personal_email'    => $validated['personal_email'],
            'account_code'      => $code,
            'role'              => $validated['role'],
            'status'            => 'inactive',
            'password'          => bcrypt(\Illuminate\Support\Str::random(32)),
            'created_by'        => auth()->id(),
            'is_setup_complete' => false,
        ]);

        $plainToken = $user->generateInviteToken();
        $setupUrl   = route('account.setup.show', ['id' => $user->id, 'token' => $plainToken]);

        try {
            Mail::to($user->personal_email)->send(new AccountInviteMail($user, $setupUrl));
        } catch (\Exception $e) {
            \Log::error('AccountInviteMail failed: ' . $e->getMessage());
        }

        AuditLog::log('sent_account_invite', [
            'model'         => 'User',
            'record_id'     => $user->id,
            'affected_name' => $systemEmail,
            'category'      => 'auth',
            'severity'      => 'medium',
            'description'   => "Invite sent to {$validated['personal_email']} for {$validated['role']} ({$systemEmail})",
            'new_values'    => ['email' => $systemEmail, 'personal_email' => $validated['personal_email'], 'role' => $validated['role']],
        ]);

        return redirect()
            ->route('superadmin.accounts.index')
            ->with('success', "✅ Invite sent to {$validated['personal_email']}. Login email: {$systemEmail}");
    }

    // ── RESEND INVITE ────────────────────────────────────────

    public function accountsResendInvite(User $user)
    {
        if ($user->is_setup_complete) {
            return back()->with('error', 'This account has already been set up.');
        }

        $plainToken = $user->generateInviteToken();
        $setupUrl   = route('account.setup.show', ['id' => $user->id, 'token' => $plainToken]);

        try {
            Mail::to($user->personal_email)->send(new AccountInviteMail($user, $setupUrl));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send invite. Check mail config.');
        }

        AuditLog::log('resent_account_invite', [
            'model'         => 'User', 'record_id' => $user->id,
            'affected_name' => $user->email, 'category' => 'auth', 'severity' => 'low',
            'description'   => "Invite resent to {$user->personal_email} ({$user->email})",
        ]);

        return back()->with('success', "Invite resent to {$user->personal_email}.");
    }

    // ── TOGGLE STATUS ────────────────────────────────────────

    public function accountsToggleStatus(User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Cannot modify a Super Admin account.');
        }

        $old          = $user->status;
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        AuditLog::log('toggled_user_status', [
            'model' => 'User', 'record_id' => $user->id, 'affected_name' => $user->email,
            'description' => "Status changed from {$old} to {$user->status}",
            'old_values'  => ['status' => $old], 'new_values' => ['status' => $user->status],
        ]);

        return back()->with('success', "Account {$user->email} " . ($user->status === 'active' ? 'activated' : 'deactivated') . '.');
    }

    // ── SOFT DELETE ──────────────────────────────────────────

    public function accountsDestroy(User $user)
    {
        if ($user->isSuperAdmin() || $user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete this account.');
        }

        $email = $user->email;
        $role  = $user->roleLabel();
        $name  = $user->name;

        $user->update(['status' => 'inactive']);
        $user->delete(); // Soft delete — sets deleted_at only

        AuditLog::log('deleted_user_account', [
            'model'         => 'User', 'record_id' => $user->id, 'affected_name' => $email,
            'category'      => 'auth', 'severity'  => 'high',
            'description'   => "Archived account {$email} ({$role}) — {$name}",
            'old_values'    => ['email' => $email, 'role' => $role, 'name' => $name],
        ]);

        return back()->with('success', "Account {$email} has been archived. You can restore it from Archived Accounts.");
    }

    // ── ARCHIVED ACCOUNTS ────────────────────────────────────

    public function accountsArchived(Request $request)
    {
        $pendingUsers = User::whereNotIn('role', ['super_admin'])->where('is_setup_complete', false)->count();

        $query = User::onlyTrashed()
            ->whereNotIn('role', ['super_admin'])
            ->with('creator');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('personal_email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) $query->where('role', $request->role);

        $archivedUsers = $query->latest('deleted_at')->paginate(15)->withQueryString();

        return view('superadmin.accounts.archived', compact('archivedUsers', 'pendingUsers'));
    }

    // ── RESTORE ──────────────────────────────────────────────

    public function accountsRestore(int $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        $user->update(['status' => 'inactive']);

        AuditLog::log('restored_user_account', [
            'model'         => 'User', 'record_id' => $user->id, 'affected_name' => $user->email,
            'category'      => 'auth', 'severity'  => 'medium',
            'description'   => "Restored archived account {$user->email} ({$user->roleLabel()})",
        ]);

        return back()->with('success', "Account {$user->email} restored. Activate it from Account Management.");
    }

    // ── FORCE DELETE ─────────────────────────────────────────

    public function accountsForceDelete(int $id)
    {
        $user  = User::onlyTrashed()->findOrFail($id);
        $email = $user->email;
        $role  = $user->roleLabel();

        AuditLog::log('permanently_deleted_user', [
            'model'         => 'User', 'record_id' => $user->id, 'affected_name' => $email,
            'category'      => 'auth', 'severity'  => 'high',
            'description'   => "Permanently deleted {$email} ({$role})",
            'old_values'    => $user->toArray(),
        ]);

        $user->forceDelete();

        return back()->with('success', "Account {$email} permanently deleted.");
    }

    // ── ROLE PERMISSIONS ─────────────────────────────────────

    public function rolesIndex()
    {
        $pendingUsers = User::whereNotIn('role', ['super_admin'])->where('is_setup_complete', false)->count();
        $roles = [
            'admin'   => ['label' => 'Administrator', 'badge' => 'badge-admin',   'icon' => '🛡️', 'count' => User::where('role','admin')->count(),   'privileges' => (new User(['role'=>'admin']))->rolePrivileges()],
            'encoder' => ['label' => 'Encoder',        'badge' => 'badge-encoder', 'icon' => '📝', 'count' => User::where('role','encoder')->count(), 'privileges' => (new User(['role'=>'encoder']))->rolePrivileges()],
            'staff'   => ['label' => 'Staff',          'badge' => 'badge-staff',   'icon' => '📡', 'count' => User::where('role','staff')->count(),   'privileges' => (new User(['role'=>'staff']))->rolePrivileges()],
            'auditor' => ['label' => 'Auditor',        'badge' => 'badge-auditor', 'icon' => '🔍', 'count' => User::where('role','auditor')->count(), 'privileges' => (new User(['role'=>'auditor']))->rolePrivileges()],
        ];
        return view('superadmin.roles.index', compact('roles', 'pendingUsers'));
    }

    // ── TRAIL LOGS ───────────────────────────────────────────

    public function trailsAdvanced(Request $request)
    {
        $pendingUsers = User::whereNotIn('role', ['super_admin'])->where('is_setup_complete', false)->count();
        $query        = AuditLog::with('user')->latest('created_at');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('action', 'like', '%' . $request->search . '%')
                  ->orWhere('user_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('severity')) $query->where('severity', $request->severity);

        $logs = $query->paginate(25)->withQueryString();
        return view('superadmin.trails.advanced', compact('logs', 'pendingUsers'));
    }

    public function trailsIndex()
    {
        $pendingUsers = User::whereNotIn('role', ['super_admin'])->where('is_setup_complete', false)->count();
        $logs         = AuditLog::with('user')->latest('created_at')->paginate(20)->withQueryString();
        return view('superadmin.trails.index', compact('logs', 'pendingUsers'));
    }
}