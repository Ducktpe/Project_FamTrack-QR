<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Run this FIRST — modifies the default Laravel users table
// to add role, status, and last_login_at columns

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'encoder', 'staff', 'auditor'])
                  ->default('encoder')
                  ->after('email');
            $table->enum('status', ['active', 'inactive'])
                  ->default('active')
                  ->after('role');
            $table->timestamp('last_login_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'last_login_at']);
        });
    }
};