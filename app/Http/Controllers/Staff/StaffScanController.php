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
        // Get ongoing events only
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
            'event_id' => 'required|exists:distribution_events,id',
        ]);

        // Find household by serial code
        $household = Household::where('serial_code', $validated['serial_code'])
            ->with('members')
            ->first();

        if (!$household) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid QR Code. Household not found.',
            ], 404);
        }

        // Check if household is approved
        if (!$household->isApproved()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This household is not yet approved by Admin.',
            ], 400);
        }

        // Check for duplicate release
        $existingLog = DistributionLog::where('event_id', $validated['event_id'])
            ->where('household_id', $household->id)
            ->first();

        if ($existingLog) {
            return response()->json([
                'status' => 'duplicate',
                'message' => 'ALREADY RECEIVED',
                'household' => [
                    'name' => $household->household_head_name,
                    'serial_code' => $household->serial_code,
                    'members_count' => $household->total_members,
                ],
                'previous_release' => [
                    'date' => $existingLog->distributed_at->format('M d, Y h:i A'),
                    'staff' => $existingLog->staff->name,
                ],
            ], 200);
        }

        // Return household info for confirmation
        return response()->json([
            'status' => 'success',
            'message' => 'Household found. Ready to confirm release.',
            'household' => [
                'id' => $household->id,
                'name' => $household->household_head_name,
                'serial_code' => $household->serial_code,
                'address' => "{$household->street_purok}, {$household->barangay}",
                'members_count' => $household->total_members,
                'is_4ps' => $household->is_4ps_beneficiary,
                'is_pwd' => $household->is_pwd,
                'is_senior' => $household->is_senior,
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
            'event_id' => 'required|exists:distribution_events,id',
            'goods_detail' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $household = Household::findOrFail($validated['household_id']);

        // Double-check for duplicate (race condition protection)
        $existing = DistributionLog::where('event_id', $validated['event_id'])
            ->where('household_id', $household->id)
            ->first();

        if ($existing) {
            return response()->json([
                'status' => 'error',
                'message' => 'Duplicate detected. This household already received ayuda for this event.',
            ], 400);
        }

        // Create distribution log
        $log = DistributionLog::create([
            'event_id' => $validated['event_id'],
            'household_id' => $household->id,
            'serial_code' => $household->serial_code,
            'distributed_by' => auth()->id(),
            'distributed_at' => now(),
            'goods_detail' => $validated['goods_detail'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
        ]);

        // Audit log
        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'action' => 'distributed_ayuda',
            'model' => 'DistributionLog',
            'record_id' => $log->id,
            'new_values' => [
                'household' => $household->household_head_name,
                'serial_code' => $household->serial_code,
                'event_id' => $validated['event_id'],
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Distribution recorded successfully!',
            'log' => [
                'id' => $log->id,
                'household' => $household->household_head_name,
                'time' => $log->distributed_at->format('h:i A'),
            ],
        ], 201);
    }
}
