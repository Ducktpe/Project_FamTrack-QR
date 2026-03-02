<?php

namespace App\Http\Controllers\Encoder;

use App\Http\Controllers\Controller;
use App\Models\Household;
use App\Models\FamilyMember;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * Store a newly created household in database
     */
    public function store(Request $request)
    {
        // Validate household head data
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
            'is_4ps_beneficiary' => 'boolean',
            'is_pwd' => 'boolean',
            'is_senior' => 'boolean',
            'is_solo_parent' => 'boolean',
            
            // Family members validation (optional)
            'members' => 'nullable|array',
            'members.*.full_name' => 'required|string|max:150',
            'members.*.relationship' => 'required|string|max:50',
            'members.*.sex' => 'required|in:Male,Female',
            'members.*.birthday' => 'required|date|before:today',
            'members.*.is_pwd' => 'boolean',
            'members.*.is_student' => 'boolean',
            'members.*.occupation' => 'nullable|string|max:100',
            'members.*.educational_attainment' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            // Create household record (without serial code yet — assigned on approval)
            $household = Household::create([
                'household_head_name' => $validated['household_head_name'],
                'sex' => $validated['sex'],
                'birthday' => $validated['birthday'],
                'civil_status' => $validated['civil_status'],
                'contact_number' => $validated['contact_number'] ?? null,
                'house_number' => $validated['house_number'] ?? null,
                'street_purok' => $validated['street_purok'] ?? null,
                'barangay' => $validated['barangay'],
                'municipality' => $validated['municipality'],
                'province' => $validated['province'],
                'listahanan_id' => $validated['listahanan_id'] ?? null,
                'is_4ps_beneficiary' => $request->has('is_4ps_beneficiary'),
                'is_pwd' => $request->has('is_pwd'),
                'is_senior' => $request->has('is_senior'),
                'is_solo_parent' => $request->has('is_solo_parent'),
                'status' => 'active',
                'encoded_by' => auth()->id(),
                // approved_by is NULL until Admin approves
            ]);

            // Add family members if provided
            if (!empty($validated['members'])) {
                foreach ($validated['members'] as $member) {
                    FamilyMember::create([
                        'household_id' => $household->id,
                        'full_name' => $member['full_name'],
                        'relationship' => $member['relationship'],
                        'sex' => $member['sex'],
                        'birthday' => $member['birthday'],
                        'is_pwd' => isset($member['is_pwd']) && $member['is_pwd'],
                        'is_student' => isset($member['is_student']) && $member['is_student'],
                        'occupation' => $member['occupation'] ?? null,
                        'educational_attainment' => $member['educational_attainment'] ?? null,
                    ]);
                }
            }

            // Audit log
            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
                'action' => 'created',
                'model' => 'Household',
                'record_id' => $household->id,
                'new_values' => $household->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
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

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'action' => 'updated',
            'model' => 'Household',
            'record_id' => $household->id,
            'new_values' => $household->toArray(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('encoder.households.show', $household)
            ->with('success', 'Household updated successfully.');
    }
}