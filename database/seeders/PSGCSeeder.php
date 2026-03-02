<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\Barangay;

class PSGCSeeder extends Seeder
{
    public function run(): void
    {
        $csv = array_map('str_getcsv', file(database_path('psgc.csv')));
        $header = array_shift($csv);

        foreach ($csv as $row) {

            $code = $row[0];
            $name = $row[1];
            $level = $row[2];
            $parent = $row[3];

            if ($level === 'Prov') {
                Province::create([
                    'psgc_code' => $code,
                    'name' => $name,
                ]);
            }

            if ($level === 'Mun' || $level === 'City') {
                $province = Province::where('psgc_code', substr($code, 0, 5))->first();

                if ($province) {
                    Municipality::create([
                        'psgc_code' => $code,
                        'province_id' => $province->id,
                        'name' => $name,
                    ]);
                }
            }

            if ($level === 'Bgy') {
                $municipality = Municipality::where('psgc_code', substr($code, 0, 7))->first();

                if ($municipality) {
                    Barangay::create([
                        'psgc_code' => $code,
                        'municipality_id' => $municipality->id,
                        'name' => $name,
                    ]);
                }
            }
        }
    }
}