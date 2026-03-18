<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribution_events', function (Blueprint $table) {
            // 'household' = scan the household QR card (one per household, current behavior)
            // 'family_head' = scan the personal family head QR card (one per family head)
            $table->enum('scan_mode', ['household', 'family_head'])
                  ->default('household')
                  ->after('status')
                  ->comment('Determines which QR type is accepted: household card or family head personal card');
        });
    }

    public function down(): void
    {
        Schema::table('distribution_events', function (Blueprint $table) {
            $table->dropColumn('scan_mode');
        });
    }
};
