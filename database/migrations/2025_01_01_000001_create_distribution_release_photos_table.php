<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the distribution_release_photos table.
 *
 * PURPOSE
 * ───────
 * Stores the photo proof taken by distribution staff at the moment a
 * household or family-head QR code is confirmed during an ayuda release.
 * This is a SEPARATE, additive table — it references distribution_logs
 * but does NOT alter it, so existing data and behaviour are untouched.
 *
 * DESIGN NOTES
 * ────────────
 * • qr_type         — distinguishes household vs. family-head QR scans so
 *                     reports can be filtered / aggregated per mode.
 * • family_member_id — nullable: only set when a family-head personal QR
 *                     was scanned; NULL for household-level QR releases.
 * • household_id     — denormalised FK kept here so common queries (e.g.
 *                     "show all photos for household X") avoid an extra
 *                     join through distribution_logs.
 * • photo_path       — relative path from the storage root:
 *                     "distribution_photos/{year}/{month}/{filename}.jpg"
 *                     Full URL = Storage::url($photo_path).
 * • photo_taken_at   — explicit timestamp captured client-side at the
 *                     moment the camera shutter fires; differs from
 *                     created_at which reflects server write time.
 * • taken_by         — the authenticated staff user who confirmed the
 *                     release (auth()->id() in the controller).
 *
 * STORAGE DISK
 * ────────────
 * Photos are written to:
 *   storage/app/public/distribution_photos/
 * Run `php artisan storage:link` once to expose them under /storage/.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribution_release_photos', function (Blueprint $table) {

            $table->id();

            // ── Core reference ──────────────────────────────────────────────
            $table->foreignId('distribution_log_id')
                  ->constrained('distribution_logs')
                  ->cascadeOnDelete();   // photo deleted automatically if log is deleted

            // ── Denormalised shortcuts (avoid joins for common reads) ────────
            $table->foreignId('household_id')
                  ->constrained('households')
                  ->cascadeOnDelete();

            $table->foreignId('family_member_id')
                  ->nullable()          // NULL  → household QR was used
                  ->constrained('family_members')
                  ->nullOnDelete();     // keep photo record even if member is soft-deleted

            // ── QR type segregation ─────────────────────────────────────────
            $table->enum('qr_type', ['household', 'family_head'])
                  ->comment('household = household-level QR card; family_head = personal family-head QR');

            // ── Photo storage ───────────────────────────────────────────────
            $table->string('photo_path')
                  ->comment('Relative path from storage root: distribution_photos/YYYY/MM/filename.jpg');

            $table->timestamp('photo_taken_at')
                  ->comment('Client-side timestamp when the camera shutter fired');

            // ── Audit ───────────────────────────────────────────────────────
            $table->foreignId('taken_by')
                  ->constrained('users')
                  ->restrictOnDelete(); // prevent deleting a user who has photo records

            $table->timestamps(); // created_at = server write time; updated_at rarely changes

            // ── Indexes ─────────────────────────────────────────────────────
            $table->index('distribution_log_id',  'drp_log_idx');
            $table->index('household_id',          'drp_household_idx');
            $table->index('family_member_id',      'drp_member_idx');
            $table->index('taken_by',              'drp_taken_by_idx');
            $table->index('photo_taken_at',        'drp_taken_at_idx');
            $table->index(['qr_type', 'household_id'], 'drp_type_household_idx'); // for per-mode household lookups
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_release_photos');
    }
};
