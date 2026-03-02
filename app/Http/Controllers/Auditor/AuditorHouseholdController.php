<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\Household;
use Illuminate\Http\Request;

class AuditorHouseholdController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $search = $request->get('search');

        $query = Household::with(['encoder', 'distributionLogs']);

        if ($filter === 'pending')  $query->pending();
        if ($filter === 'approved') $query->approved();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('household_head_name', 'like', "%{$search}%")
                  ->orWhere('barangay', 'like', "%{$search}%")
                  ->orWhere('serial_code', 'like', "%{$search}%");
            });
        }

        $households    = $query->latest()->paginate(20);
        $pendingCount  = Household::pending()->count();
        $approvedCount = Household::approved()->count();

        return view('auditor.households.index', compact('households', 'filter', 'pendingCount', 'approvedCount'));
    }

    public function show(Household $household)
    {
        $household->load(['encoder', 'approver', 'members', 'qrCode', 'distributionLogs']);

        return view('auditor.households.show', compact('household'));
    }
}