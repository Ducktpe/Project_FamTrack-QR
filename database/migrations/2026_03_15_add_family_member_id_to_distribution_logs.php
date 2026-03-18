<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribution_logs', function (Blueprint $table) {
            // Nullable — only populated when scan_mode = 'family_head'
            $table->foreignId('family_member_id')
                  ->nullable()
                  ->after('household_id')
                  ->constrained('family_members')
                  ->nullOnDelete()
                  ->comment('Populated when scan_mode is family_head — links to the specific family member scanned');
        });
    }

    public function down(): void
    {
        Schema::table('distribution_logs', function (Blueprint $table) {
            $table->dropForeign(['family_member_id']);
            $table->dropColumn('family_member_id');
        });
    }
};
