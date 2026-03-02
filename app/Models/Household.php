<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Household extends Model
{
    use HasFactory;

    protected $fillable = [
        'household_head_name',
        'sex',
        'birthday',
        'civil_status',
        'contact_number',
        'house_number',
        'street_purok',
        'barangay',
        'municipality',
        'province',
        'listahanan_id',
        'is_4ps_beneficiary',
        'is_pwd',
        'is_senior',
        'is_solo_parent',
        'status',
        'encoded_by',
        'approved_by',
        'qr_code_path',
    ];

    protected $casts = [
        'birthday' => 'date',
        'is_4ps_beneficiary' => 'boolean',
        'is_pwd' => 'boolean',
        'is_senior' => 'boolean',
        'is_solo_parent' => 'boolean',
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
        return $this->members()->count() + 1; // +1 for household head
    }

    public function getAgeAttribute(): int
    {
        return $this->birthday ? $this->birthday->age : 0;
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