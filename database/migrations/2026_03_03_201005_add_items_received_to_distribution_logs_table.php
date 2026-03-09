<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribution_logs', function (Blueprint $table) {
            $table->json('items_received')->nullable()->after('serial_code');
            // Stores the actual items given to this specific household,
            // copied from event relief_items at scan time but editable by staff.
            // e.g. {"rice":{"name":"Rice","qty":5,"unit":"kg"}, ...}
        });
    }

    public function down(): void
    {
        Schema::table('distribution_logs', function (Blueprint $table) {
            $table->dropColumn('items_received');
        });
    }
};