<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Household;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResidentController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->get('search');
        $scope    = $request->get('scope', 'all'); // all | name | barangay | serial | age | employment | relationship | student
        $type     = $request->get('type');       // head | member | ''
        $sex      = $request->get('sex');         // Male | Female | ''
        $barangay = $request->get('barangay');
        $tag      = $request->get('tag');         // 4ps | pwd | senior | solo | student | lgbtqia
        $ageGroup = $request->get('age_group');   // child | adult | senior
        $sort     = $request->get('sort', 'name');
        $dir      = $request->get('dir', 'asc') === 'desc' ? 'desc' : 'asc';

        // --- Build query over family_members joined to households ---
        $query = FamilyMember::query()
            ->select([
                'family_members.id',
                'family_members.household_id',
                'family_members.is_family_head',
                'family_members.full_name',
                'family_members.relationship',
                'family_members.sex',
                'family_members.birthday',
                'family_members.age',
                'family_members.is_pwd',
                'family_members.is_senior_citizen',
                'family_members.occupation',
                'households.household_head_name',
                'households.contact_number',
                'households.barangay',
                'households.serial_code',
                'households.is_4ps_beneficiary',
                'households.is_solo_parent',
                'fmd.employment_status',
                'fmd.is_lgbtqia',
            ])
            ->join('households', 'households.id', '=', 'family_members.household_id')
            ->leftJoin('family_member_details as fmd', 'fmd.family_member_id', '=', 'family_members.id');

        // --- Filters ---
        if ($search) {
            if ($scope === 'student') {
                // Student scope: filter to students whose name matches the search term
                $query->where('fmd.employment_status', 'Student')
                      ->where('family_members.full_name', 'like', "%{$search}%");
            } elseif ($scope === 'name') {
                $query->where('family_members.full_name', 'like', "%{$search}%");
            } elseif ($scope === 'barangay') {
                $query->where('households.barangay', 'like', "%{$search}%");
            } elseif ($scope === 'serial') {
                $query->where('households.serial_code', 'like', "%{$search}%");
            } elseif ($scope === 'age') {
                $query->where('family_members.age', (int) $search);
            } elseif ($scope === 'employment') {
                $query->where('family_members.occupation', 'like', "%{$search}%");
            } elseif ($scope === 'relationship') {
                $query->where('family_members.relationship', 'like', "%{$search}%");
            } elseif ($scope === 'civil_status') {
                $query->where('fmd.civil_status', 'like', "%{$search}%");
            } else {
                // all fields
                $query->where(function ($q) use ($search) {
                    $q->where('family_members.full_name', 'like', "%{$search}%")
                      ->orWhere('households.barangay', 'like', "%{$search}%")
                      ->orWhere('households.serial_code', 'like', "%{$search}%")
                      ->orWhere('households.household_head_name', 'like', "%{$search}%")
                      ->orWhere('family_members.occupation', 'like', "%{$search}%")
                      ->orWhere('family_members.relationship', 'like', "%{$search}%");
                });
            }
        }

        if ($type === 'head') {
            $query->where('family_members.is_family_head', 1);
        } elseif ($type === 'member') {
            $query->where('family_members.is_family_head', 0);
        }

        if ($sex) {
            $query->where('family_members.sex', $sex);
        }

        if ($barangay) {
            $query->where('households.barangay', $barangay);
        }

        if ($tag === '4ps') {
            $query->where('households.is_4ps_beneficiary', 1);
        } elseif ($tag === 'pwd') {
            $query->where('family_members.is_pwd', 1);
        } elseif ($tag === 'senior') {
            $query->where('family_members.is_senior_citizen', 1);
        } elseif ($tag === 'solo') {
            $query->where('households.is_solo_parent', 1);
        } elseif ($tag === 'student') {
            $query->where('fmd.employment_status', 'Student');
        } elseif ($tag === 'lgbtqia') {
            $query->where('fmd.is_lgbtqia', 1);
        }

        if ($ageGroup === 'child') {
            $query->where('family_members.age', '<', 18);
        } elseif ($ageGroup === 'adult') {
            $query->whereBetween('family_members.age', [18, 59]);
        } elseif ($ageGroup === 'senior') {
            $query->where('family_members.age', '>=', 60);
        }

        // --- Sort ---
        $sortMap = [
            'name'     => 'family_members.full_name',
            'age'      => 'family_members.birthday',
            'barangay' => 'households.barangay',
        ];
        $sortCol = $sortMap[$sort] ?? 'family_members.full_name';
        // For age, sort by birthday in reverse direction so "older" = higher age
        $sortDir = ($sort === 'age') ? ($dir === 'asc' ? 'desc' : 'asc') : $dir;
        $query->orderBy($sortCol, $sortDir);

        // Paginate — preserve all query params
        $residents = $query->paginate(50)->withQueryString();

        // --- Transform into flat array for blade ---
        $residents->through(function ($m) {
            return [
                'household_id'   => $m->household_id,
                'name'           => $m->full_name,
                'type'           => $m->is_family_head ? 'head' : 'member',
                'sex'            => $m->sex,
                'age'            => $m->age,
                'barangay'       => $m->barangay,
                'household_head' => $m->household_head_name,
                'relationship'   => $m->relationship,
                'serial_code'    => $m->serial_code,
                'contact_number' => $m->contact_number,
                'occupation'     => $m->occupation,
                'is_4ps'         => (bool) $m->is_4ps_beneficiary,
                'is_pwd'         => (bool) $m->is_pwd,
                'is_senior'      => (bool) $m->is_senior_citizen,
                'is_solo'        => (bool) $m->is_solo_parent,
                'is_student'     => $m->employment_status === 'Student',
                'is_lgbtqia'     => (bool) ($m->is_lgbtqia ?? false),
            ];
        });

        // --- Summary stats (always counts full dataset, not filtered) ---
        $totalResidents  = FamilyMember::count();
        $totalHeads      = FamilyMember::where('is_family_head', 1)->count();
        $total4Ps        = Household::where('is_4ps_beneficiary', 1)->count();
        $totalSeniors    = FamilyMember::where('is_senior_citizen', 1)->count();
        $totalPwd        = FamilyMember::where('is_pwd', 1)->count();
        $totalSoloParents = Household::where('is_solo_parent', 1)->count();
        $totalStudents    = DB::table('family_member_details')
            ->where('employment_status', 'Student')
            ->count();

        // --- Barangay list for filter dropdown ---
        $barangays = Household::select('barangay')
            ->distinct()
            ->orderBy('barangay')
            ->pluck('barangay');

        // --- Pre-built JSON for mobile drawer (avoids complex closure inside @json in Blade) ---
        $residentsJson = $residents->getCollection()->map(function ($p) {
            return [
                'name'           => $p['name'],
                'type'           => $p['type'],
                'sex'            => $p['sex'],
                'age'            => $p['age'] ?? '—',
                'barangay'       => $p['barangay'] ?? '—',
                'relationship'   => $p['relationship'] ?? ($p['type'] === 'head' ? 'Head' : '—'),
                'occupation'     => $p['occupation'] ?? '',
                'serial_code'    => $p['serial_code'] ?? '',
                'household_head' => $p['household_head'] ?? '',
                'contact_number' => $p['contact_number'] ?? '',
                'household_id'   => $p['household_id'],
                'is_4ps'         => (bool) $p['is_4ps'],
                'is_pwd'         => (bool) $p['is_pwd'],
                'is_senior'      => (bool) $p['is_senior'],
                'is_solo'        => (bool) ($p['is_solo'] ?? false),
                'is_student'     => (bool) ($p['is_student'] ?? false),
                'is_lgbtqia'     => (bool) ($p['is_lgbtqia'] ?? false),
            ];
        })->values();

        return view('admin.residents.index', compact(
            'residents',
            'residentsJson',
            'barangays',
            'totalResidents',
            'totalHeads',
            'total4Ps',
            'totalSeniors',
            'totalPwd',
            'totalSoloParents',
            'totalStudents'
        ));
    }
}