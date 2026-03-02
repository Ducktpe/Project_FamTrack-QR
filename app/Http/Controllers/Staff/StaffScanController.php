<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\DistributionEvent;
use App\Models\DistributionLog;
use App\Models\Household;
use App\Models\AuditLog;
use Illuminate\Http\Request;

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

        // Find household by serial code
        $household = Household::where('serial_code', $validated['serial_code'])
            ->with('members')
            ->first();

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

        // ── BARANGAY CHECK ─────────────────────────────────────────────────
        // Prevent scanning households outside the event's target barangay.
        // target_barangay is stored as "All Barangays" OR "Sabang, Bucana, ..."
        $event      = DistributionEvent::findOrFail($validated['event_id']);
        $targetRaw  = trim($event->target_barangay ?? '');

        // Skip check entirely if target_barangay is empty (legacy events) or "All Barangays"
        $skipCheck  = empty($targetRaw) || $targetRaw === 'All Barangays';

        if (!$skipCheck) {
            $targetList = array_map('trim', explode(',', $targetRaw));

            // Filter out any empty strings from the list before comparing
            $targetList = array_filter($targetList, fn($b) => $b !== '');

            if (!empty($targetList) && !in_array(trim($household->barangay), $targetList)) {
                return response()->json([
                    'status'       => 'wrong_barangay',
                    'event_target' => $targetRaw,
                    'household'    => [
                        'name'     => $household->household_head_name,
                        'barangay' => $household->barangay,
                    ],
                ], 200);
            }
        }
        // ── END BARANGAY CHECK ─────────────────────────────────────────────

        // Check for duplicate release
        $existingLog = DistributionLog::where('event_id', $validated['event_id'])
            ->where('household_id', $household->id)
            ->first();

        if ($existingLog) {
            return response()->json([
                'status'  => 'duplicate',
                'message' => 'ALREADY RECEIVED',
                'household' => [
                    'name'         => $household->household_head_name,
                    'serial_code'  => $household->serial_code,
                    'members_count'=> $household->total_members,
                ],
                'previous_release' => [
                    'date'  => $existingLog->distributed_at->format('M d, Y h:i A'),
                    'staff' => $existingLog->staff->name,
                ],
            ], 200);
        }

        // Return household info for confirmation
        return response()->json([
            'status'  => 'success',
            'message' => 'Household found. Ready to confirm release.',
            'household' => [
                'id'            => $household->id,
                'name'          => $household->household_head_name,
                'serial_code'   => $household->serial_code,
                'address'       => "{$household->street_purok}, {$household->barangay}",
                'members_count' => $household->total_members,
                'is_4ps'        => $household->is_4ps_beneficiary,
                'is_pwd'        => $household->is_pwd,
                'is_senior'     => $household->is_senior,
            ],
        ], 200);
    }

    /**
     * Confirm and record distribution
     */
    public function confirm(Request $request)
    {
        $validated = $request->validate([
            'household_id' => 'required|exists:households,id',
            'event_id'     => 'required|exists:distribution_events,id',
            'goods_detail' => 'nullable|string',
            'remarks'      => 'nullable|string',
        ]);

        $household = Household::findOrFail($validated['household_id']);

        // Double-check for duplicate (race condition protection)
        $existing = DistributionLog::where('event_id', $validated['event_id'])
            ->where('household_id', $household->id)
            ->first();

        if ($existing) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Duplicate detected. This household already received ayuda for this event.',
            ], 400);
        }

        // Create distribution log
        $log = DistributionLog::create([
            'event_id'        => $validated['event_id'],
            'household_id'    => $household->id,
            'serial_code'     => $household->serial_code,
            'distributed_by'  => auth()->id(),
            'distributed_at'  => now(),
            'goods_detail'    => $validated['goods_detail'] ?? null,
            'remarks'         => $validated['remarks'] ?? null,
        ]);

        // Audit log
        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'action'     => 'distributed_ayuda',
            'model'      => 'DistributionLog',
            'record_id'  => $log->id,
            'new_values' => [
                'household'   => $household->household_head_name,
                'serial_code' => $household->serial_code,
                'event_id'    => $validated['event_id'],
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Distribution recorded successfully!',
            'log'     => [
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

        // ── FIX: stats_only used by the scanner blade to seed counters on load ──
        if ($request->boolean('stats_only')) {
            $confirmedToday = DistributionLog::where('distributed_by', $staffId)
                ->whereDate('distributed_at', today())
                ->count();

            // Blocked duplicates are not stored in DB (they're stopped before insert).
            // The blade tracks new duplicates locally in-session and adds to this base.
            return response()->json([
                'confirmed_today'  => $confirmedToday,
                'duplicates_today' => 0,
            ]);
        }
        // ── END FIX ───────────────────────────────────────────────────────────

        $search   = $request->input('search');
        $eventId  = $request->input('event_id');
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        $logs = DistributionLog::where('distributed_by', $staffId)
            ->with(['household', 'event'])
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

        // Stats — always unfiltered totals for this staff member
        $totalScans  = DistributionLog::where('distributed_by', $staffId)->count();
        $todayScans  = DistributionLog::where('distributed_by', $staffId)
                        ->whereDate('distributed_at', today())->count();
        $totalEvents = DistributionLog::where('distributed_by', $staffId)
                        ->distinct('event_id')->count('event_id');
        $lastScanAt  = DistributionLog::where('distributed_by', $staffId)
                        ->latest('distributed_at')->value('distributed_at');

        // Only events this staff member has actually worked — for the filter dropdown
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