<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Household extends Model
{
    protected $fillable = [
        'barangay_config_id',
        'household_code',
        'head_of_household',
        'address',
        'member_count',
    ];

    public function familyMembers()
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function qrCodes()
    {
        return $this->hasMany(QrCode::class);
    }
}
