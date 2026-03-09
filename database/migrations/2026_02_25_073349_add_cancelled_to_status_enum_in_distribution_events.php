<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE distribution_events MODIFY COLUMN status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') NOT NULL DEFAULT 'upcoming'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE distribution_events MODIFY COLUMN status ENUM('upcoming', 'ongoing', 'completed') NOT NULL DEFAULT 'upcoming'");
    }
};