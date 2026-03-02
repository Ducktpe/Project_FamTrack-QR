<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DistributionEvent;
use App\Models\FamilyMember;
use App\Models\Household;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Summary Counts ──────────────────────────────────────
        $totalHouseholds = Household::count();
        $totalMembers    = FamilyMember::count();
        $totalResidents  = $totalHouseholds + $totalMembers;

        $total4Ps    = Household::where('is_4ps_beneficiary', true)->count();

        $totalPwd    = Household::where('is_pwd', true)->count()
                     + FamilyMember::where('is_pwd', true)->count();

        $totalSeniors = Household::where('is_senior', true)->count()
                      + FamilyMember::whereNotNull('birthday')
                          ->whereDate('birthday', '<=', now()->subYears(60))
                          ->count();

        // ── Households per Barangay (for bar chart) ─────────────
        $householdsPerBarangay = Household::selectRaw('barangay, COUNT(*) as total')
            ->groupBy('barangay')
            ->orderBy('barangay')
            ->get();

        // ── Recent Distribution Events ───────────────────────────
        $recentEvents = DistributionEvent::latest('event_date')
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalResidents',
            'totalHouseholds',
            'total4Ps',
            'totalSeniors',
            'totalPwd',
            'householdsPerBarangay',
            'recentEvents'
        ));
    }
}