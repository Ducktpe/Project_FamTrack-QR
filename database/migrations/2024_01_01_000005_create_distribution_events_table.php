<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Run this FIFTH — relief distribution events created by Admin
// e.g. "Typhoon Carina Relief Round 1"

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribution_events', function (Blueprint $table) {
            $table->id();

            $table->string('event_name', 150);              // e.g. Typhoon Carina Relief Round 1
            $table->json('relief_type');                    // e.g. ["Food Pack","Cash Aid"] — multi-select
            $table->json('relief_items')->nullable();       // e.g. {"rice":{"qty":5,"unit":"kg"},...}
            $table->json('target_barangay')->nullable();    // JSON array of selected barangays
            $table->date('event_date')->nullable();         // Scheduled distribution date
            $table->text('description')->nullable();        // Optional event notes
            $table->text('goods_detail')->nullable();       // Auto-filled human-readable item summary

            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'cancelled'])
                  ->default('upcoming');

            $table->timestamp('started_at')->nullable();    // Actual start datetime
            $table->timestamp('ended_at')->nullable();      // Actual end datetime
            $table->timestamp('cancelled_at')->nullable();  // Cancellation datetime
            $table->text('cancellation_reason')->nullable(); // Why it was cancelled

            $table->foreignId('created_by')
                  ->constrained('users')
                  ->onDelete('restrict');                   // Admin who created the event

            $table->timestamps();

            $table->index('status');
            $table->index('event_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_events');
    }
};