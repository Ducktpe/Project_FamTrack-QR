<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'household_id',
        'serial_code',
        'distributed_by',
        'distributed_at',
        'goods_detail',
        'remarks',
    ];

    protected $casts = [
        'distributed_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(DistributionEvent::class);
    }

    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'distributed_by');
    }
}