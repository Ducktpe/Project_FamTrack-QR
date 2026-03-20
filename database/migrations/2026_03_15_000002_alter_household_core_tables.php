<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * ALTER-only migration — no data is lost.
 *
 * households:
 *   - DROP sex, birthday, civil_status, house_number
 *   - Reorder remaining columns to match form sections (1A → 1B → 1C → flags → meta)
 *
 * nuclear_families:
 *   - ADD is_primary (tinyint, default 0)
 *
 * family_members:
 *   - Reorder: civil_status moved to after birthday/age
 *   - ADD qr_code_path (personal QR code for the family head member, separate from household QR)
 *   - ADD is_family_head (boolean flag — exactly one per nuclear family)
 *
 * family_member_details:
 *   - Reorder: is_lgbtqia moved to before vulnerable_sector
 *
 * household_risk_profiles:
 *   - No structural changes (column order is already correct)
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. households ─────────────────────────────────────────────────────

        Schema::table('households', function (Blueprint $table) {
            // Drop the 4 removed fields (only if they still exist)
            $columns = Schema::getColumnListing('households');

            if (in_array('sex', $columns))          $table->dropColumn('sex');
            if (in_array('birthday', $columns))     $table->dropColumn('birthday');
            if (in_array('civil_status', $columns)) $table->dropColumn('civil_status');
            if (in_array('house_number', $columns)) $table->dropColumn('house_number');
        });

        // Reorder columns to match form section layout using raw ALTER statements.
        // MODIFY COLUMN ... AFTER ... only changes display order — data is untouched.
        $householdsOrder = [
            // id, serial_code, qr_code_path stay at top naturally

            // Section 1A — Location & Contact
            "`household_head_name` varchar(150)      NOT NULL                  AFTER `qr_code_path`",
            "`contact_number`      varchar(20)        DEFAULT NULL              AFTER `household_head_name`",
            "`national_id`       varchar(50)        DEFAULT NULL              AFTER `contact_number`",
            "`email`               varchar(150)       DEFAULT NULL              AFTER `national_id`",
            "`barangay`            varchar(100)       NOT NULL                  AFTER `email`",
            "`municipality`        varchar(100)       NOT NULL                  AFTER `barangay`",
            "`province`            varchar(100)       NOT NULL                  AFTER `municipality`",
            "`barangay_area`       varchar(50)        DEFAULT NULL              AFTER `province`",
            "`location`            varchar(255)       DEFAULT NULL              AFTER `barangay_area`",
            "`street_purok`        varchar(100)       DEFAULT NULL              AFTER `location`",
            "`latitude`            decimal(10,7)      DEFAULT NULL              AFTER `street_purok`",
            "`longitude`           decimal(10,7)      DEFAULT NULL              AFTER `latitude`",
            "`coordinates_image`   varchar(255)       DEFAULT NULL              AFTER `longitude`",

            // Section 1B — Housing Unit
            "`year_built`          smallint(5) UNSIGNED DEFAULT NULL            AFTER `coordinates_image`",
            "`housing_type`        varchar(50)        DEFAULT NULL              AFTER `year_built`",
            "`housing_material`    varchar(50)        DEFAULT NULL              AFTER `housing_type`",
            "`ownership_type`      varchar(50)        DEFAULT NULL              AFTER `housing_material`",
            "`electricity_source`  varchar(50)        DEFAULT NULL              AFTER `ownership_type`",

            // Section 1C — Utilities & Sanitation
            "`water_source`        varchar(100)       DEFAULT NULL              AFTER `electricity_source`",
            "`toilet_access`       varchar(100)       DEFAULT NULL              AFTER `water_source`",
            "`waste_disposal`      varchar(50)        DEFAULT NULL              AFTER `toilet_access`",

            // Computed flags
            "`is_4ps_beneficiary`  tinyint(1)         NOT NULL DEFAULT 0        AFTER `waste_disposal`",
            "`is_pwd`              tinyint(1)         NOT NULL DEFAULT 0        AFTER `is_4ps_beneficiary`",
            "`is_senior`           tinyint(1)         NOT NULL DEFAULT 0        AFTER `is_pwd`",
            "`is_solo_parent`      tinyint(1)         NOT NULL DEFAULT 0        AFTER `is_senior`",

            // Meta
            "`status`              enum('active','archived') NOT NULL DEFAULT 'active' AFTER `is_solo_parent`",
            "`encoded_by`          bigint(20) UNSIGNED NOT NULL                AFTER `status`",
            "`approved_by`         bigint(20) UNSIGNED DEFAULT NULL            AFTER `encoded_by`",
        ];

        foreach ($householdsOrder as $definition) {
            DB::statement("ALTER TABLE `households` MODIFY COLUMN {$definition}");
        }

        // ── 2. nuclear_families ───────────────────────────────────────────────

        Schema::table('nuclear_families', function (Blueprint $table) {
            if (!Schema::hasColumn('nuclear_families', 'is_primary')) {
                $table->boolean('is_primary')
                      ->default(false)
                      ->after('family_head')
                      ->comment('1 = primary/owner family of this household record');
            }
        });

        // ── 3. family_members — reorder civil_status + add personal qr_code_path ─

        DB::statement("ALTER TABLE `family_members`
            MODIFY COLUMN `civil_status` varchar(30) DEFAULT NULL
            AFTER `age`"
        );

        // Personal QR code + head flag for the household head member.
        // qr_code_path  — separate from households.qr_code_path (household-level QR).
        // is_family_head — exactly one member per nuclear family should be flagged 1.
        Schema::table('family_members', function (Blueprint $table) {
            if (!Schema::hasColumn('family_members', 'qr_code_path')) {
                $table->string('qr_code_path', 255)
                      ->nullable()
                      ->after('nuclear_family_id')
                      ->comment('Personal QR code path — only populated for the family head');
            }
            if (!Schema::hasColumn('family_members', 'is_family_head')) {
                $table->boolean('is_family_head')
                      ->default(false)
                      ->after('qr_code_path')
                      ->comment('1 = this member is the head of their nuclear family');
            }
        });

        // ── 4. family_member_details — reorder is_lgbtqia before vuln_sector ─

        DB::statement("ALTER TABLE `family_member_details`
            MODIFY COLUMN `is_lgbtqia` tinyint(4) NOT NULL DEFAULT 0
            AFTER `family_member_id`"
        );

        // ── 5. household_risk_profiles — already in correct order, no changes ─
    }

    public function down(): void
    {
        // ── Reverse nuclear_families ──────────────────────────────────────────
        Schema::table('nuclear_families', function (Blueprint $table) {
            $table->dropColumn('is_primary');
        });

        // ── Restore dropped households columns ────────────────────────────────
        Schema::table('households', function (Blueprint $table) {
            // Re-add as nullable since we no longer have the original data
            $table->enum('sex', ['Male', 'Female'])->nullable()->after('household_head_name');
            $table->date('birthday')->nullable()->after('sex');
            $table->string('civil_status', 30)->nullable()->after('birthday');
            $table->string('house_number', 30)->nullable()->after('civil_status');
        });

        // ── Reverse family_member_details column order ────────────────────────
        DB::statement("ALTER TABLE `family_member_details`
            MODIFY COLUMN `is_lgbtqia` tinyint(4) NOT NULL DEFAULT 0
            AFTER `vuln_id_number`"
        );

        // ── Reverse family_members changes ───────────────────────────────────
        Schema::table('family_members', function (Blueprint $table) {
            $table->dropColumn(['qr_code_path', 'is_family_head']);
        });

        DB::statement("ALTER TABLE `family_members`
            MODIFY COLUMN `civil_status` varchar(30) DEFAULT NULL
            AFTER `relationship`"
        );
    }
};
