<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DistributionEvent;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AdminDistributionEventController extends Controller
{
    public function index()
    {
        $events = DistributionEvent::with('creator')
            ->orderBy('event_date', 'desc')
            ->paginate(20);

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_name'            => 'required|string|max:255',
            'relief_type'           => 'required|array|min:1',
            'relief_type.*'         => 'required|string|max:255',
            'target_barangay'       => 'required|array|min:1',
            'target_barangay.*'     => 'required|string|max:100',
            'scan_mode'             => 'required|in:household,family_head',
            'event_date'            => 'nullable|date',
            'goods_detail'          => 'nullable|string',
            'started_at'            => 'required|date',
            'ended_at'              => 'required|date|after_or_equal:started_at',
            'status'                => 'required|in:upcoming,ongoing,completed,cancelled',
            'distribution_lat'      => 'nullable|numeric|between:-90,90',
            'distribution_lng'      => 'nullable|numeric|between:-180,180',
            'distribution_location' => 'nullable|string|max:255',
            'distribution_dms'      => 'nullable|string|max:100',
        ]);

        $reliefType     = implode(', ', $request->input('relief_type'));
        $targetBarangay = $request->input('target_barangay');

        $reliefItems = [];
        $unitMap = [
            'feminine_hygiene_wash' => 'btl','sanitary_pads'=>'pack',
            'tissue_wipes'=>'pack','underwear'=>'pcs',
            'alcohol'=>'btl','bandaid'=>'box',
            'bandage'=>'roll','betadine'=>'btl',
            'elastic_bandage'=>'roll','emergency_medicine'=>'pcs',
            'gauze_pad'=>'pcs','gauze_roll'=>'roll',
            'medical_tape'=>'roll','canned_goods'=>'cans',
            'coffee'=>'pack','instant_noodles'=>'pcs',
            'rice'=>'kg','bar_soap'=>'bars',
            'bucket'=>'pcs','deodorant'=>'pcs',
            'dipper'=>'pcs','shampoo'=>'btl',
            'toothbrush'=>'pcs','toothpaste'=>'tube',
            'towel'=>'pcs',
        ];

        foreach ($request->input('items', []) as $key => $item) {
            if (!empty($item['included'])) {
                $reliefItems[] = [
                    'key'  => $key,
                    'name' => ucwords(str_replace('_', ' ', $key)),
                    'qty'  => $item['qty'] ?? null,
                    'unit' => $unitMap[$key] ?? null,
                ];
            }
        }

        if ($request->filled('cash_amount')) {
            $reliefItems[] = [
                'key'  => 'cash_aid',
                'name' => 'Cash Aid',
                'qty'  => $request->input('cash_amount'),
                'unit' => 'PHP',
            ];
        }

        $event = DistributionEvent::create([
            'event_name'            => $request->event_name,
            'relief_type'           => $reliefType,
            'relief_items'          => !empty($reliefItems) ? $reliefItems : null,
            'target_barangay'       => $targetBarangay,
            'scan_mode'             => $request->scan_mode,
            'event_date'            => $request->event_date ?? now()->toDateString(),
            'description'           => $request->goods_detail,
            'status'                => 'upcoming',
            'started_at'            => $request->started_at,
            'ended_at'              => $request->ended_at,
            'created_by'            => auth()->id(),
            'distribution_lat'      => $request->distribution_lat ?: null,
            'distribution_lng'      => $request->distribution_lng ?: null,
            'distribution_location' => $request->distribution_location ?: null,
            'distribution_dms'      => $request->distribution_dms ?: null,
        ]);

        AuditLog::log('created_distribution_event', [
            'model'         => 'DistributionEvent',
            'record_id'     => $event->id,
            'affected_name' => $event->event_name,
            'description'   => 'Created distribution event "' . $event->event_name . '" (scan mode: ' . $event->scan_mode . ')',
            'new_values'    => $event->toArray(),
        ]);

        return redirect()->route('admin.distribution.logs')
            ->with('success', 'Distribution event "' . $event->event_name . '" created successfully!');
    }

    public function show(DistributionEvent $event)
    {
        $event->load([
            'logs.household',
            'logs.staff',
            'logs.familyMember',
            'scanAttempts'
        ]);

        $totalReleased    = $event->logs()->count();
        $uniqueHouseholds = $event->logs()->distinct('household_id')->count();

        return view('admin.events.show', compact('event', 'totalReleased', 'uniqueHouseholds'));
    }

    /**
     * Start a distribution event (upcoming → ongoing)
     */
    public function start(DistributionEvent $event)
    {
        if (!$event->canStart()) {
            return back()->withErrors(['error' => 'Only upcoming events can be started.']);
        }

        $event->update([
            'status'     => 'ongoing',
            'started_at' => now(),
        ]);

        AuditLog::log('started_distribution_event', [
            'model'         => 'DistributionEvent',
            'record_id'     => $event->id,
            'affected_name' => $event->event_name,
            'description'   => "Started distribution event \"{$event->event_name}\"",
            'new_values'    => ['status' => 'ongoing', 'started_at' => now()],
        ]);

        return back()->with('success', "Event \"{$event->event_name}\" is now ongoing.");
    }

    /**
     * End / complete a distribution event (ongoing → completed)
     */
    public function end(DistributionEvent $event)
    {
        if (!$event->canEnd()) {
            return back()->withErrors(['error' => 'Only ongoing events can be completed.']);
        }

        $event->update([
            'status'   => 'completed',
            'ended_at' => now(),
        ]);

        AuditLog::log('completed_distribution_event', [
            'model'         => 'DistributionEvent',
            'record_id'     => $event->id,
            'affected_name' => $event->event_name,
            'description'   => "Completed distribution event \"{$event->event_name}\"",
            'new_values'    => ['status' => 'completed', 'ended_at' => now()],
        ]);

        return back()->with('success', "Event \"{$event->event_name}\" marked as completed.");
    }

    /**
     * Cancel a distribution event (upcoming or ongoing → cancelled)
     * Expects POST fields:
     *   cancellation_reason_preset  — preset reason string
     *   cancellation_reason         — additional detail / "Other" description
     */
    public function cancel(Request $request, DistributionEvent $event)
    {
        if (!$event->canCancel()) {
            return back()->withErrors(['error' => 'Only upcoming or ongoing events can be cancelled.']);
        }

        $request->validate([
            'cancellation_reason_preset' => 'required|string|max:255',
            'cancellation_reason'        => 'nullable|string|max:500',
        ]);

        // Build the final reason: use the additional detail if provided, otherwise use the preset
        $preset = $request->input('cancellation_reason_preset');
        $detail = trim($request->input('cancellation_reason', ''));

        $finalReason = $preset === 'Other'
            ? ($detail ?: $preset)
            : ($detail ? "{$preset} — {$detail}" : $preset);

        $event->update([
            'status'               => 'cancelled',
            'cancelled_at'         => now(),
            'cancellation_reason'  => $finalReason,
        ]);

        AuditLog::log('cancelled_distribution_event', [
            'model'         => 'DistributionEvent',
            'record_id'     => $event->id,
            'affected_name' => $event->event_name,
            'description'   => "Cancelled distribution event \"{$event->event_name}\": {$finalReason}",
            'new_values'    => [
                'status'              => 'cancelled',
                'cancelled_at'        => now(),
                'cancellation_reason' => $finalReason,
            ],
        ]);

        return back()->with('success', "Event \"{$event->event_name}\" has been cancelled.");
    }
}