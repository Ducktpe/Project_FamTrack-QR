<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Model for distribution_release_photos
 *
 * Stores proof-of-release photos taken by staff during ayuda distribution.
 * One photo record per distribution log entry.
 *
 * @property int         $id
 * @property int         $distribution_log_id
 * @property int         $household_id
 * @property int|null    $family_member_id      null when household QR was used
 * @property string      $qr_type               'household' | 'family_head'
 * @property string      $photo_path            relative path from storage root
 * @property string      $photo_taken_at        client-side ISO-8601 timestamp
 * @property int         $taken_by              user id of the staff who confirmed
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class DistributionReleasePhoto extends Model
{
    use HasFactory;

    protected $table = 'distribution_release_photos';

    protected $fillable = [
        'distribution_log_id',
        'household_id',
        'family_member_id',
        'qr_type',
        'photo_path',
        'photo_taken_at',
        'taken_by',
    ];

    protected $casts = [
        'photo_taken_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function distributionLog()
    {
        return $this->belongsTo(DistributionLog::class);
    }

    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    /**
     * The family member who received the release (family-head QR mode only).
     * Null for household-QR releases.
     */
    public function familyMember()
    {
        return $this->belongsTo(FamilyMember::class);
    }

    /**
     * The staff member who took the photo and confirmed the release.
     */
    public function takenBy()
    {
        return $this->belongsTo(User::class, 'taken_by');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    /** Filter to household-QR releases only. */
    public function scopeHouseholdQr($query)
    {
        return $query->where('qr_type', 'household');
    }

    /** Filter to family-head-QR releases only. */
    public function scopeFamilyHeadQr($query)
    {
        return $query->where('qr_type', 'family_head');
    }

    /** Photos taken by a specific staff user. */
    public function scopeTakenBy($query, int $userId)
    {
        return $query->where('taken_by', $userId);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    /**
     * Full public URL for the photo.
     * Usage: $photo->url   (or $photo->getUrlAttribute())
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->photo_path);
    }

    /**
     * Whether this photo was captured via household QR.
     */
    public function isHouseholdQr(): bool
    {
        return $this->qr_type === 'household';
    }

    /**
     * Whether this photo was captured via family-head personal QR.
     */
    public function isFamilyHeadQr(): bool
    {
        return $this->qr_type === 'family_head';
    }
}
