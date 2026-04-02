<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add 'Student' as a valid employment_status option in family_member_details.
     *
     * The column is varchar(30) so no type change is needed — this migration
     * updates the column comment to reflect the new accepted value.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `family_member_details`
            MODIFY COLUMN `employment_status` varchar(30) DEFAULT NULL
            COMMENT 'Unemployed, Employed, Part-time, Full-time, Self-employed, Pension / Retired, Freelance, Student, Other'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE `family_member_details`
            MODIFY COLUMN `employment_status` varchar(30) DEFAULT NULL
            COMMENT 'Unemployed, Employed, Part-time, Full-time, Self-employed, Pension, Freelance, Other'
        ");
    }
};
