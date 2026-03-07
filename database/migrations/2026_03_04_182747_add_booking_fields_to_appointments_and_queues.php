<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add fields to appointments table
        Schema::table('appointments', function (Blueprint $table) {
            $table->boolean('is_whatsapp_booking')->default(false)->after('status');
            $table->string('priority_level')->default('normal')->after('priority'); // 'high', 'normal', 'low'
        });

        // Add alias column to queue_entries for backward compatibility
        Schema::table('queue_entries', function (Blueprint $table) {
            // Add estimated_wait_time as alias/computed column
            $table->integer('estimated_wait_time')->nullable()->after('estimated_wait_minutes');
        });

        // Copy existing estimated_wait_minutes to estimated_wait_time
        DB::statement('UPDATE queue_entries SET estimated_wait_time = estimated_wait_minutes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['is_whatsapp_booking', 'priority_level']);
        });

        Schema::table('queue_entries', function (Blueprint $table) {
            $table->dropColumn('estimated_wait_time');
        });
    }
};
