<?php

namespace App\Http\Controllers\Encoder;

use App\Http\Controllers\Controller;
use App\Models\Household;
use App\Models\FamilyMember;
use App\Models\NuclearFamily;
use App\Models\HouseholdRiskProfile;
use App\Models\FamilyMemberDetail;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EncoderHouseholdController extends Controller
{
    /**
     * Display a listing of households created by this encoder
     */
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $status     = $request->input('status');
        $sex        = $request->input('sex');
        $dateFrom   = $request->input('date_from');
        $dateTo     = $request->input('date_to');
        $is4ps      = $request->boolean('is_4ps');
        $isPwd      = $request->boolean('is_pwd');
        $isSenior   = $request->boolean('is_senior');
        $isSoloParent = $request->boolean('is_solo_parent');

        $households = Household::where('encoded_by', auth()->id())
            ->with('members')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('household_head_name', 'like', "%{$search}%")
                      ->orWhere('barangay', 'like', "%{$search}%")
                      ->orWhere('street_purok', 'like', "%{$search}%")
                      ->orWhere('serial_code', 'like', "%{$search}%");
                });
            })
            ->when($status === 'pending', fn($q) => $q->whereNull('approved_by'))
            ->when($status === 'approved', fn($q) => $q->whereNotNull('approved_by'))
            ->when($sex, fn($q, $sex) => $q->where('sex', $sex))
            ->when($dateFrom, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($dateTo,   fn($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->when($is4ps,      fn($q) => $q->where('is_4ps_beneficiary', true))
            ->when($isPwd,      fn($q) => $q->where('is_pwd', true))
            ->when($isSenior,   fn($q) => $q->where('is_senior', true))
            ->when($isSoloParent, fn($q) => $q->where('is_solo_parent', true))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('encoder.households.index', compact('households'));
    }

    /**
     * Show the form for creating a new household
     */
    public function create()
    {
        return view('encoder.households.create');
    }

    /**
     * Store a newly created household in database.
     * Reads from blade's fam[fi][...] + fam[fi][m][mi][...] structure.
     */
    public function store(Request $request)
    {
        $request->validate([
            'household_head_name' => 'required|string|max:150',
            'sex'                 => 'required|in:Male,Female',
            'birthday'            => 'required|date|before:today',
            'civil_status'        => 'required|string|max:30',
            'contact_number'      => 'nullable|string|max:20',
            'house_number'        => 'nullable|string|max:30',
            'street_purok'        => 'nullable|string|max:100',
            'barangay'            => 'required|string|max:100',
            'municipality'        => 'required|string|max:100',
            'province'            => 'required|string|max:100',
            'listahanan_id'       => 'nullable|string|max:50',
            'latitude'            => 'nullable|numeric|between:-90,90',
            'longitude'           => 'nullable|numeric|between:-180,180',
            'coordinates_image_file' => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        DB::beginTransaction();
        try {
            // ── 1. households ────────────────────────────────────────────
            $household = Household::create([
                'household_head_name' => $request->household_head_name,
                'sex'                 => $request->sex,
                'birthday'            => $request->birthday,
                'civil_status'        => $request->civil_status,
                'contact_number'      => $request->contact_number,
                'house_number'        => $request->house_number,
                'street_purok'        => $request->location,
                'location'            => $request->location,
                'barangay'            => $request->barangay,
                'barangay_area'       => $request->barangay_area,
                'municipality'        => $request->municipality ?? 'Naic',
                'province'            => $request->province    ?? 'Cavite',
                'email'               => $request->email,
                'year_built'          => $request->year_built  ?: null,
                'housing_type'        => $request->housing_type,
                'housing_material'    => $request->housing_material,
                'ownership_type'      => $request->ownership_type,
                'electricity_source'  => $request->electricity_source,
                'water_source'        => $request->water_source,
                'toilet_access'       => $request->toilet_access,
                'waste_disposal'      => $request->waste_disposal,
                'latitude'            => $request->latitude  ?: null,
                'longitude'           => $request->longitude ?: null,
                'coordinates_image'   => null,
                'listahanan_id'       => $request->listahanan_id,
                'is_4ps_beneficiary'  => 0,
                'is_pwd'              => 0,
                'is_senior'           => 0,
                'is_solo_parent'      => 0,
                'status'              => 'active',
                'encoded_by'          => auth()->id(),
            ]);

            // Location photo upload
            if ($request->hasFile('coordinates_image_file')) {
                $path = $request->file('coordinates_image_file')
                    ->store('household-photos', 'public');
                $household->update(['coordinates_image' => $path]);
            }

            // ── 2. household_risk_profiles ───────────────────────────────
            HouseholdRiskProfile::create([
                'household_id'         => $household->id,
                'early_warning'        => $request->early_warning        ?? 0,
                'ews_sources'          => implode(',', $request->input('ews_sources', [])),
                'hazard_awareness'     => $request->hazard_awareness     ?? 0,
                'income_average'       => $request->income_average,
                'literacy_rate'        => $request->literacy_rate,
                'financial_assistance' => $request->financial_assistance ?? 0,
                'access_info'          => $request->access_info          ?? 0,
                'relocate_willingness' => $request->relocate_willingness ?? 0,
                'remarks'              => $request->remarks,
            ]);

            // ── 3. nuclear_families + family_members + family_member_details
            // Blade sends: fam[fi][family_name|family_type|family_head]
            //              fam[fi][m][mi][full_name|sex|birthday|civil_status|...]
            $isSenior    = false;
            $isPwd       = false;
            $is4ps       = false;
            $isSoloParent = false;

            // ── Lookup tables: blade sends numeric index, DB needs string ──
            $vulnLabels   = ['None','Senior','PWD','Solo Parent','4Ps Member','Young','Old'];
            $employLabels = ['Unemployed','Employed','Part-time','Full-time','Self-employed','Pension/Retired','Freelance','Other'];
            $educLabels   = ['Elementary Undergraduate','Elementary Graduate','High School Undergraduate','High School Graduate','Vocational','College Undergraduate','College Graduate','Master','Doctorate','TESDA','Other'];
            $famTypeLabels = ['Nuclear Family','Extended Family','Single Parent Family','Blended Family','Childless Couple','Grandparent-headed','Skipped Generation','Other'];
            $civilLabels  = ['Single','Married','Legally Separated','Widowed'];

            foreach ($request->input('fam', []) as $fi => $famData) {

                $famTypeIdx = isset($famData['family_type']) && $famData['family_type'] !== ''
                    ? (int)$famData['family_type'] : null;

                $nf = NuclearFamily::create([
                    'household_id' => $household->id,
                    'family_name'  => $famData['family_name'] ?? null,
                    'family_type'  => $famTypeIdx !== null ? ($famTypeLabels[$famTypeIdx] ?? null) : null,
                    'family_head'  => $famData['family_head'] ?? null,
                ]);

                // ── For Nuclear Family 1: insert household head as first family member ──
                // Uses Section 1 fields (household_head_name, sex, birthday, civil_status)
                if ($fi == 1) {
                    $headMember = FamilyMember::create([
                        'household_id'           => $household->id,
                        'nuclear_family_id'      => $nf->id,
                        'full_name'              => $household->household_head_name,
                        'relationship'           => 'Head',
                        'civil_status'           => $household->civil_status,
                        'sex'                    => $household->sex,
                        'birthday'               => $household->birthday,
                        'educational_attainment' => null,
                        'is_pwd'                 => 0,
                        'is_student'             => 0,
                    ]);

                    FamilyMemberDetail::create([
                        'family_member_id'  => $headMember->id,
                        'vulnerable_sector' => null,
                        'vuln_registered'   => null,
                        'vuln_id_number'    => null,
                        'is_lgbtqia'        => 0,
                        'employment_status' => null,
                        'job_title'         => null,
                    ]);
                }

                // Loop members — fam[fi][m][mi][...]
                foreach ($famData['m'] ?? [] as $mi => $m) {
                    // NF1 row 1 = household head, already inserted above from Section 1 fields
                    if ($fi == 1 && $mi == 1) continue;

                    if (empty($m['full_name'])) continue;
                    if (empty($m['birthday']))  continue; // birthday is NOT NULL

                    // Decode numeric indexes → string labels
                    $vulnIdx  = isset($m['vuln_sector'])        && $m['vuln_sector']        !== '' ? (int)$m['vuln_sector']        : null;
                    $empIdx   = isset($m['employment_status'])  && $m['employment_status']  !== '' ? (int)$m['employment_status']  : null;
                    $educIdx  = isset($m['educational_attainment']) && $m['educational_attainment'] !== '' ? (int)$m['educational_attainment'] : null;
                    $civilIdx = isset($m['civil_status'])       && $m['civil_status']       !== '' ? (int)$m['civil_status']       : null;

                    $vulnStr  = $vulnIdx  !== null ? ($vulnLabels[$vulnIdx]   ?? null) : null;
                    $empStr   = $empIdx   !== null ? ($employLabels[$empIdx]  ?? null) : null;
                    $educStr  = $educIdx  !== null ? ($educLabels[$educIdx]   ?? null) : null;
                    $civilStr = $civilIdx !== null ? ($civilLabels[$civilIdx] ?? null) : null;

                    $member = FamilyMember::create([
                        'household_id'           => $household->id,
                        'nuclear_family_id'      => $nf->id,
                        'full_name'              => $m['full_name'],
                        'relationship'           => $m['relationship']  ?? 'Other',
                        'civil_status'           => $civilStr,
                        'sex'                    => $m['sex']           ?? null,
                        'birthday'               => $m['birthday'],
                        'educational_attainment' => $educStr,
                        'is_pwd'                 => ($vulnStr === 'PWD') ? 1 : 0,
                        'is_student'             => 0,
                    ]);

                    FamilyMemberDetail::create([
                        'family_member_id'  => $member->id,
                        'vulnerable_sector' => $vulnStr,
                        'vuln_registered'   => $m['vuln_registered'] ?? null,
                        'vuln_id_number'    => $m['vuln_id_number']  ?? null,
                        'is_lgbtqia'        => isset($m['is_lgbtqia']) ? 1 : 0,
                        'employment_status' => $empStr,
                        'job_title'         => $m['job_title']       ?? null,
                    ]);

                    // Bubble vulnerability flags up to household
                    if ($vulnStr === 'PWD')         $isPwd        = true;
                    if ($vulnStr === 'Senior')       $isSenior     = true;
                    if ($vulnStr === 'Solo Parent')  $isSoloParent = true;
                    if ($vulnStr === '4Ps Member')   $is4ps        = true;
                }
            }

            // Update household vulnerability flags
            $household->update([
                'is_pwd'             => $isPwd        ? 1 : 0,
                'is_senior'          => $isSenior     ? 1 : 0,
                'is_solo_parent'     => $isSoloParent ? 1 : 0,
                'is_4ps_beneficiary' => $is4ps        ? 1 : 0,
            ]);

            // ── 4. Audit log ─────────────────────────────────────────────
            AuditLog::log('created_household', [
                'model'         => 'Household',
                'record_id'     => $household->id,
                'affected_name' => $household->household_head_name,
                'description'   => "Registered new household for {$household->household_head_name} in {$household->barangay}",
                'new_values'    => $household->toArray(),
            ]);

            DB::commit();

            return redirect()->route('encoder.households.index')
                ->with('success', 'Household registered successfully. Pending Admin approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to register household: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified household
     */
    public function show(Household $household)
    {
        // Only allow encoder to view their own households
        if ($household->encoded_by !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $household->load('members', 'encoder', 'approver');
        return view('encoder.households.show', compact('household'));
    }

    /**
     * Show the form for editing the specified household
     * Note: Can only edit if NOT yet approved
     */
    public function edit(Household $household)
    {
        if ($household->encoded_by !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        if ($household->isApproved()) {
            return back()->withErrors(['error' => 'Cannot edit approved household. Contact Admin.']);
        }

        $household->load('members');
        return view('encoder.households.edit', compact('household'));
    }

    /**
     * Update the specified household in database
     */
    public function update(Request $request, Household $household)
    {
        if ($household->encoded_by !== auth()->id()) {
            abort(403);
        }

        if ($household->isApproved()) {
            return back()->withErrors(['error' => 'Cannot edit approved household.']);
        }

        // Same validation as store()
        $validated = $request->validate([
            'household_head_name' => 'required|string|max:150',
            'sex' => 'required|in:Male,Female',
            'birthday' => 'required|date|before:today',
            'civil_status' => 'required|string|max:30',
            'contact_number' => 'nullable|string|max:20',
            'house_number' => 'nullable|string|max:30',
            'street_purok' => 'nullable|string|max:100',
            'barangay' => 'required|string|max:100',
            'municipality' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'listahanan_id' => 'nullable|string|max:50',
        ]);

        $household->update($validated);

        AuditLog::log('updated_household', [
            'model'         => 'Household',
            'record_id'     => $household->id,
            'affected_name' => $household->household_head_name,
            'description'   => "Updated household record of {$household->household_head_name}",
            'new_values'    => $household->toArray(),
        ]);

        return redirect()->route('encoder.households.show', $household)
            ->with('success', 'Household updated successfully.');
    }
}