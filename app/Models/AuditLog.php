<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'category',
        'severity',
        'model',
        'record_id',
        'affected_name',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($log) {
            $log->created_at = now();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Convenience static logger ──────────────────────────────────────────
    // Usage: AuditLog::log('approved_household', [...])
    public static function log(string $action, array $data = []): void
    {
        $user = auth()->user();

        static::create(array_merge([
            'user_id'       => $user?->id,
            'user_name'     => $user?->name ?? 'System',
            'action'        => $action,
            'category'      => static::categoryFor($action),
            'severity'      => static::severityFor($action),
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
        ], $data));
    }

    // ── Auto-categorize based on action string ─────────────────────────────
    private static function categoryFor(string $action): string
    {
        return match(true) {
            str_contains($action, 'household') || str_contains($action, 'approved') || str_contains($action, 'rejected') => 'household',
            str_contains($action, 'qr')        => 'qr_code',
            str_contains($action, 'distribut') || str_contains($action, 'ayuda') || str_contains($action, 'event') => 'distribution',
            str_contains($action, 'login')     || str_contains($action, 'logout') => 'auth',
            default                            => 'general',
        };
    }

    // ── Auto-assign severity ───────────────────────────────────────────────
    private static function severityFor(string $action): string
    {
        return match(true) {
            str_contains($action, 'delete') || str_contains($action, 'rejected') => 'high',
            str_contains($action, 'approved') || str_contains($action, 'distribut') || str_contains($action, 'login') => 'medium',
            default => 'low',
        };
    }
}