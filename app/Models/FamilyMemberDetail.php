<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyMemberDetail extends Model
{
    protected $fillable = [
        'family_member_id',
        'vulnerable_sector',
        'vuln_registered',
        'vuln_id_number',
        'is_lgbtqia',
        'employment_status',
        'job_title',
    ];

    protected $casts = [
        'is_lgbtqia'     => 'integer',
        'vuln_registered' => 'integer',
    ];

    public function member()
    {
        return $this->belongsTo(FamilyMember::class, 'family_member_id');
    }
}