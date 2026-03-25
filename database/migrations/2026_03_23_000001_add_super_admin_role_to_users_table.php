<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Expand the role ENUM to include 'super_admin'
        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN role ENUM('super_admin','admin','encoder','staff','auditor')
            NOT NULL DEFAULT 'encoder'
        ");

        // 2. Add 'created_by' to track which super_admin created the account
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('status');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'created_by')) {
                // Check if the foreign key actually exists before dropping
                $foreignKeys = collect(
                    DB::select("
                        SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
                        WHERE TABLE_SCHEMA = DATABASE()
                          AND TABLE_NAME = 'users'
                          AND COLUMN_NAME = 'created_by'
                          AND REFERENCED_TABLE_NAME IS NOT NULL
                    ")
                )->pluck('CONSTRAINT_NAME')->toArray();

                if (in_array('users_created_by_foreign', $foreignKeys)) {
                    $table->dropForeign(['created_by']);
                }

                $table->dropColumn('created_by');
            }
        });

        // Demote any super_admin rows to 'admin' before removing
        // 'super_admin' from the ENUM — otherwise MySQL rejects the
        // ALTER because existing rows would hold an invalid value.
        DB::table('users')
            ->where('role', 'super_admin')
            ->update(['role' => 'admin']);

        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN role ENUM('admin','encoder','staff','auditor')
            NOT NULL DEFAULT 'encoder'
        ");
    }
};