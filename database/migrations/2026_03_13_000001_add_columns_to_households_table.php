<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('households', function (Blueprint $table) {

            // ── Location & Contact (Section A) ──
            $table->string('email', 150)->nullable()->after('serial_code');
            $table->string('barangay_area', 50)->nullable()->after('barangay');
            $table->string('location', 255)->nullable()->after('street_purok');
            $table->decimal('latitude', 10, 7)->nullable()->after('location');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('coordinates_image', 255)->nullable()->after('longitude');

            // ── Housing Unit (Section B) ──
            $table->smallInteger('year_built')->unsigned()->nullable()->after('coordinates_image');
            $table->string('housing_type', 50)->nullable()->after('year_built');
            $table->string('housing_material', 50)->nullable()->after('housing_type');
            $table->string('ownership_type', 50)->nullable()->after('housing_material');
            $table->string('electricity_source', 50)->nullable()->after('ownership_type');

            // ── Utilities & Sanitation (Section C) ──
            $table->string('water_source', 100)->nullable()->after('electricity_source');
            $table->string('toilet_access', 100)->nullable()->after('water_source');
            $table->string('waste_disposal', 50)->nullable()->after('toilet_access');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('households')) {
            return;
        }

        Schema::table('households', function (Blueprint $table) {
            $table->dropColumn([
                'email', 'barangay_area', 'location',
                'latitude', 'longitude', 'coordinates_image',
                'year_built', 'housing_type', 'housing_material',
                'ownership_type', 'electricity_source',
                'water_source', 'toilet_access', 'waste_disposal',
            ]);
        });
    }
};