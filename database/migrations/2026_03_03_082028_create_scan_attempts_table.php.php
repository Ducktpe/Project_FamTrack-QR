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
       // database/migrations/xxxx_create_scan_attempts_table.php
    Schema::create('scan_attempts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('event_id')->constrained('distribution_events');
    $table->foreignId('household_id')->nullable()->constrained('households');
    $table->string('serial_code');
    $table->foreignId('scanned_by')->constrained('users');
    $table->enum('result', ['success', 'duplicate', 'not_found', 'wrong_barangay']);
    $table->timestamp('scanned_at');
        });
    }
};
