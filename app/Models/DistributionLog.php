<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributionLog extends Model
{
    protected $fillable = [
        'distribution_event_id',
        'household_id',
        'items_distributed',
        'quantity',
        'distribution_date',
        'received_by',
        'notes',
    ];

    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function event()
    {
        return $this->belongsTo(DistributionEvent::class, 'distribution_event_id');
    }
}
