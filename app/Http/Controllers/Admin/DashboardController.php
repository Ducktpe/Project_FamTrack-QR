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

        // ── Students per Barangay ──────────────────────────────────────
        $studentsByBarangay = \DB::table('family_member_details')
            ->join('family_members', 'family_member_details.family_member_id', '=', 'family_members.id')
            ->join('households', 'family_members.household_id', '=', 'households.id')
            ->where('family_member_details.employment_status', 'Student')
            ->groupBy('households.barangay')
            ->orderByDesc('student_count')
            ->selectRaw('households.barangay, COUNT(*) as student_count')
            ->pluck('student_count', 'barangay');

        // ── Students by School Level (stored in job_title when Student) ──
        $studentsByLevel = \DB::table('family_member_details')
            ->join('family_members', 'family_member_details.family_member_id', '=', 'family_members.id')
            ->join('households', 'family_members.household_id', '=', 'households.id')
            ->where('family_member_details.employment_status', 'Student')
            ->whereNotNull('family_member_details.job_title')
            ->groupBy('family_member_details.job_title')
            ->orderByDesc('level_count')
            ->selectRaw('family_member_details.job_title as level, COUNT(*) as level_count')
            ->pluck('level_count', 'level');

        // ── Employment Status Breakdown (all members) ─────────────────
        $employmentCounts = \DB::table('family_member_details')
            ->whereNotNull('employment_status')
            ->groupBy('employment_status')
            ->orderByDesc('total')
            ->selectRaw('employment_status, COUNT(*) as total')
            ->pluck('total', 'employment_status');

        return view('admin.dashboard', compact(
            'totalResidents',
            'totalHouseholds',
            'total4Ps',
            'totalSeniors',
            'totalPwd',
            'householdsPerBarangay',
            'recentEvents',
            'studentsByBarangay',
            'studentsByLevel',
            'employmentCounts'
        ));
    }
}