<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    protected $fillable = [
        'household_id',
        'first_name',
        'last_name',
        'middle_name',
        'date_of_birth',
        'gender',
        'relationship',
        'contact_number',
    ];

    public function household()
    {
        return $this->belongsTo(Household::class);
    }
}
