<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'household_id',
        'serial_code',
        'file_path',
        'file_name',
        'is_active',
        'reprint_count',
        'generated_by',
        'generated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'generated_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────

    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
