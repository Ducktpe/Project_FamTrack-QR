<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_member_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('family_member_id');

            // ── Vulnerable Sector ──
            $table->string('vulnerable_sector', 30)->nullable()
                  ->comment('None, Senior, PWD, Solo Parent, 4Ps Member, Young, Old');
            $table->tinyInteger('vuln_registered')->nullable()
                  ->comment('1=Registered, 0=Unregistered, NULL=N/A');
            $table->string('vuln_id_number', 50)->nullable()
                  ->comment('PWD/Senior/4Ps ID or household ID');

            // ── LGBTQIA+ ──
            $table->tinyInteger('is_lgbtqia')->default(0);

            // ── Employment ──
            $table->string('employment_status', 30)->nullable()
                  ->comment('Unemployed, Employed, Part-time, Full-time, Self-employed, Pension, Freelance, Other');
            $table->string('job_title', 100)->nullable();

            $table->timestamps();

            $table->unique('family_member_id', 'uq_detail_member');
            $table->foreign('family_member_id', 'fk_detail_member')
                  ->references('id')->on('family_members')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_member_details');
    }
};
