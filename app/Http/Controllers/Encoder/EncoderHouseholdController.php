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
     * Display a listing of households created by this encoder.
     */
    public function index(Request $request)
    {
        $search       = $request->input('search');
        $status       = $request->input('status');
        $barangay     = $request->input('barangay');
        $dateFrom     = $request->input('date_from');
        $dateTo       = $request->input('date_to');
        $is4ps        = $request->boolean('is_4ps');
        $isPwd        = $request->boolean('is_pwd');
        $isSenior     = $request->boolean('is_senior');
        $isSoloParent = $request->boolean('is_solo_parent');

        $households = Household::where('encoded_by', auth()->id())
            ->with(['nuclearFamilies', 'members', 'primaryFamily.headMember'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('household_head_name', 'like', "%{$search}%")
                      ->orWhere('barangay',          'like', "%{$search}%")
                      ->orWhere('location',          'like', "%{$search}%")
                      ->orWhere('street_purok',      'like', "%{$search}%")
                      ->orWhere('serial_code',       'like', "%{$search}%")
                      ->orWhere('contact_number',    'like', "%{$search}%");
                });
            })
            ->when($status === 'pending',  fn($q) => $q->whereNull('approved_by'))
            ->when($status === 'approved', fn($q) => $q->whereNotNull('approved_by'))
            ->when($barangay,     fn($q, $b) => $q->where('barangay', $b))
            ->when($dateFrom,     fn($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($dateTo,       fn($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->when($is4ps,        fn($q) => $q->where('is_4ps_beneficiary', true))
            ->when($isPwd,        fn($q) => $q->where('is_pwd',             true))
            ->when($isSenior,     fn($q) => $q->where('is_senior',          true))
            ->when($isSoloParent, fn($q) => $q->where('is_solo_parent',     true))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('encoder.households.index', compact('households'));
    }

    /**
     * Show the form for creating a new household.
     */
    public function create()
    {
        return view('encoder.households.create');
    }

    /**
     * Store a newly created household in the database.
     * Reads from blade's fam[fi][...] + fam[fi][m][mi][...] structure.
     */
    public function store(Request $request)
    {
        $request->validate([
            'household_head_name'    => 'required|string|max:150',
            'contact_number'         => 'nullable|string|max:20',
            'national_id'          => 'nullable|string|max:50',
            'email'                  => 'nullable|email|max:150',
            'barangay'               => 'required|string|max:100',
            'municipality'           => 'required|string|max:100',
            'province'               => 'required|string|max:100',
            'barangay_area'          => 'nullable|string|max:50',
            'location'               => 'nullable|string|max:255',
            'street_purok'           => 'nullable|string|max:100',
            'latitude'               => 'nullable|numeric|between:-90,90',
            'longitude'              => 'nullable|numeric|between:-180,180',
            'coordinates_image'      => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:5120',
            'year_built'             => 'nullable|integer|min:1900|max:' . date('Y'),
            'housing_type'           => 'nullable|string|max:50',
            'housing_material'       => 'nullable|string|max:50',
            'ownership_type'         => 'nullable|string|max:50',
            'electricity_source'     => 'nullable|string|max:50',
            'water_source'           => 'nullable|string|max:100',
            'toilet_access'          => 'nullable|string|max:100',
            'waste_disposal'         => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            // ── 1. households ────────────────────────────────────────────────
            $household = Household::create([
                // Section 1A
                'household_head_name' => $request->household_head_name,
                'contact_number'      => $request->contact_number,
                'national_id'       => $request->national_id,
                'email'               => $request->email,
                'barangay'            => $request->barangay,
                'municipality'        => $request->municipality ?? 'Naic',
                'province'            => $request->province     ?? 'Cavite',
                'barangay_area'       => $request->barangay_area,
                'location'            => $request->location,
                'street_purok'        => $request->location,
                'latitude'            => $request->latitude  ?: null,
                'longitude'           => $request->longitude ?: null,
                'coordinates_image'   => null,
                // Section 1B
                'year_built'          => $request->year_built ?: null,
                'housing_type'        => $request->housing_type,
                'housing_material'    => $request->housing_material,
                'ownership_type'      => $request->ownership_type,
                'electricity_source'  => $request->electricity_source,
                // Section 1C
                'water_source'        => $request->water_source,
                'toilet_access'       => $request->toilet_access,
                'waste_disposal'      => $request->waste_disposal,
                // Flags (computed after members are saved)
                'is_4ps_beneficiary'  => 0,
                'is_pwd'              => 0,
                'is_senior'           => 0,
                'is_solo_parent'      => 0,
                // Meta
                'status'              => 'active',
                'encoded_by'          => auth()->id(),
            ]);

            // Location photo upload
            if ($request->hasFile('coordinates_image')) {
                $path = $request->file('coordinates_image')
                    ->store('household-photos', 'public');
                $household->update(['coordinates_image' => $path]);
            }

            // ── 2. household_risk_profiles ───────────────────────────────────
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

            // ── 3. nuclear_families + family_members + family_member_details ─
            // Lookup tables: blade sends numeric index for vuln/civil/famType,
            // but sends label text directly for employment_status & educational_attainment
            $vulnLabels    = ['None','Senior','PWD','Solo Parent','4Ps Member','Young','Old'];
            $famTypeLabels = ['Nuclear Family','Extended Family','Single Parent Family','Blended Family','Childless Couple','Grandparent-headed','Skipped Generation','Other'];
            $civilLabels   = ['Single','Married','Legally Separated','Widowed'];

            // Vulnerability flags bubbled up from members to household
            $flagPwd = $flagSenior = $flag4ps = $flagSoloParent = false;

            foreach ($request->input('fam', []) as $fi => $famData) {

                $famTypeIdx = isset($famData['family_type']) && $famData['family_type'] !== ''
                    ? (int) $famData['family_type'] : null;

                // Nuclear Family 1 is always the primary (owner) family of the household
                $isPrimaryFamily = ($fi == 1);

                $nf = NuclearFamily::create([
                    'household_id' => $household->id,
                    'family_name'  => $famData['family_name'] ?? null,
                    'family_type'  => $famTypeIdx !== null ? ($famTypeLabels[$famTypeIdx] ?? null) : null,
                    'family_head'  => $famData['family_head'] ?? null,
                    'is_primary'   => $isPrimaryFamily ? 1 : 0,
                ]);

                // ── For Nuclear Family 1: insert household head as first member ──
                if ($isPrimaryFamily) {
                    $headRow = $famData['m'][1] ?? [];

                    if (!empty($headRow['full_name']) && !empty($headRow['birthday'])) {
                        $headCivilIdx = isset($headRow['civil_status']) && $headRow['civil_status'] !== ''
                            ? (int) $headRow['civil_status'] : null;

                        // employment_status & educational_attainment are sent as label text from blade
                        $headEmpStr  = !empty($headRow['employment_status'])     ? $headRow['employment_status']     : null;
                        $headEducStr = !empty($headRow['educational_attainment'])? $headRow['educational_attainment']: null;

                        $headMember = FamilyMember::create([
                            'household_id'           => $household->id,
                            'nuclear_family_id'      => $nf->id,
                            'is_family_head'         => 1,
                            'full_name'              => $headRow['full_name'],
                            'relationship'           => 'Head',
                            'sex'                    => $headRow['sex']      ?? null,
                            'birthday'               => $headRow['birthday'],
                            'civil_status'           => $headCivilIdx !== null ? ($civilLabels[$headCivilIdx] ?? null) : null,
                            'educational_attainment' => $headEducStr,
                            'is_pwd'                 => 0,
                            'is_student'             => 0,
                        ]);

                        FamilyMemberDetail::create([
                            'family_member_id'  => $headMember->id,
                            'is_lgbtqia'        => isset($headRow['is_lgbtqia']) ? 1 : 0,
                            'vulnerable_sector' => null,
                            'vuln_registered'   => null,
                            'vuln_id_number'    => null,
                            'employment_status' => $headEmpStr,
                            'job_title'         => $headRow['job_title']          ?? null,
                            'employment_other'  => $headRow['employment_other']   ?? null,
                        ]);
                    }
                }

                // ── Loop remaining members — fam[fi][m][mi][...] ─────────────
                foreach ($famData['m'] ?? [] as $mi => $m) {
                    // NF1 row 1 = household head, already inserted above
                    if ($fi == 1 && $mi == 1) continue;

                    if (empty($m['full_name'])) continue;
                    if (empty($m['birthday']))  continue;

                    // vuln and civil still use numeric index; emp and educ are now label text
                    $vulnIdx  = isset($m['vuln_sector'])  && $m['vuln_sector']  !== '' ? (int) $m['vuln_sector']  : null;
                    $civilIdx = isset($m['civil_status']) && $m['civil_status'] !== '' ? (int) $m['civil_status'] : null;

                    $vulnStr  = $vulnIdx  !== null ? ($vulnLabels[$vulnIdx]   ?? null) : null;
                    $civilStr = $civilIdx !== null ? ($civilLabels[$civilIdx] ?? null) : null;

                    // employment_status & educational_attainment sent as label text directly
                    $empStr  = !empty($m['employment_status'])      ? $m['employment_status']      : null;
                    $educStr = !empty($m['educational_attainment']) ? $m['educational_attainment'] : null;

                    $member = FamilyMember::create([
                        'household_id'           => $household->id,
                        'nuclear_family_id'      => $nf->id,
                        'is_family_head'         => !empty($m['is_family_head']) ? 1 : 0,
                        'full_name'              => $m['full_name'],
                        'relationship'           => !empty($m['is_family_head']) ? 'Head' : ($m['relationship'] ?? 'Other'),
                        'sex'                    => $m['sex']           ?? null,
                        'birthday'               => $m['birthday'],
                        'civil_status'           => $civilStr,
                        'educational_attainment' => $educStr,
                        'is_pwd'                 => ($vulnStr === 'PWD') ? 1 : 0,
                        'is_student'             => 0,
                    ]);

                    FamilyMemberDetail::create([
                        'family_member_id'  => $member->id,
                        'is_lgbtqia'        => isset($m['is_lgbtqia']) ? 1 : 0,
                        'vulnerable_sector' => $vulnStr,
                        'vuln_registered'   => $m['vuln_registered']    ?? null,
                        'vuln_id_number'    => $m['vuln_id_number']     ?? null,
                        'employment_status' => $empStr,
                        'job_title'         => $m['job_title']          ?? null,
                        'employment_other'  => $m['employment_other']   ?? null,
                    ]);

                    // Bubble vulnerability flags up to household
                    if ($vulnStr === 'PWD')        $flagPwd        = true;
                    if ($vulnStr === 'Senior')      $flagSenior     = true;
                    if ($vulnStr === 'Solo Parent') $flagSoloParent = true;
                    if ($vulnStr === '4Ps Member')  $flag4ps        = true;
                }
            }

            // Update household vulnerability flags
            $household->update([
                'is_pwd'             => $flagPwd        ? 1 : 0,
                'is_senior'          => $flagSenior     ? 1 : 0,
                'is_solo_parent'     => $flagSoloParent ? 1 : 0,
                'is_4ps_beneficiary' => $flag4ps        ? 1 : 0,
            ]);

            // ── 4. Audit log ─────────────────────────────────────────────────
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
     * Display the specified household.
     */
    public function show(Household $household)
    {
        if ($household->encoded_by !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $household->load([
            'members.detail',
            'nuclearFamilies.members.detail',
            'primaryFamily.headMember',
            'riskProfile',
            'encoder',
            'approver',
        ]);

        return view('encoder.households.show', compact('household'));
    }

    /**
     * Show the form for editing the specified household.
     * Can only edit if NOT yet approved.
     */
    public function edit(Household $household)
    {
        if ($household->encoded_by !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        if ($household->isApproved()) {
            return back()->withErrors(['error' => 'Cannot edit approved household. Contact Admin.']);
        }

        $household->load(['nuclearFamilies.members.detail', 'riskProfile']);

        // Ensure every member has a detail record so the edit form always pre-fills
        foreach ($household->nuclearFamilies as $nf) {
            foreach ($nf->members as $member) {
                if (!$member->detail) {
                    $detail = FamilyMemberDetail::firstOrCreate(
                        ['family_member_id' => $member->id],
                        ['is_lgbtqia' => 0]
                    );
                    $member->setRelation('detail', $detail);
                }
            }
        }

        return view('encoder.households.edit', compact('household'));
    }

    /**
     * Update the specified household in the database,
     * including all nuclear families and their members.
     */
    public function update(Request $request, Household $household)
    {
        if ($household->encoded_by !== auth()->id()) {
            abort(403);
        }

        if ($household->isApproved()) {
            return back()->withErrors(['error' => 'Cannot edit approved household.']);
        }

        $request->validate([
            // Section 1A
            'household_head_name' => 'required|string|max:150',
            'contact_number'      => 'nullable|string|max:20',
            'national_id'       => 'nullable|string|max:50',
            'email'               => 'nullable|email|max:150',
            'barangay'            => 'required|string|max:100',
            'barangay_area'       => 'nullable|string|max:50',
            'location'            => 'nullable|string|max:255',
            'latitude'            => 'nullable|numeric|between:-90,90',
            'longitude'           => 'nullable|numeric|between:-180,180',
            'coordinates_image'   => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:5120',
            // Section 1B
            'year_built'          => 'nullable|integer|min:1900|max:' . date('Y'),
            'housing_type'        => 'nullable|string|max:50',
            'housing_material'    => 'nullable|string|max:50',
            'ownership_type'      => 'nullable|string|max:50',
            'electricity_source'  => 'nullable|string|max:50',
            // Section 1C
            'water_source'        => 'nullable|string|max:100',
            'toilet_access'       => 'nullable|string|max:100',
            'waste_disposal'      => 'nullable|string|max:50',
            // Members
            'members.*.full_name'      => 'required|string|max:150',
            'members.*.birthday'       => 'nullable|date|before:today',
            // Risk profile
            'risk.income_average'      => 'nullable|numeric|min:0',
            'risk.literacy_rate'       => 'nullable|integer|min:0|max:100',
            'risk.remarks'             => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // ── 1. Update household fields ────────────────────────────────────
            // Handle coordinates_image upload — delete old file if replaced
            $coordinatesImagePath = $household->coordinates_image;
            if ($request->hasFile('coordinates_image')) {
                if ($coordinatesImagePath) {
                    Storage::disk('public')->delete($coordinatesImagePath);
                }
                $coordinatesImagePath = $request->file('coordinates_image')
                    ->store('household-photos', 'public');
            }

            $household->update([
                'household_head_name' => $request->household_head_name,
                'contact_number'      => $request->contact_number,
                'national_id'       => $request->national_id,
                'email'               => $request->email,
                'barangay'            => $request->barangay,
                'municipality'        => $request->municipality ?? $household->municipality,
                'province'            => $request->province     ?? $household->province,
                'barangay_area'       => $request->barangay_area,
                'location'            => $request->location,
                'street_purok'        => $request->location,
                'latitude'            => $request->latitude  ?: null,
                'longitude'           => $request->longitude ?: null,
                'coordinates_image'   => $coordinatesImagePath,
                'year_built'          => $request->year_built ?: null,
                'housing_type'        => $request->housing_type,
                'housing_material'    => $request->housing_material,
                'ownership_type'      => $request->ownership_type,
                'electricity_source'  => $request->electricity_source,
                'water_source'        => $request->water_source,
                'toilet_access'       => $request->toilet_access,
                'waste_disposal'      => $request->waste_disposal,
            ]);

            // ── 2. Update nuclear families ────────────────────────────────────
            foreach ($request->input('families', []) as $nfId => $famData) {
                $nf = NuclearFamily::where('id', $nfId)
                    ->where('household_id', $household->id)
                    ->first();

                if (!$nf) continue;

                // Primary family name and head stay in sync with household head
                if ($nf->is_primary) {
                    $nf->update([
                        'family_head' => $household->household_head_name,
                        'family_type' => $famData['family_type'] ?? $nf->family_type,
                    ]);
                } else {
                    $nf->update([
                        'family_name' => $famData['family_name'] ?? $nf->family_name,
                        'family_type' => $famData['family_type'] ?? $nf->family_type,
                        'family_head' => $famData['family_head'] ?? $nf->family_head,
                    ]);
                }
            }

            // ── 3. Update family members & their details ──────────────────────
            $flagPwd = $flagSenior = $flag4ps = $flagSoloParent = false;

            foreach ($request->input('members', []) as $memberId => $m) {
                $member = FamilyMember::where('id', $memberId)
                    ->where('household_id', $household->id)
                    ->first();

                if (!$member) continue;

                $isFamilyHead = !empty($m['is_family_head']) ? 1 : ($member->is_family_head ? 1 : 0);

                $member->update([
                    'full_name'              => $m['full_name'],
                    'is_family_head'         => $isFamilyHead,
                    'relationship'           => $isFamilyHead ? 'Head' : ($m['relationship'] ?? $member->relationship),
                    'sex'                    => $m['sex']      ?? $member->sex,
                    'birthday'               => $m['birthday'] ?: $member->birthday,
                    'civil_status'           => $m['civil_status']           ?? null,
                    'educational_attainment' => $m['educational_attainment'] ?? null,
                    'is_pwd'                 => isset($m['is_pwd'])     ? 1 : 0,
                    'is_student'             => isset($m['is_student']) ? 1 : 0,
                ]);

                // If this is the primary family head, sync household_head_name
                if ($member->is_family_head && $member->nuclearFamily?->is_primary) {
                    $household->update(['household_head_name' => $m['full_name']]);
                }

                // Update or create detail record
                FamilyMemberDetail::updateOrCreate(
                    ['family_member_id' => $member->id],
                    [
                        'is_lgbtqia'        => isset($m['is_lgbtqia']) ? 1 : 0,
                        'vulnerable_sector' => $m['vulnerable_sector'] ?? null,
                        'employment_status' => $m['employment_status'] ?? null,
                        'job_title'         => $m['job_title']         ?? null,
                        'employment_other'  => $m['employment_other']  ?? null,
                    ]
                );

                // Re-compute vulnerability flags
                $vuln = $m['vulnerable_sector'] ?? null;
                if ($vuln === 'PWD')         $flagPwd        = true;
                if ($vuln === 'Senior')      $flagSenior     = true;
                if ($vuln === 'Solo Parent') $flagSoloParent = true;
                if ($vuln === '4Ps Member')  $flag4ps        = true;
                if (isset($m['is_pwd']) && $m['is_pwd']) $flagPwd = true;
            }

            // ── 4. Re-sync household vulnerability flags ──────────────────────
            $household->update([
                'is_pwd'             => $flagPwd        ? 1 : 0,
                'is_senior'          => $flagSenior     ? 1 : 0,
                'is_solo_parent'     => $flagSoloParent ? 1 : 0,
                'is_4ps_beneficiary' => $flag4ps        ? 1 : 0,
            ]);

            // ── 5. Update household risk profile ──────────────────────────────
            $riskData = $request->input('risk', []);

            HouseholdRiskProfile::updateOrCreate(
                ['household_id' => $household->id],
                [
                    'early_warning'        => isset($riskData['early_warning'])        ? 1 : 0,
                    'ews_sources'          => $riskData['ews_sources'] ?? null,
                    'hazard_awareness'     => isset($riskData['hazard_awareness'])     ? 1 : 0,
                    'income_average'       => $riskData['income_average']       ?: null,
                    'literacy_rate'        => $riskData['literacy_rate']        ?: null,
                    'financial_assistance' => isset($riskData['financial_assistance']) ? 1 : 0,
                    'access_info'          => isset($riskData['access_info'])          ? 1 : 0,
                    'relocate_willingness' => isset($riskData['relocate_willingness']) ? 1 : 0,
                    'remarks'              => $riskData['remarks'] ?? null,
                ]
            );

            // ── 6. Audit log ──────────────────────────────────────────────────
            AuditLog::log('updated_household', [
                'model'         => 'Household',
                'record_id'     => $household->id,
                'affected_name' => $household->household_head_name,
                'description'   => "Updated household and member records of {$household->household_head_name}",
                'new_values'    => $household->fresh()->toArray(),
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to save changes: ' . $e->getMessage()]);
        }

        return redirect()->route('encoder.households.show', $household)
            ->with('success', 'Household and member records updated successfully.');
    }
}