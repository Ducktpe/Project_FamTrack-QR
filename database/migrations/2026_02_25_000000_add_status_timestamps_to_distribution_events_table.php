<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribution_events', function (Blueprint $table) {
            if (!Schema::hasColumn('distribution_events', 'started_at'))
                $table->timestamp('started_at')->nullable()->after('status');
            if (!Schema::hasColumn('distribution_events', 'ended_at'))
                $table->timestamp('ended_at')->nullable()->after('started_at');
            if (!Schema::hasColumn('distribution_events', 'cancelled_at'))
                $table->timestamp('cancelled_at')->nullable()->after('ended_at');
            if (!Schema::hasColumn('distribution_events', 'cancellation_reason'))
                $table->text('cancellation_reason')->nullable()->after('cancelled_at');
        });
    }

    public function down(): void
    {
        Schema::table('distribution_events', function (Blueprint $table) {
            $table->dropColumn(['started_at', 'ended_at', 'cancelled_at', 'cancellation_reason']);
        });
    }
};