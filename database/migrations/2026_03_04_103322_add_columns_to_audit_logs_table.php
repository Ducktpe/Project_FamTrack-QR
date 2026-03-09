<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            // Add new columns only if they don't exist yet
            if (!Schema::hasColumn('audit_logs', 'category')) {
                $table->string('category')->default('general')->after('action');
            }
            if (!Schema::hasColumn('audit_logs', 'severity')) {
                $table->enum('severity', ['low', 'medium', 'high'])->default('low')->after('category');
            }
            if (!Schema::hasColumn('audit_logs', 'affected_name')) {
                $table->string('affected_name')->nullable()->after('record_id');
            }
            if (!Schema::hasColumn('audit_logs', 'description')) {
                $table->string('description')->nullable()->after('affected_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn(['category', 'severity', 'affected_name', 'description']);
        });
    }
};