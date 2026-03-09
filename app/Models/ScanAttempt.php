<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanAttempt extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'event_id', 'household_id', 'serial_code',
        'scanned_by', 'result', 'scanned_at',
    ];

    protected $casts = ['scanned_at' => 'datetime'];

    public function event()    { return $this->belongsTo(DistributionEvent::class); }
    public function household(){ return $this->belongsTo(Household::class); }
    public function scanner()  { return $this->belongsTo(User::class, 'scanned_by'); }
}

?>