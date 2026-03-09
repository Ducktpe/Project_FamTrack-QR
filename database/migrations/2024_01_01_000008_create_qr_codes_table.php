<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Run this EIGHTH — stores QR code metadata per household
// The actual PNG file is saved in storage/app/public/qrcodes/
// This table tracks when it was generated and by whom

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('household_id')
                  ->unique()                               // one QR per household only
                  ->constrained('households')
                  ->onDelete('cascade');

            $table->string('serial_code', 20);            // duplicate for fast lookup
            $table->string('file_path', 255);             // e.g. qrcodes/NIC-2024-00001.png
            $table->string('file_name', 100);             // e.g. NIC-2024-00001.png

            $table->boolean('is_active')->default(true);  // false = card was reported lost, reprint issued
            $table->integer('reprint_count')->default(0); // tracks how many times reprinted

            $table->foreignId('generated_by')
                  ->constrained('users')
                  ->onDelete('restrict');                  // admin who approved & triggered generation

            $table->timestamp('generated_at')->useCurrent();
            $table->timestamps();

            $table->index('serial_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};