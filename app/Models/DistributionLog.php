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
        'family_member_id',  // ← REQUIRED for family_head scan mode — was missing, caused silent save failure
        'serial_code',
        'distributed_by',
        'distributed_at',
        'items_received',
        'goods_detail',
        'remarks',
    ];

    protected $casts = [
        'distributed_at' => 'datetime',
        'items_received' => 'array',
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

    public function familyMember()
    {
        return $this->belongsTo(FamilyMember::class, 'family_member_id');
    }
}