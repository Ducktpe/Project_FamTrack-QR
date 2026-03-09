<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'household_id',
        'full_name',
        'relationship',
        'sex',
        'birthday',
        'is_pwd',
        'is_student',
        'occupation',
        'philhealth_no',
        'educational_attainment',
    ];

    protected $casts = [
        'birthday' => 'date',
        'is_pwd' => 'boolean',
        'is_student' => 'boolean',
    ];

    // ── Relationships ────────────────────────────────────

    public function household()
    {
        return $this->belongsTo(Household::class);
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
}