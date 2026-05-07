<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('family_members', function (Blueprint $table) {
            $table->timestamp('qr_generated_at')->nullable()->after('qr_code_path');
            $table->unsignedInteger('qr_reprint_count')->default(0)->after('qr_generated_at');
        });
    }

    public function down(): void
    {
        Schema::table('family_members', function (Blueprint $table) {
            $table->dropColumn(['qr_generated_at', 'qr_reprint_count']);
        });
    }
};
