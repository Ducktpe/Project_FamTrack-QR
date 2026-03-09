<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Call PSGC Seeder FIRST
        $this->call(PSGCSeeder::class);

        // Create one account per role for testing
        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@barangay.gov.ph',
            'password' => bcrypt('Admin@1234'),
            'role'     => 'admin',
            'status'   => 'active',
        ]);

        User::create([
            'name'     => 'Test Encoder',
            'email'    => 'encoder@barangay.gov.ph',
            'password' => bcrypt('Encoder@1234'),
            'role'     => 'encoder',
            'status'   => 'active',
        ]);

        User::create([
            'name'     => 'Test Staff',
            'email'    => 'staff@barangay.gov.ph',
            'password' => bcrypt('Staff@1234'),
            'role'     => 'staff',
            'status'   => 'active',
        ]);

        User::create([
            'name'     => 'Test Auditor',
            'email'    => 'auditor@barangay.gov.ph',
            'password' => bcrypt('Auditor@1234'),
            'role'     => 'auditor',
            'status'   => 'active',
        ]);
    }
}