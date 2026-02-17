<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    // Audit logs are insert-only — never update
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'model',
        'record_id',
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

    // Auto-set created_at on insert
    protected static function booted(): void
    {
        static::creating(function ($log) {
            $log->created_at = now();
        });
    }

    // Relationship back to the user who did the action
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}