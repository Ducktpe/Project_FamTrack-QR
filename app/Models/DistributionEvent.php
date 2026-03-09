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
        'goods_detail',
        'status',
        'created_by',
        'started_at',
        'ended_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'event_date'      => 'date',
        'relief_type'     => 'array',
        'relief_items'    => 'array',
        'target_barangay' => 'array',
        'started_at'      => 'datetime',
        'ended_at'        => 'datetime',
        'cancelled_at'    => 'datetime',
    ];

    // ─── Display Accessors ────────────────────────────────────────────────────
    // These convert the JSON-cast array columns into comma-separated strings so
    // Blade's {{ }} / htmlspecialchars() never receives an array.

    /**
     * Return relief_type as a human-readable string.
     * Use $event->relief_type_display in Blade templates.
     */
    public function getReliefTypeDisplayAttribute(): string
    {
        $value = $this->getRawOriginal('relief_type');
        if (is_null($value)) {
            return '—';
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? implode(', ', $decoded) : (string) $value;
    }

    /**
     * Return target_barangay as a human-readable string.
     * Use $event->target_barangay_display in Blade templates.
     */
    public function getTargetBarangayDisplayAttribute(): ?string
    {
        $value = $this->getRawOriginal('target_barangay');
        if (is_null($value)) {
            return null;
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? implode(', ', $decoded) : (string) $value;
    }

    /**
     * Return relief_items as a human-readable string.
     * Use $event->relief_items_display in Blade templates.
     */
    public function getReliefItemsDisplayAttribute(): string
    {
        $value = $this->getRawOriginal('relief_items');
        if (is_null($value)) {
            return '—';
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? implode(', ', $decoded) : (string) $value;
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