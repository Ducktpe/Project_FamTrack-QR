<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangayConfig extends Model
{
    use HasFactory;

    protected $table = 'barangay_config';

    protected $fillable = [
        'barangay_name',
        'municipality',
        'province',
        'serial_prefix',
        'seal_image_path',
        'contact_number',
        'email',
        'captain_name',
    ];

    // ── Helper: Get the singleton config record ─────────

    public static function getConfig()
    {
        return self::first() ?? self::create([
            'barangay_name' => 'Barangay Poblacion',
            'municipality' => 'Naic',
            'province' => 'Cavite',
            'serial_prefix' => 'NIC',
        ]);
    }

    // ── Helper: Generate next serial code ───────────────

    public static function generateSerialCode(): string
    {
        $config = self::getConfig();
        $year = date('Y');
        
        // Get the last household serial code for this year
        $lastHousehold = Household::where('serial_code', 'LIKE', "{$config->serial_prefix}-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastHousehold) {
            // Extract the number part and increment
            $parts = explode('-', $lastHousehold->serial_code);
            $lastNumber = (int) end($parts);
            $nextNumber = $lastNumber + 1;
        } else {
            // First household this year
            $nextNumber = 1;
        }

        // Format: NIC-2024-00001
        return sprintf('%s-%s-%05d', $config->serial_prefix, $year, $nextNumber);
    }
}