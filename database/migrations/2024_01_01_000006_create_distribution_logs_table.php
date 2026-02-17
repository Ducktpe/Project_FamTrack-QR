<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Run this SIXTH — the core ayuda tracking record
// Created every time a QR code is scanned and confirmed
// This is what prevents duplicates

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribution_logs', function (Blueprint $table) {
            $table->id();

            // --- What event ---
            $table->foreignId('event_id')
                  ->constrained('distribution_events')
                  ->onDelete('restrict');

            // --- Which family ---
            $table->foreignId('household_id')
                  ->constrained('households')
                  ->onDelete('restrict');

            $table->string('serial_code', 20);              // copy for fast audit lookup
                                                            // (avoids join just for reports)

            // --- Who distributed ---
            $table->foreignId('distributed_by')
                  ->constrained('users')
                  ->onDelete('restrict');                   // Distribution Staff user

            // --- When ---
            $table->timestamp('distributed_at')
                  ->useCurrent();                           // auto-set to NOW() on insert

            // --- What was given ---
            $table->text('goods_detail')->nullable();       // e.g. 5kg rice, 2 canned goods
            $table->text('remarks')->nullable();            // optional staff notes

            $table->timestamps();

            // --- CRITICAL: Unique constraint prevents duplicate release per event ---
            // One household can only receive once per event
            $table->unique(['event_id', 'household_id'], 'unique_release_per_event');

            // --- Indexes ---
            $table->index('serial_code');
            $table->index('distributed_at');
            $table->index('event_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_logs');
    }
};