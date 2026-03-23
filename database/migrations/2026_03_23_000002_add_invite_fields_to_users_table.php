<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Personal Gmail — used only for sending invites/notifications
            $table->string('personal_email')->nullable()->after('email');

            // Generated account code e.g. "A001", "B023"
            $table->string('account_code', 10)->nullable()->after('personal_email');

            // Invite token for the setup link (hashed)
            $table->string('invite_token')->nullable()->after('account_code');

            // When the invite link expires (24 hours after sending)
            $table->timestamp('invite_expires_at')->nullable()->after('invite_token');

            // Track whether user has completed their account setup
            $table->boolean('is_setup_complete')->default(false)->after('invite_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'personal_email',
                'account_code',
                'invite_token',
                'invite_expires_at',
                'is_setup_complete',
            ]);
        });
    }
};
