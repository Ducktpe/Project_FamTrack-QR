<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Run this THIRD — the main family/household record (RBI format)
// One record = one household/family unit

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('households', function (Blueprint $table) {
            $table->id();

            // --- Serial Code & QR ---
            $table->string('serial_code', 20)->unique();    // e.g. NIC-2024-00001
            $table->string('qr_code_path', 255)->nullable(); // storage path to QR PNG

            // --- Household Head Info (RBI) ---
            $table->string('household_head_name', 150);     // Last, First, MI
            $table->enum('sex', ['Male', 'Female']);
            $table->date('birthday');
            $table->string('civil_status', 30);             // Single/Married/Widowed/Separated
            $table->string('contact_number', 20)->nullable();

            // --- Address ---
            $table->string('house_number', 30)->nullable();
            $table->string('street_purok', 100)->nullable(); // Street or Purok name
            $table->string('barangay', 100);
            $table->string('municipality', 100);
            $table->string('province', 100);

            // --- DSWD / Listahanan Flags ---
            $table->string('listahanan_id', 50)->nullable(); // DSWD Listahanan HH ID
            $table->boolean('is_4ps_beneficiary')->default(false);
            $table->boolean('is_pwd')->default(false);       // has PWD member
            $table->boolean('is_senior')->default(false);    // has senior (60+) member
            $table->boolean('is_solo_parent')->default(false);

            // --- Record Management ---
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->foreignId('encoded_by')
                  ->constrained('users')
                  ->onDelete('restrict');                    // encoder who registered
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');                    // admin who approved

            $table->timestamps();

            // --- Indexes for fast lookup ---
            $table->index('serial_code');
            $table->index('status');
            $table->index('barangay');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('households');
    }
};