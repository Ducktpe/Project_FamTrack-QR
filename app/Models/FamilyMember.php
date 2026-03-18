<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'household_id',
        'nuclear_family_id',
        'qr_code_path',       // personal QR for the family head
        'is_family_head',     // true = this member is the head of their nuclear family
        'full_name',
        'relationship',
        'sex',
        'birthday',
        'civil_status',
        'is_pwd',
        'is_student',
        'occupation',
        'philhealth_no',
        'educational_attainment',
    ];

    protected $casts = [
        'birthday'       => 'date',
        'is_pwd'         => 'boolean',
        'is_student'     => 'boolean',
        'is_family_head' => 'boolean',
    ];

    // ── Relationships ────────────────────────────────────

    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function nuclearFamily()
    {
        return $this->belongsTo(NuclearFamily::class);
    }

    public function detail()
    {
        return $this->hasOne(FamilyMemberDetail::class);
    }

    // ── Helper Methods ───────────────────────────────────

    public function getAgeAttribute(): int
    {
        return $this->birthday ? $this->birthday->age : 0;
    }

    public function isSenior(): bool
    {
        return $this->age >= 60;
    }

    public function isMinor(): bool
    {
        return $this->age < 18;
    }

    public function hasPersonalQr(): bool
    {
        return !is_null($this->qr_code_path);
    }

    // ── Scopes ───────────────────────────────────────────

    public function scopeHeads($query)
    {
        return $query->where('is_family_head', true);
    }
}