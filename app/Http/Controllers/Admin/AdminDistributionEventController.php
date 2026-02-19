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
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:150',
            'relief_type' => 'required|string|max:100',
            'event_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $event = DistributionEvent::create([
            'event_name' => $validated['event_name'],
            'relief_type' => $validated['relief_type'],
            'event_date' => $validated['event_date'],
            'description' => $validated['description'] ?? null,
            'status' => 'upcoming',
            'created_by' => auth()->id(),
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'action' => 'created',
            'model' => 'DistributionEvent',
            'record_id' => $event->id,
            'new_values' => $event->toArray(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Distribution event created successfully!');
    }

    /**
     * Change event status
     */
    public function updateStatus(DistributionEvent $event, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:upcoming,ongoing,completed',
        ]);

        $event->update(['status' => $validated['status']]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'action' => 'updated_status',
            'model' => 'DistributionEvent',
            'record_id' => $event->id,
            'new_values' => ['status' => $validated['status']],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "Event status changed to: {$validated['status']}");
    }

    /**
     * View event details and distribution logs
     */
    public function show(DistributionEvent $event)
    {
        $event->load('logs.household', 'logs.staff');
        
        $totalReleased = $event->logs()->count();
        $uniqueHouseholds = $event->logs()->distinct('household_id')->count();

        return view('admin.events.show', compact('event', 'totalReleased', 'uniqueHouseholds'));
    }
}
