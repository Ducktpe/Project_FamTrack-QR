<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributionEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_name',
        'relief_type',
        'event_date',
        'description',
        'status',
        'created_by',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    // ── Relationships ────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs()
    {
        return $this->hasMany(DistributionLog::class, 'event_id');
    }

    // ── Scopes ───────────────────────────────────────────

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming');
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}

// ═══════════════════════════════════════════════════════════════

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

    // ── Relationships ────────────────────────────────────

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