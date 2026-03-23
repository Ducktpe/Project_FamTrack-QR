<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Expands serial_code columns from varchar(20) to varchar(25).
     * Required because barangay-prefixed codes such as NIC-BAL-HH-2026-00001
     * are 21 characters — one over the old limit of 20.
     */
    public function up(): void
    {
        Schema::table('households', function (Blueprint $table) {
            $table->string('serial_code', 25)->nullable()->change();
        });

        Schema::table('distribution_logs', function (Blueprint $table) {
            $table->string('serial_code', 25)->change();
        });

        Schema::table('qr_codes', function (Blueprint $table) {
            $table->string('serial_code', 25)->change();
        });
    }

    /**
     * Reverse the migrations.
     * Note: kept at varchar(25) to avoid truncation errors on migrate:refresh
     * when serial codes longer than 20 chars already exist in the database.
     */
    public function down(): void
    {
        Schema::table('households', function (Blueprint $table) {
            $table->string('serial_code', 25)->nullable()->change();
        });

        Schema::table('distribution_logs', function (Blueprint $table) {
            $table->string('serial_code', 25)->change();
        });

        Schema::table('qr_codes', function (Blueprint $table) {
            $table->string('serial_code', 25)->change();
        });
    }
};