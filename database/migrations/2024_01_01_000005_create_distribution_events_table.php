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
            $table->string('relief_type', 100);             // Food Pack / Cash Aid / Medical Kit
            $table->date('event_date');                     // Scheduled distribution date
            $table->text('description')->nullable();        // Optional event notes

            $table->enum('status', ['upcoming', 'ongoing', 'completed'])
                  ->default('upcoming');

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