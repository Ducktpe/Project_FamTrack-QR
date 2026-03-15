<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AdminTrailController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest('created_at');

        // Role filter (via related user)
        if ($request->filled('role')) {
            $query->whereHas('user', fn($q) => $q->where('role', $request->role));
        }

        // User filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Action filter
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Severity filter
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('user_name',      'like', "%{$s}%")
                  ->orWhere('action',       'like', "%{$s}%")
                  ->orWhere('description',  'like', "%{$s}%")
                  ->orWhere('affected_name','like', "%{$s}%");
            });
        }

        $logs = $query->paginate(50)->withQueryString();

        // Dropdown data
        $users      = User::orderBy('name')->get(['id', 'name', 'email', 'role']);
        $actions    = AuditLog::select('action')->distinct()->orderBy('action')->pluck('action');
        $categories = ['household', 'qr_code', 'distribution', 'auth', 'general'];

        // Summary counts (always global, unfiltered)
        $totalAll     = AuditLog::count();
        $totalAdmin   = AuditLog::whereHas('user', fn($q) => $q->where('role', 'admin'))->count();
        $totalStaff   = AuditLog::whereHas('user', fn($q) => $q->where('role', 'staff'))->count();
        $totalEncoder = AuditLog::whereHas('user', fn($q) => $q->where('role', 'encoder'))->count();
        $totalAuditor = AuditLog::whereHas('user', fn($q) => $q->where('role', 'auditor'))->count();
        $sevHigh      = AuditLog::where('severity', 'high')->count();
        $sevMedium    = AuditLog::where('severity', 'medium')->count();
        $sevLow       = AuditLog::where('severity', 'low')->count();

        return view('admin.traillog.trail', [
            'logs'         => $logs,
            'users'        => User::orderBy('name')->get(),
            'categories'   => AuditLog::distinct()->pluck('category')->filter()->sort()->values(),
            'totalAll'     => $totalAll,
            'totalAdmin'   => $totalAdmin,
            'totalStaff'   => $totalStaff,
            'totalEncoder' => $totalEncoder,
            'totalAuditor' => $totalAuditor,
            'sevHigh'      => $sevHigh,
            'sevMedium'    => $sevMedium,
            'sevLow'       => $sevLow,
        ]);
    }
}