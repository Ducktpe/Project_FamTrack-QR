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

        // Recent activity — latest 6 logs (exclude super_admin actions)
        $recentLogs         = AuditLog::with('user')
                                ->where('user_name', '!=', 'Super Administrator')
                                ->whereNot(function ($q) {
                                    $q->whereHas('user', function ($q2) {
                                        $q2->where('role', 'super_admin');
                                    });
                                })
                                ->latest()
                                ->take(6)
                                ->get();

        // Sector flag counts from households
        $flagCounts = [
            '4ps'         => Household::where('is_4ps_beneficiary', 1)->count(),
            'pwd'         => Household::where('is_pwd', 1)->count(),
            'senior'      => Household::where('is_senior', 1)->count(),
            'solo_parent' => Household::where('is_solo_parent', 1)->count(),
        ];

        // Upcoming + ongoing events list for the dashboard panel
        $upcomingEventsList = DistributionEvent::whereIn('status', ['upcoming', 'ongoing'])
            ->orderBy('event_date', 'asc')
            ->limit(8)
            ->get();

        // Encoder activity — households registered per encoder
        $encoderActivity = Household::select('encoded_by', \DB::raw('count(*) as total'))
            ->with('encoder:id,name')
            ->groupBy('encoded_by')
            ->orderByDesc('total')
            ->get();

        // Students per barangay (employment_status = 'Student' in family_member_details)
        $studentsByBarangay = \DB::table('family_member_details')
            ->join('family_members', 'family_member_details.family_member_id', '=', 'family_members.id')
            ->join('households', 'family_members.household_id', '=', 'households.id')
            ->where('family_member_details.employment_status', 'Student')
            ->groupBy('households.barangay')
            ->orderByDesc('student_count')
            ->selectRaw('households.barangay, COUNT(*) as student_count')
            ->pluck('student_count', 'barangay');

        $totalStudents = $studentsByBarangay->sum();

        // Students by school level (stored in job_title when employment_status = Student)
        $studentsByLevel = \DB::table('family_member_details')
            ->join('family_members', 'family_member_details.family_member_id', '=', 'family_members.id')
            ->join('households', 'family_members.household_id', '=', 'households.id')
            ->where('family_member_details.employment_status', 'Student')
            ->whereNotNull('family_member_details.job_title')
            ->groupBy('family_member_details.job_title')
            ->orderByDesc('level_count')
            ->selectRaw('family_member_details.job_title as level, COUNT(*) as level_count')
            ->pluck('level_count', 'level');

        // Employment status breakdown (all members)
        $employmentCounts = \DB::table('family_member_details')
            ->whereNotNull('employment_status')
            ->groupBy('employment_status')
            ->orderByDesc('total')
            ->selectRaw('employment_status, COUNT(*) as total')
            ->pluck('total', 'employment_status');

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
            'recentLogs',
            'flagCounts',
            'upcomingEventsList',
            'encoderActivity',
            'studentsByBarangay',
            'totalStudents',
            'studentsByLevel',
            'employmentCounts'
        ));
    }

    public function familyProfiles()
    {
        /**
         * Join household head's data from family_members + family_member_details.
         *
         * Why a raw join (not eager-loading):
         *   - sex, birthday, civil_status, educational_attainment are on
         *     family_members — these columns do NOT exist on households.
         *   - employment_status is on family_member_details.
         *   - Head is identified by is_family_head = 1 on family_members.
         *   - Subquery picks only one head per household (MIN id) to prevent
         *     duplicate rows when multiple family heads exist.
         *
         * Columns appended to each household row:
         *   head_sex, head_birthday, head_civil_status,
         *   head_educational_attainment, head_employment_status
         */
        $households = Household::select([
                'households.*',
                'fm.sex                    as head_sex',
                'fm.birthday               as head_birthday',
                'fm.civil_status           as head_civil_status',
                'fm.educational_attainment as head_educational_attainment',
                'fmd.employment_status     as head_employment_status',
            ])
            ->leftJoin(
                \DB::raw('(
                    SELECT fm_inner.*
                    FROM family_members fm_inner
                    INNER JOIN (
                        SELECT household_id, MIN(id) as min_id
                        FROM family_members
                        WHERE is_family_head = 1
                        GROUP BY household_id
                    ) first_head ON fm_inner.id = first_head.min_id
                ) as fm'),
                'fm.household_id', '=', 'households.id'
            )
            ->leftJoin('family_member_details as fmd', 'fmd.family_member_id', '=', 'fm.id')
            ->paginate(25);

        /**
         * IDs of households with at least one member whose
         * employment_status = 'Student' in family_member_details.
         * Cast to int — JS Array.includes() is strict so "5" !== 5.
         */
        $householdIdsWithStudents = \DB::table('family_member_details')
            ->join('family_members', 'family_member_details.family_member_id', '=', 'family_members.id')
            ->where('family_member_details.employment_status', 'Student')
            ->pluck('family_members.household_id')
            ->unique()
            ->values()
            ->map(fn($id) => (int) $id)
            ->toArray();

        return view('auditor.family-profiles', compact('households', 'householdIdsWithStudents'));
    }
}