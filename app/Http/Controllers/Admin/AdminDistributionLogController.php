<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DistributionLog;
use App\Models\DistributionEvent;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\DistributionEventExport;

class AdminDistributionLogController extends Controller
{
    public function index(Request $request)
    {
        // Eager-load creator and logs with household so the modal
        // info header and household table have all data without N+1 queries
        $query = DistributionEvent::with(['creator', 'logs.household', 'logs.staff']);

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

        if ($request->filled('barangay')) {
            $brgy = $request->barangay;
            $query->where(function ($q) use ($brgy) {
                $q->whereJsonContains('target_barangay', $brgy)
                  ->orWhere('target_barangay', 'like', "%{$brgy}%");
            });
        }

        $events = $query->orderBy('event_date', 'desc')
            ->paginate(20);

        // Compute totals from already-loaded logs (no extra queries)
        $events->getCollection()->transform(function ($event) {
            $event->total_distributed = $event->logs->count();
            $event->unique_households = $event->logs->unique('household_id')->count();
            return $event;
        });

        // Collect all distinct barangays from target_barangay for the filter dropdown
        $allBarangays = DistributionEvent::all()
            ->flatMap(fn($e) => is_array($e->target_barangay) ? $e->target_barangay : [$e->target_barangay])
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('admin.distribution.logs', compact('events', 'allBarangays'));
    }

    public function exportEventCsv(DistributionEvent $event)
    {
        $fileName = 'distribution_event_' . $event->id . '.csv';

        $callback = function () use ($event) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Event', 'Serial Code', 'Household Head', 'Barangay', 'Distributed By', 'Distributed At', 'Goods Detail', 'Remarks']);

            $event->load('logs.household', 'logs.staff');
            foreach ($event->logs as $log) {
                fputcsv($handle, [
                    $log->id,
                    $event->event_name,
                    $log->serial_code,
                    $log->household?->household_head_name,
                    $log->household?->barangay,
                    $log->staff?->name,
                    $log->distributed_at?->format('Y-m-d H:i:s'),
                    $log->goods_detail,
                    $log->remarks,
                ]);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    public function exportEventPdf(DistributionEvent $event)
    {
        $event->load('logs.household', 'logs.staff');
        $html = view('admin.distribution.event-report-pdf', compact('event'))->render();

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');
        return $pdf->download('distribution_event_' . $event->id . '.pdf');
    }

    public function exportEventXlsx(DistributionEvent $event)
    {
        return (new DistributionEventExport($event))
            ->download('distribution_event_' . $event->id . '.xlsx');
    }

    /**
     * Customisable export — called by the export options modal (POST).
     * Accepts selected columns, barangay filter, and date range.
     */
    public function exportCustomXlsx(Request $request, DistributionEvent $event)
    {
        $logCols  = $request->input('log_cols', []);
        $hhCols   = $request->input('hh_cols', []);
        $barangay = $request->input('barangay');
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        $filename = 'distribution_' . \Str::slug($event->event_name) . '_' . now()->format('Ymd_His') . '.xlsx';

        return (new DistributionEventExport($event, $logCols, $hhCols, $barangay, $dateFrom, $dateTo))
            ->download($filename);
    }

    public function eventsList()
    {
        $events = DistributionEvent::with(['creator', 'logs.household', 'logs.staff'])
            ->orderBy('event_date', 'desc')
            ->get()
            ->map(function ($event) {
                $event->total_distributed = $event->logs->count();
                $event->unique_households = $event->logs->unique('household_id')->count();
                return $event;
            });

        return view('admin.distribution.events-list', compact('events'));
    }

    public function eventHouseholds(DistributionEvent $event, Request $request)
    {
        $query = $event->logs()
            ->with(['household.qrCode', 'staff']);

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

        return view('admin.distribution.event-households', compact('event', 'households'));
    }

    public function cancel(Request $request, DistributionEvent $event)
    {
        $request->validate([
            'cancellation_reason_preset' => 'required|string',
            'cancellation_reason'        => 'nullable|string|max:500',
        ]);

        $reason = $request->cancellation_reason_preset;
        if ($request->filled('cancellation_reason')) {
            $reason .= ': ' . $request->cancellation_reason;
        }

        $event->update([
            'status'              => 'cancelled',
            'cancelled_at'        => now(),
            'cancellation_reason' => $reason,
        ]);

        return back()->with('success', 'Event "' . $event->event_name . '" has been cancelled.');
    }
}