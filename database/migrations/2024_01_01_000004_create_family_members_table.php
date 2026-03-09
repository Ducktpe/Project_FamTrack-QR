<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Run this FOURTH — individual members linked to a household
// Multiple rows per household (one per family member)

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();

            // --- Link to parent household ---
            $table->foreignId('household_id')
                  ->constrained('households')
                  ->onDelete('cascade');                     // delete members if household deleted

            // --- Member Info (RBI) ---
            $table->string('full_name', 150);               // Last, First, MI
            $table->string('relationship', 50);             // Son/Daughter/Spouse/Parent/Other
            $table->enum('sex', ['Male', 'Female']);
            $table->date('birthday');
            $table->tinyInteger('age')
                  ->virtualAs('TIMESTAMPDIFF(YEAR, birthday, CURDATE())'); // auto-computed

            // --- Status Flags ---
            $table->boolean('is_pwd')->default(false);
            $table->boolean('is_student')->default(false);
            $table->boolean('is_senior_citizen')
                  ->virtualAs('TIMESTAMPDIFF(YEAR, birthday, CURDATE()) >= 60'); // auto-computed

            // --- Optional Info ---
            $table->string('occupation', 100)->nullable();
            $table->string('philhealth_no', 30)->nullable();
            $table->string('educational_attainment', 50)->nullable(); // Elementary/HS/College/etc

            $table->timestamps();

            // --- Index ---
            $table->index('household_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};