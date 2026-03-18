<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Household extends Model
{
    use HasFactory;

    protected $fillable = [
        // Identity / QR
        'serial_code',
        'qr_code_path',

        // Section 1A — Location & Contact
        'household_head_name',
        'contact_number',
        'listahanan_id',
        'email',
        'barangay',
        'municipality',
        'province',
        'barangay_area',
        'location',
        'street_purok',
        'latitude',
        'longitude',
        'coordinates_image',

        // Section 1B — Housing Unit
        'year_built',
        'housing_type',
        'housing_material',
        'ownership_type',
        'electricity_source',

        // Section 1C — Utilities & Sanitation
        'water_source',
        'toilet_access',
        'waste_disposal',

        // Computed vulnerability flags (derived from family_members)
        'is_4ps_beneficiary',
        'is_pwd',
        'is_senior',
        'is_solo_parent',

        // Meta
        'status',
        'encoded_by',
        'approved_by',
    ];

    protected $casts = [
        'is_4ps_beneficiary' => 'boolean',
        'is_pwd'             => 'boolean',
        'is_senior'          => 'boolean',
        'is_solo_parent'     => 'boolean',
        'latitude'           => 'decimal:7',
        'longitude'          => 'decimal:7',
        'year_built'         => 'integer',
    ];

    // ── Relationships ────────────────────────────────────

    public function encoder()
    {
        return $this->belongsTo(User::class, 'encoded_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function members()
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function nuclearFamilies()
    {
        return $this->hasMany(NuclearFamily::class);
    }

    /** The primary nuclear family that owns this household record */
    public function primaryFamily()
    {
        return $this->hasOne(NuclearFamily::class)->where('is_primary', true);
    }

    /** The household head member (is_family_head = true in the primary nuclear family) */
    public function headMember()
    {
        return $this->hasOneThrough(
            FamilyMember::class,
            NuclearFamily::class,
            'household_id',   // FK on nuclear_families
            'nuclear_family_id', // FK on family_members
            'id',             // PK on households
            'id'              // PK on nuclear_families
        )->where('nuclear_families.is_primary', true)
         ->where('family_members.is_family_head', true);
    }

    public function riskProfile()
    {
        return $this->hasOne(HouseholdRiskProfile::class);
    }

    public function qrCode()
    {
        return $this->hasOne(QrCode::class);
    }

    public function distributionLogs()
    {
        return $this->hasMany(DistributionLog::class, 'household_id');
    }

    // ── Helper Methods ───────────────────────────────────

    public function isApproved(): bool
    {
        return !is_null($this->approved_by);
    }

    public function isPending(): bool
    {
        return is_null($this->approved_by);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getTotalMembersAttribute(): int
    {
        return $this->members()->count();
    }

    // ── Scopes ───────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->whereNull('approved_by');
    }

    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_by');
    }

    public function scopeListahanan($query)
    {
        return $query->whereNotNull('listahanan_id');
    }

    public function scope4Ps($query)
    {
        return $query->where('is_4ps_beneficiary', true);
    }
}