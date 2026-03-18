<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NuclearFamily extends Model
{
    protected $fillable = [
        'household_id',
        'family_name',
        'family_type',
        'family_head',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    // ── Relationships ────────────────────────────────────

    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function members()
    {
        return $this->hasMany(FamilyMember::class);
    }

    /** The head member of this nuclear family */
    public function headMember()
    {
        return $this->hasOne(FamilyMember::class)->where('is_family_head', true);
    }

    // ── Scopes ───────────────────────────────────────────

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}