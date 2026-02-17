<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Run this SEVENTH — records every action done in the system
// Who did what, to which record, and when
// Required for DSWD and COA audit compliance

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // --- Who ---
            $table->foreignId('user_id')
                  ->nullable()                              // nullable for system/guest actions
                  ->constrained('users')
                  ->onDelete('set null');

            $table->string('user_name', 150)->nullable();  // snapshot of name at time of action

            // --- What action ---
            $table->string('action', 50);                  // created / updated / deleted / login / logout / scanned

            // --- Which record ---
            $table->string('model', 100)->nullable();       // e.g. Household, FamilyMember, User
            $table->unsignedBigInteger('record_id')->nullable(); // ID of the affected record

            // --- Details ---
            $table->json('old_values')->nullable();         // what the data looked like before
            $table->json('new_values')->nullable();         // what it looks like after

            // --- Where from ---
            $table->string('ip_address', 45)->nullable();  // supports IPv4 and IPv6
            $table->string('user_agent', 255)->nullable();

            $table->timestamp('created_at')->useCurrent();  // audit logs never update, only insert

            // --- Indexes ---
            $table->index('user_id');
            $table->index('action');
            $table->index('model');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};