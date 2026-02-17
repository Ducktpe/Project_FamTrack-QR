<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributionEvent extends Model
{
    protected $fillable = [
        'barangay_config_id',
        'event_name',
        'description',
        'event_date',
        'start_time',
        'end_time',
        'location',
        'status',
    ];

    public function distributionLogs()
    {
        return $this->hasMany(DistributionLog::class);
    }
}
