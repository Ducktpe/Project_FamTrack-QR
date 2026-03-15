<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HouseholdRiskProfile extends Model
{
    protected $fillable = [
        'household_id',
        'early_warning',
        'ews_sources',
        'hazard_awareness',
        'income_average',
        'literacy_rate',
        'financial_assistance',
        'access_info',
        'relocate_willingness',
        'remarks',
    ];

    protected $casts = [
        'early_warning'        => 'integer',
        'hazard_awareness'     => 'integer',
        'financial_assistance' => 'integer',
        'access_info'          => 'integer',
        'relocate_willingness' => 'integer',
        'income_average'       => 'decimal:2',
        'literacy_rate'        => 'integer',
    ];

    public function household()
    {
        return $this->belongsTo(Household::class);
    }
}