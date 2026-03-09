<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Run this SECOND — stores barangay name, serial code prefix, and seal
// Must exist before households (because households uses the prefix)

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangay_config', function (Blueprint $table) {
            $table->id();
            $table->string('barangay_name', 100);          // e.g. Barangay Poblacion
            $table->string('municipality', 100);            // e.g. Naic
            $table->string('province', 100);                // e.g. Cavite
            $table->string('serial_prefix', 10);            // e.g. NIC, BRY, STA
            $table->string('seal_image_path', 255)
                  ->nullable();                             // path to barangay seal PNG
            $table->string('contact_number', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('captain_name', 150)->nullable(); // Barangay Captain full name
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barangay_config');
    }
};