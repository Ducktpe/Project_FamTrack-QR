<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\DistributionEvent;
use App\Models\DistributionLog;
use App\Models\DistributionReleasePhoto;
use App\Models\Household;
use App\Models\FamilyMember;
use App\Models\AuditLog;
use App\Models\ScanAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffScanController extends Controller
{
    /**
     * Show the scanner interface
     */
    public function index()
    {
        $events = DistributionEvent::ongoing()
            ->orderBy('event_date', 'desc')
            ->get();

        return view('staff.scan.index', compact('events'));
    }

    /**
     * Process scanned QR code
     */
    public function scan(Request $request)
    {
        $validated = $request->validate([
            'serial_code' => 'required|string',
            'event_id'    => 'required|exists:distribution_events,id',
        ]);

        $serialCode = trim($validated['serial_code']);
        $event      = DistributionEvent::findOrFail($validated['event_id']);
        $scanMode   = $event->scan_mode ?? 'household'; // 'household' | 'family_head'

        // ── STEP 1: QR TYPE VALIDATION ─────────────────────────────────────
        // Household QR  serial: NIC-TB-HH-2026-00001       (contains -HH-)
        // Family Head QR serial: NIC-TB-FH-2026-00001-M{id} (contains -FH-)

        $isHouseholdQr  = str_contains($serialCode, '-HH-');
        $isFamilyHeadQr = str_contains($serialCode, '-FH-');

        if ($scanMode === 'household' && !$isHouseholdQr) {
            return response()->json([
                'status'        => 'wrong_qr_type',
                'expected_mode' => 'household',
                'scanned_code'  => $serialCode,
                'event_name'    => $event->event_name,
                'message'       => 'This event requires a Household QR card. You scanned a Family Head QR.',
            ], 200);
        }

        if ($scanMode === 'family_head' && !$isFamilyHeadQr) {
            return response()->json([
                'status'        => 'wrong_qr_type',
                'expected_mode' => 'family_head',
                'scanned_code'  => $serialCode,
                'event_name'    => $event->event_name,
                'message'       => 'This event requires a Family Head personal QR card. You scanned a Household QR.',
            ], 200);
        }
        // ── END QR TYPE VALIDATION ──────────────────────────────────────────

        // ── STEP 2: RESOLVE HOUSEHOLD & FAMILY MEMBER ──────────────────────
        $familyMember = null;

        if ($isFamilyHeadQr) {
            // Convert FH serial back to HH serial to find the household.
            // New format includes a member suffix: NIC-TB-FH-2026-00001-M2
            // Strip the -M{id} suffix first, then swap -FH- → -HH-
            $baseSerial      = preg_replace('/-M\d+$/', '', $serialCode);
            $householdSerial = str_replace('-FH-', '-HH-', $baseSerial);

            $household = Household::where('serial_code', $householdSerial)
                ->with('members')
                ->first();

            if ($household) {
                // Find the family head member linked to this household
                $familyMember = FamilyMember::where('household_id', $household->id)
                    ->where('is_family_head', 1)
                    ->first();
            }
        } else {
            // Standard household QR
            $household = Household::where('serial_code', $serialCode)
                ->with('members')
                ->first();
        }

        if (!$household) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid QR Code. Household not found.',
            ], 404);
        }

        // Check if household is approved
        if (!$household->isApproved()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This household is not yet approved by Admin.',
            ], 400);
        }
        // ── END RESOLVE ─────────────────────────────────────────────────────

        // ── STEP 3: BARANGAY CHECK ──────────────────────────────────────────
        $targetList = is_array($event->target_barangay) ? $event->target_barangay : [];
        $skipCheck  = empty($targetList) || in_array('All Barangays', $targetList);

        if (!$skipCheck) {
            $targetList = array_map('trim', $targetList);
            if (!in_array(trim($household->barangay), $targetList)) {
                return response()->json([
                    'status'       => 'wrong_barangay',
                    'event_target' => implode(', ', $targetList),
                    'household'    => [
                        'name'     => $household->household_head_name,
                        'barangay' => $household->barangay,
                    ],
                ], 200);
            }
        }
        // ── END BARANGAY CHECK ──────────────────────────────────────────────

        // ── STEP 4: DUPLICATE CHECK ─────────────────────────────────────────
        // For household mode  → check by household_id (one per household)
        // For family_head mode → check by serial_code (one per family head QR)
        $existingLog = $scanMode === 'family_head'
            ? DistributionLog::where('event_id', $event->id)
                ->where('serial_code', $serialCode)
                ->first()
            : DistributionLog::where('event_id', $event->id)
                ->where('household_id', $household->id)
                ->first();

        if ($existingLog) {
            ScanAttempt::create([
                'event_id'     => $event->id,
                'household_id' => $household->id,
                'serial_code'  => $serialCode,
                'scanned_by'   => auth()->id(),
                'result'       => 'duplicate',
                'scanned_at'   => now(),
            ]);

            return response()->json([
                'status'  => 'duplicate',
                'message' => 'ALREADY RECEIVED',
                'household' => [
                    'name'        => $household->household_head_name,
                    'serial_code' => $serialCode,
                ],
                'previous_release' => [
                    'date'  => $existingLog->distributed_at->format('M d, Y h:i A'),
                    'staff' => $existingLog->staff->name ?? '—',
                ],
            ], 200);
        }
        // ── END DUPLICATE CHECK ─────────────────────────────────────────────

        // Return household info for confirmation
        return response()->json([
            'status'  => 'success',
            'message' => 'Household found. Ready to confirm release.',
            'household' => [
                'id'            => $household->id,
                'name'          => $household->household_head_name,
                'serial_code'   => $serialCode, // return scanned serial (may be FH)
                'address'       => "{$household->street_purok}, {$household->barangay}",
                'members_count' => $household->total_members,
                'is_4ps'        => $household->is_4ps_beneficiary,
                'is_pwd'        => $household->is_pwd,
                'is_senior'     => $household->is_senior,
                // Pass along for confirm() to use
                'family_member_id' => $familyMember?->id,
                'scan_mode'        => $scanMode,
            ],
            'relief_items' => $event->relief_items ?? [],
        ], 200);
    }

    /**
     * Confirm and record distribution.
     *
     * Expects multipart/form-data (not JSON) because a photo file is included.
     * Required new fields:
     *   recipient_photo  — JPEG/PNG captured by the blade's camera step
     *   photo_taken_at   — ISO-8601 timestamp recorded client-side at shutter moment
     *
     * On success, returns photo_path + photo_url so the blade can render a thumbnail.
     */
    public function confirm(Request $request)
    {
        $validated = $request->validate([
            'household_id'     => 'required|exists:households,id',
            'event_id'         => 'required|exists:distribution_events,id',
            'items_received'   => 'nullable|string',   // blade sends JSON.stringify'd string via FormData
            'goods_detail'     => 'nullable|string',
            'remarks'          => 'nullable|string',
            // ── Photo fields (mandatory for both QR modes) ──
            'recipient_photo'  => 'required|file|image|max:10240', // max 10 MB
            'photo_taken_at'   => 'required|string',
        ]);

        $household      = Household::findOrFail($validated['household_id']);
        $event          = DistributionEvent::findOrFail($validated['event_id']);
        $scanMode       = $event->scan_mode ?? 'household';
        $scannedSerial  = $request->input('serial_code', $household->serial_code);
        $familyMemberId = $request->input('family_member_id') ?: null;

        // ── RACE CONDITION DUPLICATE PROTECTION ────────────────────────────
        $existing = $scanMode === 'family_head'
            ? DistributionLog::where('event_id', $validated['event_id'])
                ->where('serial_code', $scannedSerial)
                ->first()
            : DistributionLog::where('event_id', $validated['event_id'])
                ->where('household_id', $household->id)
                ->first();

        if ($existing) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Duplicate detected. This household already received ayuda for this event.',
            ], 400);
        }
        // ── END DUPLICATE PROTECTION ────────────────────────────────────────

        // ── STORE RECIPIENT PHOTO ───────────────────────────────────────────
        // Path: storage/app/public/distribution_photos/YYYY/MM/<hash>.jpg
        // Accessible via: Storage::url($photoPath)  →  /storage/distribution_photos/…
        $photoPath = $request->file('recipient_photo')->store(
            'distribution_photos/' . now()->format('Y/m'),
            'public'
        );
        // ── END PHOTO STORAGE ───────────────────────────────────────────────

        // items_received arrives as a JSON string from FormData (blade uses JSON.stringify)
        $itemsReceived = null;
        if ($request->filled('items_received')) {
            $decoded = json_decode($request->input('items_received'), true);
            $itemsReceived = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }
        $itemsReceived = $itemsReceived ?? ($event->relief_items ?? null);

        // ── CREATE DISTRIBUTION LOG ─────────────────────────────────────────
        $log = DistributionLog::create([
            'event_id'         => $validated['event_id'],
            'household_id'     => $household->id,
            'family_member_id' => $scanMode === 'family_head' ? $familyMemberId : null,
            'serial_code'      => $scannedSerial,
            'distributed_by'   => auth()->id(),
            'distributed_at'   => now(),
            'items_received'   => $itemsReceived,
            'goods_detail'     => $validated['goods_detail'] ?? null,
            'remarks'          => $validated['remarks'] ?? null,
        ]);
        // ── END LOG ─────────────────────────────────────────────────────────

        // ── SAVE PHOTO RECORD ───────────────────────────────────────────────
        DistributionReleasePhoto::create([
            'distribution_log_id' => $log->id,
            'household_id'        => $household->id,
            'family_member_id'    => $scanMode === 'family_head' ? $familyMemberId : null,
            'qr_type'             => $scanMode === 'family_head' ? 'family_head' : 'household',
            'photo_path'          => $photoPath,
            'photo_taken_at'      => $validated['photo_taken_at'],
            'taken_by'            => auth()->id(),
        ]);
        // ── END PHOTO RECORD ─────────────────────────────────────────────────

        // ── SCAN ATTEMPT ────────────────────────────────────────────────────
        ScanAttempt::create([
            'event_id'     => $validated['event_id'],
            'household_id' => $household->id,
            'serial_code'  => $scannedSerial,
            'scanned_by'   => auth()->id(),
            'result'       => 'success',
            'scanned_at'   => now(),
        ]);
        // ── END SCAN ATTEMPT ────────────────────────────────────────────────

        // ── AUDIT LOG ───────────────────────────────────────────────────────
        AuditLog::log('distributed_ayuda', [
            'model'         => 'DistributionLog',
            'record_id'     => $log->id,
            'affected_name' => $household->household_head_name,
            'description'   => "Released ayuda to {$household->household_head_name} ({$scannedSerial}) via {$scanMode} mode",
            'new_values'    => [
                'household'        => $household->household_head_name,
                'serial_code'      => $scannedSerial,
                'scan_mode'        => $scanMode,
                'family_member_id' => $familyMemberId,
                'event_id'         => $validated['event_id'],
                'event_name'       => $event->event_name ?? null,
                'relief_type'      => $event->relief_type ?? null,
                'relief_items'     => $event->relief_items ?? null,
                'items_received'   => $log->items_received,
                'goods_detail'     => $log->goods_detail,
                'remarks'          => $log->remarks,
                'photo_path'       => $photoPath,
            ],
        ]);
        // ── END AUDIT LOG ───────────────────────────────────────────────────

        return response()->json([
            'status'     => 'success',
            'message'    => 'Distribution recorded successfully!',
            'photo_path' => $photoPath,
            'photo_url'  => Storage::url($photoPath),
            'log'        => [
                'id'        => $log->id,
                'household' => $household->household_head_name,
                'time'      => $log->distributed_at->format('h:i A'),
            ],
        ], 201);
    }

    /**
     * Show the staff's personal scan history
     * Also handles ?stats_only=1 for the scanner page counter fetch
     */
    public function scanHistory(Request $request)
    {
        $staffId = auth()->id();

        // ── stats_only used by the scanner blade to seed counters on load ──
        if ($request->boolean('stats_only')) {
            $eventId = $request->input('event_id');

            $attemptBase = ScanAttempt::where('scanned_by', $staffId)
                ->when($eventId,
                    fn($q, $id) => $q->where('event_id', $id),
                    fn($q)      => $q->whereDate('scanned_at', today())
                );

            $confirmedFromAttempts  = (clone $attemptBase)->where('result', 'success')->count();
            $duplicatesFromAttempts = (clone $attemptBase)->where('result', 'duplicate')->count();

            if ($confirmedFromAttempts === 0) {
                $confirmedToday = DistributionLog::where('distributed_by', $staffId)
                    ->when($eventId,
                        fn($q, $id) => $q->where('event_id', $id),
                        fn($q)      => $q->whereDate('distributed_at', today())
                    )
                    ->count();
            } else {
                $confirmedToday = $confirmedFromAttempts;
            }

            return response()->json([
                'confirmed_today'  => $confirmedToday,
                'duplicates_today' => $duplicatesFromAttempts,
            ]);
        }
        // ── END stats_only ────────────────────────────────────────────────

        $search   = $request->input('search');
        $eventId  = $request->input('event_id');
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        $logs = DistributionLog::where('distributed_by', $staffId)
            ->with(['household', 'event', 'releasePhoto', 'familyMember'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('serial_code', 'like', "%{$search}%")
                      ->orWhereHas('household', fn($h) =>
                          $h->where('household_head_name', 'like', "%{$search}%")
                      );
                });
            })
            ->when($eventId,  fn($q, $id) => $q->where('event_id', $id))
            ->when($dateFrom, fn($q, $d)  => $q->whereDate('distributed_at', '>=', $d))
            ->when($dateTo,   fn($q, $d)  => $q->whereDate('distributed_at', '<=', $d))
            ->orderBy('distributed_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $totalScans  = DistributionLog::where('distributed_by', $staffId)->count();
        $todayScans  = DistributionLog::where('distributed_by', $staffId)
                        ->whereDate('distributed_at', today())->count();
        $totalEvents = DistributionLog::where('distributed_by', $staffId)
                        ->distinct('event_id')->count('event_id');
        $lastScanAt  = DistributionLog::where('distributed_by', $staffId)
                        ->latest('distributed_at')->value('distributed_at');

        $events = DistributionEvent::whereHas('logs', fn($q) =>
            $q->where('distributed_by', $staffId)
        )->orderBy('event_name')->get();

        return view('staff.scan-history', compact(
            'logs',
            'totalScans',
            'todayScans',
            'totalEvents',
            'lastScanAt',
            'events'
        ));
    }
}