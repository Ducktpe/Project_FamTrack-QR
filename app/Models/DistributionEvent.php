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
        'relief_items',
        'target_barangay',
        'scan_mode',                // ← ADDED — was missing, caused silent save failure
        'event_date',
        'description',
        'status',
        'created_by',
        'started_at',
        'ended_at',
        'cancelled_at',
        'cancellation_reason',
        'distribution_lat',
        'distribution_lng',
        'distribution_location',
        'distribution_dms',
    ];

    protected $casts = [
        'event_date'       => 'date',
        'relief_items'     => 'array',
        'target_barangay'  => 'array',
        'started_at'       => 'datetime',
        'ended_at'         => 'datetime',
        'cancelled_at'     => 'datetime',
        'distribution_lat' => 'float',
        'distribution_lng' => 'float',
    ];

    // ─── Display Accessors ────────────────────────────────────────────────────

    public function getReliefTypeDisplayAttribute(): string
    {
        return $this->relief_type ?? '—';
    }

    public function getTargetBarangayDisplayAttribute(): string
    {
        $val = $this->target_barangay;
        if (empty($val)) return '—';
        return is_array($val) ? implode(', ', $val) : $val;
    }

    public function getReliefItemsDisplayAttribute(): string
    {
        $items = $this->relief_items;
        if (empty($items)) return '—';

        return collect($items)->map(function ($item) {
            $qty  = $item['qty']  ?? null;
            $name = $item['name'] ?? $item['key'] ?? '?';
            return $qty ? "{$qty} {$name}" : $name;
        })->implode(', ');
    }

    /**
     * Human-readable scan mode label
     */
    public function getScanModeLabelAttribute(): string
    {
        return match($this->scan_mode ?? 'household') {
            'family_head' => 'Per Family Head',
            default       => 'Per Household',
        };
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs()
    {
        return $this->hasMany(DistributionLog::class, 'event_id');
    }

    public function scanAttempts()
    {
        return $this->hasMany(ScanAttempt::class, 'event_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

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

    // ─── Helper Methods ───────────────────────────────────────────────────────

    public function canStart(): bool
    {
        return $this->status === 'upcoming';
    }

    public function canEnd(): bool
    {
        return $this->status === 'ongoing';
    }

    public function canCancel(): bool
    {
        return in_array($this->status, ['upcoming', 'ongoing']);
    }

    public function hasPin(): bool
    {
        return !is_null($this->distribution_lat) && !is_null($this->distribution_lng);
    }
}