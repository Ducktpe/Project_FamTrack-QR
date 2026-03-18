<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\DistributionEvent;
use App\Models\DistributionLog;
use App\Models\Household;

class AuditorController extends Controller
{
    public function dashboard()
    {
        // Household counts
        $totalHouseholds    = Household::count();
        $approvedHouseholds = Household::whereNotNull('approved_by')->count();
        $pendingHouseholds  = Household::whereNull('approved_by')->count();

        // Distribution counts
        $totalEvents        = DistributionEvent::count();
        $ongoingEvents      = DistributionEvent::where('status', 'ongoing')->count();
        $completedEvents    = DistributionEvent::where('status', 'completed')->count();
        $upcomingEvents     = DistributionEvent::where('status', 'upcoming')->count();
        $totalDistributed   = DistributionLog::count();

        // Audit log counts
        $totalLogs          = AuditLog::count();
        $highSeverityLogs   = AuditLog::where('severity', 'high')->count();
        $todayLogs          = AuditLog::whereDate('created_at', today())->count();

        // Recent activity — latest 6 logs
        $recentLogs         = AuditLog::latest()->take(6)->get();

        return view('auditor.dashboard', compact(
            'totalHouseholds',
            'approvedHouseholds',
            'pendingHouseholds',
            'totalEvents',
            'ongoingEvents',
            'completedEvents',
            'upcomingEvents',
            'totalDistributed',
            'totalLogs',
            'highSeverityLogs',
            'todayLogs',
            'recentLogs'
        ));
    }

    public function familyProfiles()
    {
        $households = Household::paginate(25);
        return view('auditor.family-profiles', compact('households'));
    }
}