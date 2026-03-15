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
            'event_date'            => 'nullable|date',
            'goods_detail'          => 'nullable|string',
            'started_at'            => 'required|date',
            'ended_at'              => 'required|date|after:started_at',
            'status'                => 'required|in:upcoming,ongoing,completed,cancelled',
            'distribution_lat'      => 'nullable|numeric|between:-90,90',
            'distribution_lng'      => 'nullable|numeric|between:-180,180',
            'distribution_location' => 'nullable|string|max:255',
            'distribution_dms'      => 'nullable|string|max:100',
        ]);

        // Plain comma-separated strings (no array cast on these DB columns)
        $reliefType     = implode(', ', $request->input('relief_type'));
        $targetBarangay = implode(', ', $request->input('target_barangay'));

        // Build relief_items JSON from blade checkboxes: items[key][included] + items[key][qty]
        $reliefItems = [];
        $unitMap = [
            'feminine_hygiene_wash' => 'btl',  'sanitary_pads'      => 'pack',
            'tissue_wipes'          => 'pack',  'underwear'          => 'pcs',
            'alcohol'               => 'btl',   'bandaid'            => 'box',
            'bandage'               => 'roll',  'betadine'           => 'btl',
            'elastic_bandage'       => 'roll',  'emergency_medicine' => 'pcs',
            'gauze_pad'             => 'pcs',   'gauze_roll'         => 'roll',
            'medical_tape'          => 'roll',  'canned_goods'       => 'cans',
            'coffee'                => 'pack',  'instant_noodles'    => 'pcs',
            'rice'                  => 'kg',    'bar_soap'           => 'bars',
            'bucket'                => 'pcs',   'deodorant'          => 'pcs',
            'dipper'                => 'pcs',   'shampoo'            => 'btl',
            'toothbrush'            => 'pcs',   'toothpaste'         => 'tube',
            'towel'                 => 'pcs',
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

        $event = \App\Models\DistributionEvent::create([
                    'event_name'            => $request->event_name,
                    'relief_type'           => implode(', ', $request->relief_type),
                    'relief_items'          => !empty($reliefItems) ? $reliefItems : null,
                    'target_barangay'       => implode(', ', $request->target_barangay), // FIX 1: was passing raw array → "Array to string" error
                    'event_date'            => $request->event_date ?? now()->toDateString(),
                    'description'           => $request->goods_detail,
                    'status'                => 'upcoming',
                    'started_at'            => $request->started_at,
                    'ended_at'              => $request->ended_at,
                    'created_by'            => auth()->id(),
                    'distribution_lat'      => $request->distribution_lat ?: null,
                    'distribution_lng'      => $request->distribution_lng ?: null,
                    'distribution_location' => $request->distribution_location ?: null,
                    'distribution_dms'      => $request->distribution_dms ?: null,        // FIX 2: was missing entirely
                ]);

        AuditLog::log('created_distribution_event', [
            'model'         => 'DistributionEvent',
            'record_id'     => $event->id,
            'affected_name' => $event->event_name,
            'description'   => 'Created distribution event "' . $event->event_name . '"',
            'new_values'    => $event->toArray(),
        ]);

        return redirect()->route('admin.distribution.logs')
            ->with('success', 'Distribution event "' . $event->event_name . '" created successfully!');
    }

    public function updateStatus(DistributionEvent $event, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
        ]);

        $event->update(['status' => $validated['status']]);

        AuditLog::log('updated_event_status', [
            'model'         => 'DistributionEvent',
            'record_id'     => $event->id,
            'affected_name' => $event->event_name,
            'description'   => 'Changed status of "' . $event->event_name . '" to ' . $validated['status'],
            'new_values'    => ['status' => $validated['status']],
        ]);

        return back()->with('success', "Event status changed to: {$validated['status']}");
    }

    public function show(DistributionEvent $event)
    {
        $event->load('logs.household', 'logs.staff');

        $totalReleased    = $event->logs()->count();
        $uniqueHouseholds = $event->logs()->distinct('household_id')->count();

        return view('admin.events.show', compact('event', 'totalReleased', 'uniqueHouseholds'));
    }

    public function start(DistributionEvent $event)
    {
        if (!$event->canStart()) {
            return back()->with('error', 'Event cannot be started.');
        }

        $event->update(['status' => 'ongoing', 'started_at' => now()]);

        AuditLog::log('started_distribution_event', [
            'model'         => 'DistributionEvent',
            'record_id'     => $event->id,
            'affected_name' => $event->event_name,
            'description'   => 'Started distribution event "' . $event->event_name . '"',
            'new_values'    => ['status' => 'ongoing', 'started_at' => now()],
        ]);

        return back()->with('success', 'Event has been started!');
    }

    public function end(DistributionEvent $event)
    {
        if (!$event->canEnd()) {
            return back()->with('error', 'Event cannot be ended.');
        }

        $event->update(['status' => 'completed', 'ended_at' => now()]);

        AuditLog::log('completed_distribution_event', [
            'model'         => 'DistributionEvent',
            'record_id'     => $event->id,
            'affected_name' => $event->event_name,
            'description'   => 'Marked distribution event "' . $event->event_name . '" as completed',
            'new_values'    => ['status' => 'completed', 'ended_at' => now()],
        ]);

        return back()->with('success', 'Event has been completed!');
    }

    public function cancel(Request $request, DistributionEvent $event)
    {
        if (!$event->canCancel()) {
            return back()->with('error', 'Event cannot be cancelled.');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|min:5|max:500',
        ]);

        $event->update([
            'status'              => 'cancelled',
            'cancelled_at'        => now(),
            'cancellation_reason' => $request->cancellation_reason,
        ]);

        AuditLog::log('cancelled_distribution_event', [
            'model'         => 'DistributionEvent',
            'record_id'     => $event->id,
            'affected_name' => $event->event_name,
            'description'   => 'Cancelled event "' . $event->event_name . '": ' . $request->cancellation_reason,
            'new_values'    => ['status' => 'cancelled', 'cancellation_reason' => $request->cancellation_reason],
        ]);

        return back()->with('success', 'Event has been cancelled.');
    }
}