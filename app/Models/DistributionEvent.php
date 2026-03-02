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
        'target_barangay',
        'event_date',
        'description',
        'status',
        'created_by',
        'started_at',
        'ended_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'event_date'   => 'date',
        'started_at'   => 'datetime',
        'ended_at'     => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs()
    {
        return $this->hasMany(DistributionLog::class, 'event_id');
    }

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

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Helper methods
    public function canStart()
    {
        return $this->status === 'upcoming';
    }

    public function canEnd()
    {
        return $this->status === 'ongoing';
    }

    public function canCancel()
    {
        return in_array($this->status, ['upcoming', 'ongoing']);
    }
}