<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Call PSGC Seeder FIRST ───────────────────────────────
        $this->call(PSGCSeeder::class);

        // ── Super Admin ──────────────────────────────────────────
        User::create([
            'name'     => 'Super Administrator',
            'email'    => 'superadmin@mdrrmo.gov.ph',
            'password' => bcrypt('SuperAdmin@1234'),
            'role'     => 'super_admin',
            'status'   => 'active',
        ]);

        // // ── Admin ────────────────────────────────────────────────
        // User::create([
        //     'name'     => 'Administrator',
        //     'email'    => 'admin@barangay.gov.ph',
        //     'password' => bcrypt('Admin@1234'),
        //     'role'     => 'admin',
        //     'status'   => 'active',
        // ]);

        // // ── Encoder ──────────────────────────────────────────────
        // User::create([
        //     'name'     => 'Test Encoder',
        //     'email'    => 'encoder@barangay.gov.ph',
        //     'password' => bcrypt('Encoder@1234'),
        //     'role'     => 'encoder',
        //     'status'   => 'active',
        // ]);

        // // ── Staff ────────────────────────────────────────────────
        // User::create([
        //     'name'     => 'Test Staff',
        //     'email'    => 'staff@barangay.gov.ph',
        //     'password' => bcrypt('Staff@1234'),
        //     'role'     => 'staff',
        //     'status'   => 'active',
        // ]);

        // // ── Auditor ──────────────────────────────────────────────
        // User::create([
        //     'name'     => 'Test Auditor',
        //     'email'    => 'auditor@barangay.gov.ph',
        //     'password' => bcrypt('Auditor@1234'),
        //     'role'     => 'auditor',
        //     'status'   => 'active',
        // ]);
    }
}