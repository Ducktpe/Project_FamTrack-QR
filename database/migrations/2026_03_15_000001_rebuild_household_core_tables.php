<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rebuild core household tables in correct form-section order.
 *
 * Tables created (in FK-safe order):
 *   1. households
 *   2. nuclear_families          ← belongs to household, is_primary flag
 *   3. family_members            ← belongs to household + nuclear_family
 *   4. family_member_details     ← belongs to family_member (1-to-1)
 *   5. household_risk_profiles   ← belongs to household (1-to-1)
 *
 * Changes from original schema:
 *   - households : removed sex, birthday, civil_status, house_number
 *                  columns reordered to match form sections (1A → 1B → flags → meta)
 *   - nuclear_families : added is_primary flag
 *   - family_members   : civil_status moved after birthday/age (form column order)
 *   - family_member_details : is_lgbtqia moved before vulnerable_sector (form column order)
 *   - household_risk_profiles : section comments added, column order matches Section 3A → 3B
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Drop in reverse FK order (safe) ───────────────────────────────────
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('household_risk_profiles');
        Schema::dropIfExists('family_member_details');
        Schema::dropIfExists('family_members');
        Schema::dropIfExists('nuclear_families');
        Schema::dropIfExists('households');
        Schema::enableForeignKeyConstraints();

        // ─────────────────────────────────────────────────────────────────────
        // 1. households
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('households', function (Blueprint $table) {
            $table->id();
            $table->string('serial_code', 20)->nullable()->unique();
            $table->string('qr_code_path', 255)->nullable();

            // Section 1A — Location & Contact
            $table->string('household_head_name', 150);
            $table->string('contact_number', 20)->nullable();
            $table->string('valid_id_type', 100)->nullable();
            $table->string('valid_id_num', 100)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('barangay', 100);
            $table->string('municipality', 100)->default('Naic');
            $table->string('province', 100)->default('Cavite');
            $table->string('barangay_area', 50)->nullable();
            $table->string('location', 255)->nullable();
            $table->string('street_purok', 100)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('coordinates_image', 255)->nullable();

            // Section 1B — Housing Unit
            $table->smallInteger('year_built')->unsigned()->nullable();
            $table->string('housing_type', 50)->nullable();
            $table->string('housing_material', 50)->nullable();
            $table->string('ownership_type', 50)->nullable();
            $table->string('electricity_source', 50)->nullable();

            // Section 1C — Utilities & Sanitation
            $table->string('water_source', 100)->nullable();
            $table->string('toilet_access', 100)->nullable();
            $table->string('waste_disposal', 50)->nullable();

            // Computed flags (derived from family_members, updated on save)
            $table->boolean('is_4ps_beneficiary')->default(false);
            $table->boolean('is_pwd')->default(false);
            $table->boolean('is_senior')->default(false);
            $table->boolean('is_solo_parent')->default(false);

            // Meta
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->foreignId('encoded_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Indexes
            $table->index('serial_code');
            $table->index('status');
            $table->index('barangay');
        });

        // ─────────────────────────────────────────────────────────────────────
        // 2. nuclear_families
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('nuclear_families', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')
                  ->constrained('households')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            // Matches form: Family Name → Family Type → Family Head
            $table->string('family_name', 150)->nullable()->comment('Surname / family label');
            $table->string('family_type', 50)->nullable()->comment('Nuclear, Extended, Solo Parent, etc.');
            $table->string('family_head', 150)->nullable()->comment('Name of the head of this nuclear family');

            // Indicator: which nuclear family owns / represents this household record
            $table->boolean('is_primary')->default(false)
                  ->comment('1 = primary/owner family of this household record');

            $table->timestamps();

            $table->index('household_id', 'idx_nf_household');
        });

        // ─────────────────────────────────────────────────────────────────────
        // 3. family_members
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')
                  ->constrained('households')
                  ->cascadeOnDelete();
            $table->foreignId('nuclear_family_id')
                  ->nullable()
                  ->constrained('nuclear_families')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();

            // Member identity — matches form column order:
            // Name → Relationship → Sex → Birthday → Age (virtual) → Civil Status
            $table->string('full_name', 150);
            $table->string('relationship', 50);
            $table->enum('sex', ['Male', 'Female']);
            $table->date('birthday');
            // age — computed virtual column (not a Blueprint helper, use rawColumn)
            $table->string('civil_status', 30)->nullable();

            // Flags
            $table->boolean('is_pwd')->default(false);
            $table->boolean('is_student')->default(false);
            // is_senior_citizen — also virtual, added via statement below

            // Additional details
            $table->string('occupation', 100)->nullable();
            $table->string('philhealth_no', 30)->nullable();
            $table->string('educational_attainment', 50)->nullable();

            $table->timestamps();

            $table->index('household_id');
            $table->index('nuclear_family_id', 'idx_fm_nuclear_family');
        });

        // Add virtual computed columns that Blueprint doesn't support natively
        DB::statement('ALTER TABLE `family_members`
            ADD COLUMN `age` tinyint(4)
                GENERATED ALWAYS AS (TIMESTAMPDIFF(YEAR, `birthday`, CURDATE())) VIRTUAL
                AFTER `birthday`'
        );
        DB::statement('ALTER TABLE `family_members`
            ADD COLUMN `is_senior_citizen` tinyint(1)
                GENERATED ALWAYS AS (TIMESTAMPDIFF(YEAR, `birthday`, CURDATE()) >= 60) VIRTUAL
                AFTER `is_student`'
        );

        // ─────────────────────────────────────────────────────────────────────
        // 4. family_member_details  (1-to-1 with family_members)
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('family_member_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_member_id')
                  ->constrained('family_members')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            // Matches form column order: LGBTQIA → Vulnerable Sector → Employment
            $table->tinyInteger('is_lgbtqia')->default(0);
            $table->string('vulnerable_sector', 30)->nullable()
                  ->comment('None, Senior, PWD, Solo Parent, 4Ps Member, Young, Old');
            $table->tinyInteger('vuln_registered')->nullable()
                  ->comment('1=Registered, 0=Unregistered, NULL=N/A');
            $table->string('vuln_id_number', 50)->nullable()
                  ->comment('PWD/Senior/4Ps ID or household ID');
            $table->string('employment_status', 30)->nullable()
                  ->comment('Unemployed, Employed, Part-time, Full-time, Self-employed, Pension, Freelance, Other');
            $table->string('job_title', 100)->nullable();

            $table->timestamps();

            $table->unique('family_member_id', 'uq_detail_member');
        });

        // ─────────────────────────────────────────────────────────────────────
        // 5. household_risk_profiles  (1-to-1 with households)
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('household_risk_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')
                  ->constrained('households')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            // Section 3A — Disaster Awareness & Access
            $table->tinyInteger('early_warning')->default(0)->comment('1=Yes, 0=No');
            $table->string('ews_sources', 100)->nullable()->comment('Comma-separated: tv,radio,brgy,other');
            $table->tinyInteger('hazard_awareness')->default(0)->comment('1=Yes, 0=No');

            // Section 3B — Economic & Social Profile
            $table->decimal('income_average', 12, 2)->nullable();
            $table->unsignedTinyInteger('literacy_rate')->nullable()->comment('0–100 percent');
            $table->tinyInteger('financial_assistance')->default(0)->comment('1=Yes, 0=No');
            $table->tinyInteger('access_info')->default(0)->comment('1=Yes, 0=No');
            $table->tinyInteger('relocate_willingness')->default(0)->comment('1=Yes, 0=No');
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->unique('household_id', 'uq_risk_household');
        });
    }

    // ── Roll back: drop in reverse FK order ───────────────────────────────────
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('household_risk_profiles');
        Schema::dropIfExists('family_member_details');
        Schema::dropIfExists('family_members');
        Schema::dropIfExists('nuclear_families');
        Schema::dropIfExists('households');
        Schema::enableForeignKeyConstraints();
    }
};