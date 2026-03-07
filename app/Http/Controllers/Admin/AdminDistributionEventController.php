<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DistributionEvent;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AdminDistributionEventController extends Controller
{
    /**
     * Display all distribution events
     */
    public function index()
    {
        $events = DistributionEvent::with('creator')
            ->orderBy('event_date', 'desc')
            ->paginate(20);

        return view('admin.events.index', compact('events'));
    }

    /**
     * Show form to create new event
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Store new distribution event
     *
     * Fixes applied:
     *  1. target_barangay validated as array (blade sends target_barangay[])
     *     then imploded to a comma-separated string before saving
     *  2. event_date made nullable to match the optional field in the blade
     *  3. started_at and ended_at added to validation and create()
     *  4. goods_detail (form field name) mapped correctly to description (DB column)
     *  5. status defaults to 'upcoming' so the hidden field always works
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_name'          => 'required|string|max:255',

            // the quick-create view may send multiple types
            'relief_type'         => ['required', 'array', 'min:1'],
            'relief_type.*'       => ['required', 'string', 'max:255'],

            // ── FIX 1: array because blade sends target_barangay[] ──
            'target_barangay'     => 'required|array|min:1',
            'target_barangay.*'   => 'required|string|max:100',

            // ── FIX 2: nullable to match the optional date field ──
            'event_date'          => 'nullable|date',

            'goods_detail'        => 'nullable|string',   // maps to description

            // ── FIX 3: started_at and ended_at now validated ──
            'started_at'          => 'required|date',
            'ended_at'            => 'required|date|after:started_at',

            'status'              => 'required|in:upcoming,ongoing,completed,cancelled',
        ]);

        // ── FIX 1: join selected barangays into comma-separated string ──
        $targetBarangay = implode(', ', $validated['target_barangay']);

        // if relief_type is an array, join it to a readable string
        $reliefType = is_array($validated['relief_type'])
            ? implode(', ', $validated['relief_type'])
            : $validated['relief_type'];

        $event = DistributionEvent::create([
            'event_name'      => $validated['event_name'],
            'relief_type'     => $reliefType,
            'target_barangay' => $targetBarangay,
            'event_date'      => $validated['event_date'] ?? now()->toDateString(),
            'description'     => $validated['goods_detail'] ?? null,  // FIX 4: goods_detail → description
            'status'          => $validated['status'],
            'started_at'      => $validated['started_at'],            // FIX 3
            'ended_at'        => $validated['ended_at'],              // FIX 3
            'created_by'      => auth()->id(),
        ]);

        AuditLog::log('created_distribution_event', [
            'model'         => 'DistributionEvent',
            'record_id'     => $event->id,
            'affected_name' => $event->event_name,
            'description'   => 'Created distribution event "' . $event->event_name . '"',
            'new_values'    => $event->toArray(),
        ]);

        return redirect()->route('admin.distribution.logs')
            ->with('success', 'Distribution event created successfully!');
    }

    /**
     * Change event status
     */
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

    /**
     * View event details and distribution logs
     */
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

        $event->update([
            'status'     => 'ongoing',
            'started_at' => now(),
        ]);

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

        $event->update([
            'status'   => 'completed',
            'ended_at' => now(),
        ]);

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