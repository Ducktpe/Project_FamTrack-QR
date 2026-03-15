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
        Schema::table('distribution_events', function (Blueprint $table) {
            $table->decimal('distribution_lat', 10, 7)->nullable()->after('cancellation_reason');
            $table->decimal('distribution_lng', 10, 7)->nullable()->after('distribution_lat');
            $table->string('distribution_location')->nullable()->after('distribution_lng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distribution_events', function (Blueprint $table) {
            $table->dropColumn(['distribution_lat', 'distribution_lng', 'distribution_location']);
        });
    }
};
