<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    public function index(Request $request)
    {
        // Get all super_admin user IDs — confirmed column: users.role (enum)
        $superAdminIds = User::where('role', 'super_admin')->pluck('id');

        $query = AuditLog::with('user')->latest('created_at')
            ->where(function ($q) use ($superAdminIds) {
                $q->whereNotIn('user_id', $superAdminIds)
                  ->orWhereNull('user_id'); // keep System/null-user entries
            });

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('user_name',      'like', "%{$s}%")
                  ->orWhere('action',       'like', "%{$s}%")
                  ->orWhere('description',  'like', "%{$s}%")
                  ->orWhere('affected_name','like', "%{$s}%")
                  ->orWhere('model',        'like', "%{$s}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50)->withQueryString();

        // Summary counts — super_admin excluded, null user_id (System) kept
        $base = AuditLog::where(function ($q) use ($superAdminIds) {
            $q->whereNotIn('user_id', $superAdminIds)
              ->orWhereNull('user_id');
        });

        $totalLogs         = (clone $base)->count();
        $authCount         = (clone $base)->where('category', 'auth')->count();
        $householdCount    = (clone $base)->where('category', 'household')->count();
        $distributionCount = (clone $base)->where('category', 'distribution')->count();
        $highSeverityCount = (clone $base)->where('severity', 'high')->count();

        return view('auditor.audit-trail', compact(
            'logs',
            'totalLogs',
            'authCount',
            'householdCount',
            'distributionCount',
            'highSeverityCount'
        ));
    }
}