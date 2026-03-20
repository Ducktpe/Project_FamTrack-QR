<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('family_member_details', function (Blueprint $table) {
            // Add employment_other after job_title if it doesn't already exist
            if (!Schema::hasColumn('family_member_details', 'employment_other')) {
                $table->string('employment_other', 150)->nullable()->after('job_title');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('family_member_details', function (Blueprint $table) {
            if (Schema::hasColumn('family_member_details', 'employment_other')) {
                $table->dropColumn('employment_other');
            }
        });
    }
};
