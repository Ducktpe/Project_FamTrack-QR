<?php

namespace App\Http\Controllers\Admin;

use App\Services\QrCodeService;
use App\Http\Controllers\Controller;
use App\Models\Household;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AdminHouseholdController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $search = $request->get('search');

        $query = Household::with([
            'encoder', 'members', 'nuclearFamilies', 'distributionLogs', 'qrCode',
        ]);

        if ($filter === 'pending') $query->pending();
        elseif ($filter === 'approved') $query->approved();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('household_head_name', 'like', "%{$search}%")
                  ->orWhere('barangay',           'like', "%{$search}%")
                  ->orWhere('street_purok',        'like', "%{$search}%")
                  ->orWhere('serial_code',         'like', "%{$search}%")
                  ->orWhereHas('encoder', fn($eq) => $eq->where('name', 'like', "%{$search}%"));
            });
        }

        $households    = $query->orderBy('created_at', 'desc')->paginate(20);
        $pendingCount  = Household::pending()->count();
        $approvedCount = Household::approved()->count();

        return view('admin.households.index', compact(
            'households', 'filter', 'search', 'pendingCount', 'approvedCount'
        ));
    }

    public function show(Household $household)
    {
        $household->load([
            'members.detail', 'members.nuclearFamily',
            'nuclearFamilies.members.detail',
            'encoder', 'approver', 'qrCode', 'riskProfile',
            'distributionLogs.event',
            'distributionLogs.staff',
        ]);

        $scanCount     = $household->distributionLogs->count();
        $pendingCount  = Household::pending()->count();
        $approvedCount = Household::approved()->count();
        $filter        = 'all';

        return view('admin.households.show', compact(
            'household', 'scanCount', 'pendingCount', 'approvedCount', 'filter'
        ));
    }

    private function barangayCode(string $barangay): string
    {
        $map = [
            'Bagong Kalsada'                => 'BK',  'Balsahan'               => 'BAL',
            'Bancaan'                       => 'BAN', 'Bucana Malaki'          => 'BML',
            'Bucana Sasahan'                => 'BSS', 'Calubcob'               => 'CLB',
            'Capt. C. Nazareno (Poblacion)' => 'CCN', 'Gombalza (Poblacion)'   => 'GMB',
            'Halang'                        => 'HLG', 'Humbac'                 => 'HMB',
            'Ibayo Estacion'                => 'IBE', 'Ibayo Silangan'         => 'IBS',
            'Kanluran'                      => 'KR',  'Latoria'                => 'LAT',
            'Labac'                         => 'LAB', 'Mabolo'                 => 'MBL',
            'Malainen Bago'                 => 'MLB', 'Malainen Luma'          => 'MLL',
            'Makina'                        => 'MKN', 'Molino'                 => 'MOL',
            'Munting Mapino'                => 'MM',  'Muzon'                  => 'MUZ',
            'Palangue 2 & 3'                => 'PL2', 'Palangue Central'       => 'PLC',
            'Sabang'                        => 'SBG', 'San Roque'              => 'SR',
            'Santulan'                      => 'STL', 'Sapa'                   => 'SPA',
            'Timalan Balsahan'              => 'TB',  'Timalan Concepcion'     => 'TC',
        ];
        return $map[trim($barangay)] ?? strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $barangay), 0, 3));
    }

    public function approve(Household $household)
    {
        if ($household->serial_code !== null || $household->approved_by !== null) {
            return back()->withErrors([
                'error' => 'This household is already approved with serial code: ' . $household->serial_code
            ]);
        }

        try {
            \DB::beginTransaction();

            $year     = date('Y');
            $brgyCode = $this->barangayCode($household->barangay);
            $prefix   = "NIC-{$brgyCode}-HH-{$year}";

            $lastHousehold = \DB::table('households')
                ->where('serial_code', 'like', "{$prefix}-%")
                ->orderBy('serial_code', 'desc')
                ->lockForUpdate()
                ->first();

            $nextNumber = $lastHousehold
                ? str_pad((int) substr($lastHousehold->serial_code, -5) + 1, 5, '0', STR_PAD_LEFT)
                : '00001';

            $serialCode = "{$prefix}-{$nextNumber}";

            $household->serial_code = $serialCode;
            $household->approved_by = auth()->id();
            $household->save();

            AuditLog::log('approved_household', [
                'model'         => 'Household',
                'record_id'     => $household->id,
                'affected_name' => $household->household_head_name,
                'description'   => "Approved household and assigned serial code {$serialCode}",
                'new_values'    => ['serial_code' => $serialCode, 'approved_by' => auth()->id()],
            ]);

            \DB::commit();

            return redirect()->route('admin.households.show', $household)
                ->with('success', "Household approved! Serial Code: {$serialCode}");

        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withErrors(['error' => 'Failed to approve household: ' . $e->getMessage()]);
        }
    }

    public function unapprove(Household $household)
    {
        if (!$household->isApproved()) {
            return back()->withErrors(['error' => 'This household is not yet approved.']);
        }
        if ($household->qrCode) {
            return back()->withErrors(['error' => 'Cannot unapprove — QR code already generated. Contact system admin.']);
        }

        $oldSerial = $household->serial_code;

        $household->serial_code = null;
        $household->approved_by = null;
        $household->save();

        AuditLog::log('rejected_household', [
            'model'         => 'Household',
            'record_id'     => $household->id,
            'affected_name' => $household->household_head_name,
            'description'   => "Revoked approval and removed serial code from {$household->household_head_name}",
            'old_values'    => ['serial_code' => $oldSerial],
        ]);

        return back()->with('success', 'Household approval revoked. Serial code removed.');
    }

    public function destroy(Household $household)
    {
        if ($household->isApproved()) {
            return back()->withErrors(['error' => 'Cannot delete approved household. Unapprove first.']);
        }

        $householdName = $household->household_head_name;
        $household->delete();

        AuditLog::log('deleted_household', [
            'model'         => 'Household',
            'record_id'     => $household->id,
            'affected_name' => $householdName,
            'description'   => "Permanently deleted household record of {$householdName}",
            'old_values'    => ['household_head_name' => $householdName],
        ]);

        return redirect()->route('admin.households.index')
            ->with('success', 'Household deleted successfully.');
    }

    public function generateQrCode(Household $household, QrCodeService $qrService)
    {
        $household->refresh();

        if (!$household->isApproved()) {
            return back()->withErrors(['error' => 'Household must be approved before generating QR code.']);
        }
        if ($household->qrCode) {
            return back()->withErrors(['error' => 'QR code already exists for this household.']);
        }

        try {
            $qrCode = $qrService->generateForHousehold($household);

            AuditLog::log('generated_qr_code', [
                'model'         => 'QrCode',
                'record_id'     => $qrCode->id,
                'affected_name' => $household->household_head_name,
                'description'   => "Generated QR code for {$household->household_head_name} ({$household->serial_code})",
                'new_values'    => [
                    'household_id' => $household->id,
                    'serial_code'  => $household->serial_code,
                    'file_name'    => $qrCode->file_name,
                ],
            ]);

            return redirect()->route('admin.households.show', $household)
                ->with('success', 'QR Code generated successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to generate QR: ' . $e->getMessage()]);
        }
    }

    /**
     * Download household QR — serves the /download/ version with full info strip
     */
    public function downloadQrCode(Household $household)
    {
        if (!$household->qrCode) {
            abort(404, 'QR code not found.');
        }

        // Serve download version (with info strip)
        $downloadPath = storage_path('app/public/qrcodes/download/' . $household->serial_code . '.svg');

        // Fallback to display version if download version doesn't exist yet
        if (!file_exists($downloadPath)) {
            $downloadPath = storage_path('app/public/' . $household->qrCode->file_path);
        }

        if (!file_exists($downloadPath)) {
            abort(404, 'QR code file not found.');
        }

        return response()->download($downloadPath, $household->serial_code . '.svg');
    }

    public function generateHeadQrCode(Household $household, \App\Models\FamilyMember $member, QrCodeService $qrService)
    {
        $household->refresh();

        if (!$household->isApproved()) {
            return back()->withErrors(['error' => 'Household must be approved before generating a family head QR code.']);
        }
        // If the member isn't flagged as family head yet, fix it automatically —
        // the admin explicitly chose this member to receive a head QR.
        if (!$member->is_family_head) {
            $member->update([
                'is_family_head' => 1,
                'relationship'   => 'Head',
            ]);
            $member->refresh();
        }
        if ($member->qr_code_path) {
            return back()->withErrors(['error' => 'QR code already exists for this family head.']);
        }
        if ($member->household_id !== $household->id) {
            abort(403);
        }

        try {
            $path = $qrService->generateForFamilyHead($household, $member);

            $member->update(['qr_code_path' => $path]);

            AuditLog::log('generated_head_qr_code', [
                'model'         => 'FamilyMember',
                'record_id'     => $member->id,
                'affected_name' => $member->full_name,
                'description'   => "Generated family head QR code for {$member->full_name} of household {$household->household_head_name}",
                'new_values'    => ['qr_code_path' => $path],
            ]);

            return redirect()->route('admin.households.show', $household)
                ->with('success', "Family Head QR Code generated for {$member->full_name}!");

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to generate QR: ' . $e->getMessage()]);
        }
    }

    /**
     * Download family head QR — serves the /download/ version with full info strip
     */
    public function downloadHeadQrCode(Household $household, \App\Models\FamilyMember $member)
    {
        if (!$member->qr_code_path) {
            abort(404, 'Family head QR code not found.');
        }
        if ($member->household_id !== $household->id) {
            abort(403);
        }

        // Build serial from the stored qr_code_path to ensure we always use the correct
        // per-member serial (e.g. NIC-TB-FH-2026-00001-M2) rather than recalculating.
        $storedFile   = basename($member->qr_code_path, '.svg'); // e.g. NIC-TB-FH-2026-00001-M2
        $serial       = $storedFile;

        // Serve download version (with info strip)
        $downloadPath = storage_path('app/public/qrcodes/heads/download/' . $serial . '.svg');

        // Fallback to display version
        if (!file_exists($downloadPath)) {
            $downloadPath = storage_path('app/public/' . $member->qr_code_path);
        }

        if (!file_exists($downloadPath)) {
            abort(404, 'QR code file not found.');
        }

        $filename = $serial . '-' . \Illuminate\Support\Str::slug($member->full_name) . '-qr.svg';

        return response()->download($downloadPath, $filename);
    }
}