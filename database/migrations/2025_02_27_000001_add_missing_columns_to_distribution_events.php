<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribution_events', function (Blueprint $table) {
            // Add target_barangay if it doesn't exist
            if (!Schema::hasColumn('distribution_events', 'target_barangay')) {
                $table->string('target_barangay', 500)->nullable()->after('relief_type');
            }

            // Add started_at / ended_at / cancelled_at / cancellation_reason
            // if they were never added (check first to avoid errors on re-run)
            if (!Schema::hasColumn('distribution_events', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('distribution_events', 'ended_at')) {
                $table->timestamp('ended_at')->nullable()->after('started_at');
            }
            if (!Schema::hasColumn('distribution_events', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('ended_at');
            }
            if (!Schema::hasColumn('distribution_events', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            }

            // Also widen the status enum to include 'cancelled'
            // (original migration only had upcoming/ongoing/completed)
            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'cancelled'])
                  ->default('upcoming')
                  ->change();
        });

        // Backfill existing rows that have no target_barangay
        \DB::table('distribution_events')
            ->whereNull('target_barangay')
            ->orWhere('target_barangay', '')
            ->update(['target_barangay' => 'All Barangays']);
    }

    public function down(): void
    {
        Schema::table('distribution_events', function (Blueprint $table) {
            $table->dropColumn([
                'target_barangay',
                'started_at',
                'ended_at',
                'cancelled_at',
                'cancellation_reason',
            ]);

            $table->enum('status', ['upcoming', 'ongoing', 'completed'])
                  ->default('upcoming')
                  ->change();
        });
    }
};