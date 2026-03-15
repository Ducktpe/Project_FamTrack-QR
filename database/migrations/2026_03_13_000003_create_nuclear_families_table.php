<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nuclear_families', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('household_id');
            $table->string('family_name', 150)->nullable()->comment('Surname / family label');
            $table->string('family_type', 50)->nullable()->comment('Nuclear, Extended, Solo Parent, etc.');
            $table->string('family_head', 150)->nullable()->comment('Name of the head of this nuclear family');
            $table->timestamps();

            $table->index('household_id', 'idx_nf_household');
            $table->foreign('household_id')
                  ->references('id')->on('households')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nuclear_families');
    }
};
