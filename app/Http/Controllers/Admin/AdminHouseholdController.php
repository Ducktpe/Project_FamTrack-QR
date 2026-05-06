<?php

namespace App\Http\Controllers\Admin;

use App\Services\QrCodeService;
use App\Http\Controllers\Controller;
use App\Models\Household;
use App\Models\AuditLog;
use App\Models\FamilyMember;
use App\Models\NuclearFamily;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

    // ── EDIT ─────────────────────────────────────────────────────
    public function edit(Household $household)
    {
        $household->load([
            'members.detail',
            'nuclearFamilies.members.detail',
        ]);

        return view('admin.households.edit', compact('household'));
    }

    // ── UPDATE ────────────────────────────────────────────────────
    public function update(Request $request, Household $household)
    {
        $request->validate([
            'household_head_name' => 'required|string|max:255',
            'contact_number'      => 'nullable|string|max:20',
            'valid_id_type'       => 'nullable|string|max:100',
            'valid_id_num'        => 'nullable|string|max:100',
            'barangay'            => 'required|string|max:100',
            'barangay_area'       => 'nullable|string|max:100',
            'location'            => 'nullable|string|max:255',
            'latitude'            => 'nullable|numeric|between:-90,90',
            'longitude'           => 'nullable|numeric|between:-180,180',
            'coordinates_image'   => 'nullable|image|max:5120',
            'year_built'          => 'nullable|integer|min:1900|max:' . date('Y'),
            'housing_type'        => 'nullable|string|max:100',
            'housing_material'    => 'nullable|string|max:100',
            'ownership_type'      => 'nullable|string|max:100',
            'electricity_source'  => 'nullable|string|max:100',
            'water_source'        => 'nullable|string|max:100',
            'toilet_type'         => 'nullable|string|max:100',
            'garbage_disposal'    => 'nullable|array',
            'internet_access'     => 'nullable|boolean',
            'has_pets'            => 'nullable|boolean',
            'risk_tags'           => 'nullable|array',

            // Nuclear families
            'nuclear_families'                               => 'nullable|array',
            'nuclear_families.*.id'                          => 'nullable|integer|exists:nuclear_families,id',
            'nuclear_families.*.name'                        => 'nullable|string|max:255',

            // Members
            'members'                                        => 'nullable|array',
            'members.*.id'                                   => 'nullable|integer|exists:family_members,id',
            'members.*.nuclear_family_id'                    => 'nullable|integer',
            'members.*.full_name'                            => 'required_with:members|string|max:255',
            'members.*.relationship'                         => 'nullable|string|max:100',
            'members.*.birthdate'                            => 'nullable|date',
            'members.*.age'                                  => 'nullable|integer|min:0|max:150',
            'members.*.gender'                               => 'nullable|string|max:20',
            'members.*.civil_status'                         => 'nullable|string|max:50',
            'members.*.employment_status'                    => 'nullable|string|max:100',
            'members.*.job_title'                            => 'nullable|string|max:100',
            'members.*.educational_attainment'               => 'nullable|string|max:100',
            'members.*.monthly_income'                       => 'nullable|numeric|min:0',
            'members.*.is_pwd'                               => 'nullable|boolean',
            'members.*.pwd_type'                             => 'nullable|string|max:100',
            'members.*.is_senior'                            => 'nullable|boolean',
            'members.*.is_pregnant'                          => 'nullable|boolean',
            'members.*.is_lactating'                         => 'nullable|boolean',
            'members.*.is_solo_parent'                       => 'nullable|boolean',
            'members.*.has_maintenance_medicine'             => 'nullable|boolean',
            'members.*.maintenance_medicine_details'         => 'nullable|string|max:255',
        ]);

        $old = $household->toArray();

        // Handle coordinates image upload
        if ($request->hasFile('coordinates_image')) {
            // Delete old image if it exists
            if ($household->coordinates_image) {
                Storage::disk('public')->delete($household->coordinates_image);
            }
            $path = $request->file('coordinates_image')->store('household_images', 'public');
            $household->coordinates_image = $path;
        }

        // Update household fields
        $household->fill([
            'household_head_name' => $request->household_head_name,
            'contact_number'      => $request->contact_number,
            'valid_id_type'       => $request->valid_id_type,
            'valid_id_num'        => $request->valid_id_num,
            'barangay'            => $request->barangay,
            'barangay_area'       => $request->barangay_area,
            'location'            => $request->location,
            'latitude'            => $request->latitude,
            'longitude'           => $request->longitude,
            'year_built'          => $request->year_built,
            'housing_type'        => $request->housing_type,
            'housing_material'    => $request->housing_material,
            'ownership_type'      => $request->ownership_type,
            'electricity_source'  => $request->electricity_source,
            'water_source'        => $request->water_source,
            'toilet_type'         => $request->toilet_type,
            'garbage_disposal'    => $request->garbage_disposal,
            'internet_access'     => $request->boolean('internet_access'),
            'has_pets'            => $request->boolean('has_pets'),
            'risk_tags'           => $request->risk_tags,
        ]);

        $household->save();

        // ── Sync nuclear families ──────────────────────────────────
        $submittedFamilyIds = [];

        foreach ($request->input('nuclear_families', []) as $nfData) {
            if (!empty($nfData['id'])) {
                // Update existing
                $nf = NuclearFamily::where('id', $nfData['id'])
                                   ->where('household_id', $household->id)
                                   ->first();
                if ($nf) {
                    $nf->update(['name' => $nfData['name'] ?? $nf->name]);
                    $submittedFamilyIds[] = $nf->id;
                }
            } else {
                // Create new nuclear family
                $nf = NuclearFamily::create([
                    'household_id' => $household->id,
                    'name'         => $nfData['name'] ?? 'Nuclear Family',
                ]);
                $submittedFamilyIds[] = $nf->id;
            }
        }

        // Delete removed nuclear families (cascades to members via DB or manually)
        if (!empty($submittedFamilyIds)) {
            $household->nuclearFamilies()
                      ->whereNotIn('id', $submittedFamilyIds)
                      ->delete();
        }

        // ── Sync members ───────────────────────────────────────────
        $submittedMemberIds = [];

        foreach ($request->input('members', []) as $mData) {
            $memberData = [
                'household_id'                => $household->id,
                'nuclear_family_id'           => $mData['nuclear_family_id'] ?? null,
                'full_name'                   => $mData['full_name'],
                'relationship'                => $mData['relationship'] ?? null,
                'birthdate'                   => $mData['birthdate'] ?? null,
                'age'                         => $mData['age'] ?? null,
                'gender'                      => $mData['gender'] ?? null,
                'civil_status'                => $mData['civil_status'] ?? null,
                'employment_status'           => $mData['employment_status'] ?? null,
                'job_title'                   => $mData['job_title'] ?? null,
                'educational_attainment'      => $mData['educational_attainment'] ?? null,
                'monthly_income'              => $mData['monthly_income'] ?? null,
                'is_pwd'                      => !empty($mData['is_pwd']),
                'pwd_type'                    => $mData['pwd_type'] ?? null,
                'is_senior'                   => !empty($mData['is_senior']),
                'is_pregnant'                 => !empty($mData['is_pregnant']),
                'is_lactating'                => !empty($mData['is_lactating']),
                'is_solo_parent'              => !empty($mData['is_solo_parent']),
                'has_maintenance_medicine'    => !empty($mData['has_maintenance_medicine']),
                'maintenance_medicine_details'=> $mData['maintenance_medicine_details'] ?? null,
            ];

            if (!empty($mData['id'])) {
                $member = FamilyMember::where('id', $mData['id'])
                                      ->where('household_id', $household->id)
                                      ->first();
                if ($member) {
                    $member->update($memberData);
                    $submittedMemberIds[] = $member->id;
                }
            } else {
                $member = FamilyMember::create($memberData);
                $submittedMemberIds[] = $member->id;
            }
        }

        // Delete removed members
        if (!empty($submittedMemberIds)) {
            $household->members()
                      ->whereNotIn('id', $submittedMemberIds)
                      ->delete();
        }

        AuditLog::log('updated_household', [
            'model'         => 'Household',
            'record_id'     => $household->id,
            'affected_name' => $household->household_head_name,
            'description'   => "Admin updated household record of {$household->household_head_name}",
            'old_values'    => $old,
            'new_values'    => $household->fresh()->toArray(),
        ]);

        return redirect()->route('admin.households.show', $household)
            ->with('success', 'Household updated successfully.');
    }

    // ─────────────────────────────────────────────────────────────

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
            DB::beginTransaction();

            $year     = date('Y');
            $brgyCode = $this->barangayCode($household->barangay);
            $prefix   = "NIC-{$brgyCode}-HH-{$year}";

            $lastHousehold = DB::table('households')
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

            DB::commit();

            return redirect()->route('admin.households.show', $household)
                ->with('success', "Household approved! Serial Code: {$serialCode}");

        } catch (\Exception $e) {
            DB::rollBack();
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

    public function downloadQrCode(Household $household)
    {
        if (!$household->qrCode) {
            abort(404, 'QR code not found.');
        }

        $downloadPath = storage_path('app/public/qrcodes/download/' . $household->serial_code . '.svg');

        if (!file_exists($downloadPath)) {
            $downloadPath = storage_path('app/public/' . $household->qrCode->file_path);
        }

        if (!file_exists($downloadPath)) {
            abort(404, 'QR code file not found.');
        }

        return response()->download($downloadPath, $household->serial_code . '.svg');
    }

    public function generateHeadQrCode(Household $household, FamilyMember $member, QrCodeService $qrService)
    {
        $household->refresh();

        if (!$household->isApproved()) {
            return back()->withErrors(['error' => 'Household must be approved before generating a family head QR code.']);
        }
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

            $member->update([
                'qr_code_path'    => $path,
                'qr_generated_at' => now(),
                'qr_reprint_count' => 0,
            ]);

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

    public function downloadHeadQrCode(Household $household, FamilyMember $member)
    {
        if (!$member->qr_code_path) {
            abort(404, 'Family head QR code not found.');
        }
        if ($member->household_id !== $household->id) {
            abort(403);
        }

        $storedFile   = basename($member->qr_code_path, '.svg');
        $serial       = $storedFile;

        $downloadPath = storage_path('app/public/qrcodes/heads/download/' . $serial . '.svg');

        if (!file_exists($downloadPath)) {
            $downloadPath = storage_path('app/public/' . $member->qr_code_path);
        }

        if (!file_exists($downloadPath)) {
            abort(404, 'QR code file not found.');
        }

        $member->increment('qr_reprint_count');

        $filename = $serial . '-' . \Illuminate\Support\Str::slug($member->full_name) . '-qr.svg';

        return response()->download($downloadPath, $filename);
    }
}