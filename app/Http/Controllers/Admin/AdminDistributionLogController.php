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

        return view('admin.distribution.logs', compact('events'));
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
}