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
        'relief_items'     => 'array',   // JSON array of {key, name, qty}
        'target_barangay'  => 'array',   // JSON array — DB has a CHECK constraint requiring valid JSON
        'started_at'       => 'datetime',
        'ended_at'         => 'datetime',
        'cancelled_at'     => 'datetime',
        'distribution_lat' => 'float',
        'distribution_lng' => 'float',
        // relief_type is a plain comma-separated string — no cast needed
    ];

    // ─── Display Accessors ────────────────────────────────────────────────────

    /**
     * relief_type is stored as a plain comma-separated string.
     * e.g. "Food Pack, Hygiene Kit"
     */
    public function getReliefTypeDisplayAttribute(): string
    {
        return $this->relief_type ?? '—';
    }

    /**
     * target_barangay is stored as a plain comma-separated string.
     * e.g. "Sabang, Molino, Halang"
     */
    public function getTargetBarangayDisplayAttribute(): string
    {
        $val = $this->target_barangay;
        if (empty($val)) return '—';
        return is_array($val) ? implode(', ', $val) : $val;
    }

    /**
     * relief_items is stored as JSON array of objects: [{key, name, qty}, ...]
     * Returns a readable string like "5 kg Rice, 2 cans Canned Goods"
     */
    public function getReliefItemsDisplayAttribute(): string
    {
        $items = $this->relief_items; // already decoded by cast
        if (empty($items)) return '—';

        return collect($items)->map(function ($item) {
            $qty  = $item['qty']  ?? null;
            $name = $item['name'] ?? $item['key'] ?? '?';
            return $qty ? "{$qty} {$name}" : $name;
        })->implode(', ');
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