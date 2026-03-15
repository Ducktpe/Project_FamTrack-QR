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
           DB::statement('ALTER TABLE distribution_events DROP CONSTRAINT IF EXISTS `distribution_events.relief_type`');
            
            // Re-add without restriction, or with Cash Aid included
            // If you want no constraint at all (most flexible):
            DB::statement('ALTER TABLE distribution_events MODIFY COLUMN `relief_type` VARCHAR(500) NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distribution_events', function (Blueprint $table) {
            //
        });
    }
};
