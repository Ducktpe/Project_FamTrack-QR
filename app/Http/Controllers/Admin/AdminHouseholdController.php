<?php

namespace App\Http\Controllers\Admin;

use App\Services\QrCodeService;
use App\Http\Controllers\Controller;
use App\Models\Household;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AdminHouseholdController extends Controller
{
    /**
     * Display all households (pending + approved)
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $search = $request->get('search');

        $query = Household::with('encoder', 'members');

        if ($filter === 'pending') {
            $query->pending();
        } elseif ($filter === 'approved') {
            $query->approved();
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('household_head_name', 'like', "%{$search}%")
                  ->orWhere('barangay',           'like', "%{$search}%")
                  ->orWhere('street_purok',        'like', "%{$search}%")
                  ->orWhere('serial_code',         'like', "%{$search}%")
                  ->orWhereHas('encoder', fn($eq) =>
                      $eq->where('name', 'like', "%{$search}%")
                  );
            });
        }

        $households    = $query->orderBy('created_at', 'desc')->paginate(20);
        $pendingCount  = Household::pending()->count();
        $approvedCount = Household::approved()->count();

        return view('admin.households.index', compact(
            'households',
            'filter',
            'search',
            'pendingCount',
            'approvedCount'
        ));
    }

    /**
     * Display the specified household
     */
    public function show(Household $household)
    {
        $household->load('members', 'encoder', 'approver', 'qrCode');

        $pendingCount  = Household::pending()->count();
        $approvedCount = Household::approved()->count();
        $filter        = 'all';

        return view('admin.households.show', compact('household', 'pendingCount', 'approvedCount', 'filter'));
    }

    /**
     * Approve a household and generate serial code
     */
    public function approve(Household $household)
    {
        if ($household->serial_code !== null || $household->approved_by !== null) {
            return back()->withErrors([
                'error' => 'This household is already approved with serial code: ' . $household->serial_code
            ]);
        }

        try {
            \DB::beginTransaction();

            $year = date('Y');

            $lastHousehold = \DB::table('households')
                ->where('serial_code', 'like', "NIC-$year-%")
                ->orderBy('serial_code', 'desc')
                ->lockForUpdate()
                ->first();

            if ($lastHousehold) {
                $lastNumber = (int) substr($lastHousehold->serial_code, -5);
                $nextNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '00001';
            }

            $serialCode = "NIC-$year-$nextNumber";

            $household->serial_code = $serialCode;
            $household->approved_by = auth()->id();
            $household->save();

            AuditLog::create([
                'user_id'    => auth()->id(),
                'user_name'  => auth()->user()->name,
                'action'     => 'approved',
                'model'      => 'Household',
                'record_id'  => $household->id,
                'new_values' => [
                    'serial_code' => $serialCode,
                    'approved_by' => auth()->id(),
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            \DB::commit();

            return redirect()->route('admin.households.show', $household)
                ->with('success', "Household approved! Serial Code: {$serialCode}");

        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withErrors([
                'error' => 'Failed to approve household: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reject/unapprove a household (remove approval)
     */
    public function unapprove(Household $household)
    {
        if (!$household->isApproved()) {
            return back()->withErrors(['error' => 'This household is not yet approved.']);
        }

        if ($household->qrCode) {
            return back()->withErrors(['error' => 'Cannot unapprove — QR code already generated. Contact system admin.']);
        }

        $household->serial_code = null;
        $household->approved_by = null;
        $household->save();

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'action'     => 'unapproved',
            'model'      => 'Household',
            'record_id'  => $household->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'Household approval revoked. Serial code removed.');
    }

    /**
     * Delete a household (Admin only, before approval recommended)
     */
    public function destroy(Household $household)
    {
        if ($household->isApproved()) {
            return back()->withErrors(['error' => 'Cannot delete approved household. Unapprove first.']);
        }

        $householdName = $household->household_head_name;
        $household->delete();

        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'action'     => 'deleted',
            'model'      => 'Household',
            'record_id'  => $household->id,
            'old_values' => ['household_head_name' => $householdName],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.households.index')
            ->with('success', 'Household deleted successfully.');
    }

    public function generateQrCode(Household $household, QrCodeService $qrService)
    {
        if (!$household->isApproved()) {
            return back()->withErrors(['error' => 'Household must be approved before generating QR code.']);
        }

        if ($household->qrCode) {
            return back()->withErrors(['error' => 'QR code already exists for this household.']);
        }

        try {
            $qrCode = $qrService->generateForHousehold($household);

            AuditLog::create([
                'user_id'    => auth()->id(),
                'user_name'  => auth()->user()->name,
                'action'     => 'generated_qr',
                'model'      => 'QrCode',
                'record_id'  => $qrCode->id,
                'new_values' => [
                    'household_id' => $household->id,
                    'serial_code'  => $household->serial_code,
                    'file_name'    => $qrCode->file_name,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return redirect()->route('admin.households.show', $household)
                ->with('success', 'QR Code generated successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to generate QR: ' . $e->getMessage()]);
        }
    }

    /**
     * Download QR code image
     */
    public function downloadQrCode(Household $household)
    {
        if (!$household->qrCode) {
            abort(404, 'QR code not found.');
        }

        $filePath = storage_path('app/public/' . $household->qrCode->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'QR code file not found.');
        }

        return response()->download($filePath, $household->serial_code . '.svg');
    }
}