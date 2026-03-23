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
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });

        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN role ENUM('admin','encoder','staff','auditor')
            NOT NULL DEFAULT 'encoder'
        ");
    }
};
