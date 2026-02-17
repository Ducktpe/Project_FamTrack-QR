<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    protected $fillable = [
        'household_id',
        'qr_code',
        'qr_path',
        'is_active',
        'generated_at',
        'scanned_at',
    ];

    public function household()
    {
        return $this->belongsTo(Household::class);
    }
}
