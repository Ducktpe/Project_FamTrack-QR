<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DistributionEvent;
use App\Models\FamilyMember;
use App\Models\FamilyMemberDetail;
use App\Models\Household;
use App\Models\QrCode;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── TOP SUMMARY STRIP ─────────────────────────────────────
        $totalMembers    = FamilyMember::count();
        $totalHouseholds = Household::count();
        $approvedCount   = Household::whereNotNull('approved_by')->count();
        $pendingCount    = Household::whereNull('approved_by')->count();

        $totalResidents  = $totalMembers;   // family_members = actual residents
        $total4Ps        = Household::where('is_4ps_beneficiary', true)->count();
        $totalPwd        = Household::where('is_pwd', true)->count();
        $totalSeniors    = Household::where('is_senior', true)->count();
        $totalSoloParent = Household::where('is_solo_parent', true)->count();
        $totalVulnerable = Household::where(function ($q) {
            $q->where('is_4ps_beneficiary', true)
              ->orWhere('is_pwd', true)
              ->orWhere('is_senior', true)
              ->orWhere('is_solo_parent', true);
        })->count();

        // ── QR CODES ──────────────────────────────────────────────
        $totalQr    = QrCode::count();
        $activeQr   = QrCode::where('is_active', true)->count();
        $inactiveQr = QrCode::where('is_active', false)->count();

        // QR split by scan_mode (family_head events vs household events)
        // A "family head QR" is a QR whose household has a family_head member
        // A "household QR" is every QR — since qr_codes.household_id is always set
        // We split by how many are linked to events using each scan_mode
        $householdScanEvents  = DistributionEvent::where('scan_mode', 'household')->count();
        $familyHeadScanEvents = DistributionEvent::where('scan_mode', 'family_head')->count();

        // ── DISTRIBUTION EVENTS ───────────────────────────────────
        $totalEvents     = DistributionEvent::count();
        $upcomingEvents  = DistributionEvent::where('status', 'upcoming')->count();
        $ongoingEvents   = DistributionEvent::where('status', 'ongoing')->count();
        $completedEvents = DistributionEvent::where('status', 'completed')->count();
        $cancelledEvents = DistributionEvent::where('status', 'cancelled')->count();

        // ── PLAN B — HOUSEHOLDS JSON (for JS cross-sector logic) ──
        // Includes all fields needed for Population, Vulnerable, QR sectors
        $householdsJson = Household::select(
            'id', 'barangay',
            'is_4ps_beneficiary as is_4ps',
            'is_pwd',
            'is_senior',
            'is_solo_parent as is_solo',
            'housing_type',
            'water_source',
            'approved_by'
        )->get()->map(function ($h) {
            return [
                'id'       => $h->id,
                'barangay' => $h->barangay,
                'is_4ps'   => (bool) $h->is_4ps,
                'is_pwd'   => (bool) $h->is_pwd,
                'is_senior'=> (bool) $h->is_senior,
                'is_solo'  => (bool) $h->is_solo,
                'housing'  => $h->housing_type,
                'water'    => $h->water_source,
                'approved' => !is_null($h->approved_by),
            ];
        });

        // ── PLAN B — FAMILY MEMBERS JSON ──────────────────────────
        $membersJson = FamilyMember::select(
            'id', 'household_id', 'sex', 'birthday', 'is_family_head'
        )->get()->map(function ($m) {
            return [
                'id'            => $m->id,
                'household_id'  => $m->household_id,
                'sex'           => $m->sex,
                'birthday'      => $m->birthday ? $m->birthday->format('Y-m-d') : null,
                'is_family_head'=> (bool) $m->is_family_head,
            ];
        });

        // ── PLAN B — MEMBER DETAILS JSON ──────────────────────────
        // Note: actual column is `vulnerable_sector`, not `philsys_status`
        $detailsJson = FamilyMemberDetail::select(
            'family_member_id', 'employment_status', 'vulnerable_sector'
        )->get()->map(function ($d) {
            return [
                'family_member_id'  => $d->family_member_id,
                'employment_status' => $d->employment_status,
                'vulnerable_sector' => $d->vulnerable_sector,
            ];
        });

        // ── PLAN B — EVENTS JSON (with lat/lng for map) ───────────
        $eventsJson = DistributionEvent::select(
            'id', 'event_name', 'status', 'scan_mode',
            'relief_type', 'target_barangay',
            'distribution_lat', 'distribution_lng',
            'distribution_location', 'event_date'
        )->latest('event_date')->get()->map(function ($e) {
            $brgy = $e->target_barangay;
            if (is_string($brgy)) {
                $decoded = json_decode($brgy, true);
                $brgy = is_array($decoded) ? $decoded : [$brgy];
            }
            return [
                'id'        => $e->id,
                'name'      => $e->event_name,
                'status'    => $e->status,
                'scan_mode' => $e->scan_mode,
                'relief'    => $e->relief_type,
                'barangay'  => $brgy ?? ['All Barangays'],
                'lat'       => $e->distribution_lat ? (float) $e->distribution_lat : null,
                'lng'       => $e->distribution_lng ? (float) $e->distribution_lng : null,
                'loc'       => $e->distribution_location,
                'date'      => $e->event_date
                                ? \Carbon\Carbon::parse($e->event_date)->format('M d, Y')
                                : '—',
                'date_raw'  => $e->event_date
                                ? \Carbon\Carbon::parse($e->event_date)->format('Y-m-d')
                                : null,
            ];
        });

        // ── PLAN B — QR CODES JSON ────────────────────────────────
        $qrJson = QrCode::select('id', 'household_id', 'is_active')
            ->get()->map(function ($q) {
                return [
                    'id'           => $q->id,
                    'household_id' => $q->household_id,
                    'is_active'    => (bool) $q->is_active,
                ];
            });

        // ── EXISTING BLADE DATA (kept for welcome card + quick nav) ──
        $householdsPerBarangay = Household::selectRaw('barangay, COUNT(*) as total')
            ->groupBy('barangay')->orderBy('barangay')->get();

        $recentEvents = DistributionEvent::latest('event_date')->take(8)->get();

        // Map events (with pins)
        $mapEvents = DistributionEvent::whereNotNull('distribution_lat')
            ->whereNotNull('distribution_lng')
            ->select('id','event_name','relief_type','target_barangay','status',
                     'started_at','ended_at','distribution_lat','distribution_lng',
                     'distribution_location','event_date')
            ->latest('event_date')->get();

        $dmUpcoming  = $mapEvents->where('status','upcoming')->count();
        $dmOngoing   = $mapEvents->where('status','ongoing')->count();
        $dmCompleted = $mapEvents->where('status','completed')->count();
        $dmCancelled = $mapEvents->where('status','cancelled')->count();
        $dmTotal     = $mapEvents->count();

        return view('admin.dashboard', compact(
            // Strip counts
            'totalResidents',
            'totalHouseholds',
            'approvedCount',
            'pendingCount',
            'total4Ps',
            'totalPwd',
            'totalSeniors',
            'totalSoloParent',
            'totalVulnerable',
            'totalQr',
            'activeQr',
            'inactiveQr',
            'totalEvents',
            'upcomingEvents',
            'ongoingEvents',
            'completedEvents',
            'cancelledEvents',
            'householdScanEvents',
            'familyHeadScanEvents',
            // Plan B JSON payloads
            'householdsJson',
            'membersJson',
            'detailsJson',
            'eventsJson',
            'qrJson',
            // Existing
            'householdsPerBarangay',
            'recentEvents',
            'mapEvents',
            'dmUpcoming',
            'dmOngoing',
            'dmCompleted',
            'dmCancelled',
            'dmTotal',
        ));
    }
}