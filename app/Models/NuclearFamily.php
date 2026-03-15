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
    ];

    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function members()
    {
        return $this->hasMany(FamilyMember::class);
    }
}