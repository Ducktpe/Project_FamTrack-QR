<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Household;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ResidentController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->input('search');
        $type     = $request->input('type');
        $sex      = $request->input('sex');
        $barangay = $request->input('barangay');
        $tag      = $request->input('tag');

        // ── Build the flat residents list ────────────────────────────────
        $residents = collect();

        // -- Household Heads --
        $headsQuery = Household::query();

        if ($search) {
            $headsQuery->where(function ($q) use ($search) {
                $q->where('household_head_name', 'like', "%{$search}%")
                  ->orWhere('barangay', 'like', "%{$search}%");
            });
        }
        if ($sex)      $headsQuery->where('sex', $sex);
        if ($barangay) $headsQuery->where('barangay', $barangay);

        // Tag filters on heads
        // Validate tag input
        $allowedTags = ['4ps', 'pwd', 'senior', 'solo'];
        if ($tag && !in_array($tag, $allowedTags)) $tag = null;

        if ($tag === '4ps')    $headsQuery->where('is_4ps_beneficiary', true);
        if ($tag === 'pwd')    $headsQuery->where('is_pwd', true);
        if ($tag === 'senior') {
            $headsQuery->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereNotNull('birthday')
                       ->whereDate('birthday', '<=', now()->subYears(60));
                })->orWhere(function ($q2) {
                    $q2->whereNull('birthday')->where('is_senior', true);
                });
            });
        }
        if ($tag === 'solo')   $headsQuery->where('is_solo_parent', true);

        if (!$type || $type === 'head') {
            foreach ($headsQuery->get() as $hh) {
                $age = $hh->birthday ? $hh->birthday->age : null;
                $residents->push([
                    'type'           => 'head',
                    'name'           => $hh->household_head_name,
                    'sex'            => $hh->sex,
                    'age'            => $age,
                    'barangay'       => $hh->barangay,
                    'contact_number' => $hh->contact_number,
                    'household_head' => null,
                    'relationship'   => 'Head',
                    'is_4ps'         => $hh->is_4ps_beneficiary,
                    'is_pwd'         => $hh->is_pwd,
                    'is_senior'      => $hh->is_senior,
                    'is_solo'        => $hh->is_solo_parent,
                ]);
            }
        }

        // -- Family Members --
        if (!$type || $type === 'member') {
            $membersQuery = FamilyMember::with('household');

            if ($search) {
                $membersQuery->where(function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                      ->orWhereHas('household', fn($hq) =>
                          $hq->where('barangay', 'like', "%{$search}%")
                      );
                });
            }
            if ($sex) $membersQuery->where('sex', $sex);
            if ($barangay) {
                $membersQuery->whereHas('household', fn($q) =>
                    $q->where('barangay', $barangay)
                );
            }
            if ($tag === 'pwd')    $membersQuery->where('is_pwd', true);
            if ($tag === 'senior') {
                // senior = age >= 60, computed from birthday
                $membersQuery->whereNotNull('birthday')
                             ->whereDate('birthday', '<=', now()->subYears(60));
            }
            // 4ps and solo_parent live on the household, not the member
            if ($tag === '4ps') {
                $membersQuery->whereHas('household', fn($q) =>
                    $q->where('is_4ps_beneficiary', true)
                );
            }
            if ($tag === 'solo') {
                $membersQuery->whereHas('household', fn($q) =>
                    $q->where('is_solo_parent', true)
                );
            }

            foreach ($membersQuery->get() as $member) {
                $age = $member->birthday ? $member->birthday->age : null;
                $residents->push([
                    'type'           => 'member',
                    'name'           => $member->full_name,
                    'sex'            => $member->sex,
                    'age'            => $age,
                    'barangay'       => $member->household->barangay ?? '—',
                    'contact_number' => null,
                    'household_head' => $member->household->household_head_name ?? '—',
                    'relationship'   => $member->relationship,
                    'is_4ps'         => $member->household->is_4ps_beneficiary ?? false,
                    'is_pwd'         => $member->is_pwd,
                    'is_senior'      => $age !== null && $age >= 60,
                    'is_solo'        => $member->household->is_solo_parent ?? false,
                ]);
            }
        }

        // ── Summary stats (always unfiltered) ───────────────────────────
        $totalHeads     = Household::count();
        $totalMembers   = FamilyMember::count();
        $totalResidents = $totalHeads + $totalMembers;
        $total4Ps       = Household::where('is_4ps_beneficiary', true)->count();
        $totalPwd       = Household::where('is_pwd', true)->count()
                        + FamilyMember::where('is_pwd', true)->count();
        $totalSeniors   = Household::where('is_senior', true)->count()
                        + FamilyMember::whereNotNull('birthday')
                            ->whereDate('birthday', '<=', now()->subYears(60))
                            ->count();

        // ── Barangay list for filter dropdown ───────────────────────────
        $barangays = Household::distinct()->orderBy('barangay')->pluck('barangay');

        // ── Manual pagination ────────────────────────────────────────────
        $perPage     = 50;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $paged       = $residents->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $residents = new LengthAwarePaginator(
            $paged,
            $residents->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.residents.index', compact(
            'residents',
            'totalResidents',
            'totalHeads',
            'totalMembers',
            'total4Ps',
            'totalSeniors',
            'totalPwd',
            'barangays'
        ));
    }
}