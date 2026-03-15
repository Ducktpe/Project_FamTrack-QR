<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    	if (Schema::hasTable('audit_logs')) {
        	return;
    	}
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 100);           // e.g. 'created_distribution_event'
            $table->string('model', 100)->nullable(); // e.g. 'DistributionEvent'
            $table->unsignedBigInteger('record_id')->nullable();
            $table->string('affected_name')->nullable();
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('action');
            $table->index('model');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};