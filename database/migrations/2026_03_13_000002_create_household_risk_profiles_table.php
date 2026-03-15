<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('household_risk_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('household_id');

            // ── Disaster Awareness & Access (Section D) ──
            $table->tinyInteger('early_warning')->default(0)->comment('1=Yes, 0=No');
            $table->string('ews_sources', 100)->nullable()->comment('Comma-separated: tv,radio,brgy,other');
            $table->tinyInteger('hazard_awareness')->default(0)->comment('1=Yes, 0=No');

            // ── Economic & Social Profile (Section E) ──
            $table->decimal('income_average', 12, 2)->nullable();
            $table->tinyInteger('literacy_rate')->unsigned()->nullable()->comment('0-100 percent');
            $table->tinyInteger('financial_assistance')->default(0)->comment('1=Yes, 0=No');
            $table->tinyInteger('access_info')->default(0)->comment('1=Yes, 0=No');
            $table->tinyInteger('relocate_willingness')->default(0)->comment('1=Yes, 0=No');
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->unique('household_id', 'uq_risk_household');
            $table->foreign('household_id')
                  ->references('id')->on('households')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('household_risk_profiles');
    }
};
