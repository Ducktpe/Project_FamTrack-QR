<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\DistributionLog;
use App\Models\DistributionEvent;
use Illuminate\Http\Request;

class AuditorDistributionLogController extends Controller
{
    public function index(Request $request)
    {
        $query = DistributionEvent::with('logs');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('event_name', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhere('relief_type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->where('event_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('event_date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $events = $query->orderBy('event_date', 'desc')
            ->paginate(20);

        $events->getCollection()->transform(function ($event) {
            $event->total_distributed = $event->logs()->count();
            $event->unique_households = $event->logs()->distinct('household_id')->count();
            return $event;
        });

        return view('auditor.distribution.logs', compact('events'));
    }

    public function eventsList()
    {
        $events = DistributionEvent::with('logs')
            ->orderBy('event_date', 'desc')
            ->get()
            ->map(function ($event) {
                $event->total_distributed = $event->logs()->count();
                $event->unique_households = $event->logs()->distinct('household_id')->count();
                return $event;
            });

        return view('auditor.distribution.events-list', compact('events'));
    }

    public function eventHouseholds(DistributionEvent $event, Request $request)
    {
        $query = $event->logs()->with(['household.qrCode', 'staff']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('serial_code', 'like', "%{$search}%")
                ->orWhereHas('household', function ($hq) use ($search) {
                    $hq->where('household_head_name', 'like', "%{$search}%")
                        ->orWhere('barangay', 'like', "%{$search}%");
                });
            });
        }

        $logs = $query->orderByDesc('distributed_at')->get();

        $households = $logs->map(function ($log) {
            if ($log->household) {
                $log->household->distributionLog = $log;
                return $log->household;
            }
        })
        ->filter()
        ->unique('id')
        ->values();

        return view('auditor.distribution.event-households', compact('event', 'households'));
    }
}
