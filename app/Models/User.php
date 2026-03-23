<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'personal_email',
        'account_code',
        'password',
        'role',
        'status',
        'last_login_at',
        'created_by',
        'invite_token',
        'invite_expires_at',
        'is_setup_complete',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'invite_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'last_login_at'      => 'datetime',
            'invite_expires_at'  => 'datetime',
            'deleted_at'         => 'datetime',
            'password'           => 'hashed',
            'is_setup_complete'  => 'boolean',
        ];
    }

    // ── Role Helpers ─────────────────────────────────────────

    public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }
    public function isAdmin(): bool      { return $this->role === 'admin'; }
    public function isEncoder(): bool    { return $this->role === 'encoder'; }
    public function isStaff(): bool      { return $this->role === 'staff'; }
    public function isAuditor(): bool    { return $this->role === 'auditor'; }
    public function isActive(): bool     { return $this->status === 'active'; }

    public function roleLabel(): string
    {
        return match($this->role) {
            'super_admin' => 'Super Administrator',
            'admin'       => 'Administrator',
            'encoder'     => 'Encoder',
            'staff'       => 'Staff',
            'auditor'     => 'Auditor',
            default       => ucfirst($this->role),
        };
    }

    public function rolePrivileges(): array
    {
        return match($this->role) {
            'admin'   => ['Manage households and residents', 'Approve or reject household records', 'Create and manage distribution events', 'Generate and download QR codes', 'View distribution logs and export reports', 'View audit trail logs'],
            'encoder' => ['Encode new household records', 'Edit existing household records', 'View assigned household data'],
            'staff'   => ['Scan QR codes during relief distribution', 'View active distribution events', 'View scan history'],
            'auditor' => ['View household and family profiles (read-only)', 'View distribution logs', 'View full audit trail'],
            default   => ['Access to assigned system modules'],
        };
    }

    // ── Account Code Generator ───────────────────────────────

    public static function generateAccountCode(string $role): string
    {
        // Include trashed so codes never repeat
        $last = self::withTrashed()
            ->where('role', $role)
            ->whereNotNull('account_code')
            ->orderByDesc('id')
            ->value('account_code');

        if (! $last) return 'A001';

        $letter = $last[0];
        $number = (int) substr($last, 1);

        if ($number < 999) {
            return $letter . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
        }

        return chr(ord($letter) + 1) . '001';
    }

    public static function generateSystemEmail(string $role, string $code): string
    {
        $prefix = match($role) {
            'admin'   => 'admin',
            'encoder' => 'encoder',
            'staff'   => 'staff',
            'auditor' => 'auditor',
            default   => $role,
        };

        return strtolower($prefix . $code) . '@barangay.gov.ph';
    }

    public function generateInviteToken(): string
    {
        $plain = Str::random(64);

        $this->update([
            'invite_token'      => hash('sha256', $plain),
            'invite_expires_at' => now()->addHours(24),
        ]);

        return $plain;
    }

    public function hasValidInviteToken(string $plain): bool
    {
        if (! $this->invite_token || ! $this->invite_expires_at) return false;
        if ($this->invite_expires_at->isPast()) return false;
        return hash_equals($this->invite_token, hash('sha256', $plain));
    }

    // ── Relationships ────────────────────────────────────────

    public function creator()
    {
        // withTrashed so deleted creators still show their name
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function createdUsers()
    {
        return $this->hasMany(User::class, 'created_by');
    }

    public function encodedHouseholds()
    {
        return $this->hasMany(Household::class, 'encoded_by');
    }

    public function approvedHouseholds()
    {
        return $this->hasMany(Household::class, 'approved_by');
    }

    public function distributionLogs()
    {
        return $this->hasMany(DistributionLog::class, 'household_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}