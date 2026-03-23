<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('family_members', function (Blueprint $table) {
            $table->unsignedBigInteger('nuclear_family_id')->nullable()->after('household_id');
            $table->string('civil_status', 30)->nullable()->after('relationship');

            $table->index('nuclear_family_id', 'idx_fm_nuclear_family');
            $table->foreign('nuclear_family_id', 'fk_fm_nuclear_family')
                  ->references('id')->on('nuclear_families')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('family_members')) {
            return;
        }

        Schema::table('family_members', function (Blueprint $table) {
            $table->dropForeign('fk_fm_nuclear_family');
            $table->dropIndex('idx_fm_nuclear_family');
            $table->dropColumn(['nuclear_family_id', 'civil_status']);
        });
    }
};