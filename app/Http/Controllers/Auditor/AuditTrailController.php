<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::latest('created_at');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('user_name',     'like', "%{$s}%")
                  ->orWhere('action',      'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhere('affected_name','like', "%{$s}%")
                  ->orWhere('model',       'like', "%{$s}%");
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

        // Summary counts (always unfiltered)
        $totalLogs         = AuditLog::count();
        $authCount         = AuditLog::where('category', 'auth')->count();
        $householdCount    = AuditLog::where('category', 'household')->count();
        $distributionCount = AuditLog::where('category', 'distribution')->count();
        $highSeverityCount = AuditLog::where('severity', 'high')->count();

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