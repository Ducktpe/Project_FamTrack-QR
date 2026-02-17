<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Role Helper Methods ──────────────────────────────
    // Use these in Blade:  @if(auth()->user()->isAdmin())

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEncoder(): bool
    {
        return $this->role === 'encoder';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isAuditor(): bool
    {
        return $this->role === 'auditor';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // ── Relationships ────────────────────────────────────

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
        return $this->hasMany(DistributionLog::class, 'distributed_by');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}