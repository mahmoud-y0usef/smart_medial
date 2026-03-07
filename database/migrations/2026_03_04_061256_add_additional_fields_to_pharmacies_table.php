<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pharmacies', function (Blueprint $table) {
            // Add Arabic name column
            $table->string('name_ar')->nullable()->after('name');
            
            // Rename name to name for consistency (it's actually in Arabic already, but will keep both)
            
            // Add email field
            $table->string('email')->nullable()->after('phone');
            
            // Add specific opening/closing times instead of working_hours JSON
            $table->time('opening_time')->nullable()->after('working_hours');
            $table->time('closing_time')->nullable()->after('opening_time');
            
            // Rename 24_hours to is_24_hours for consistency
            $table->boolean('is_24_hours')->default(false)->after('closing_time');
            
            // Add accepts_insurance field
            $table->boolean('accepts_insurance')->default(true)->after('is_24_hours');
        });
        
        // Rename the confusing 24_hours column
        Schema::table('pharmacies', function (Blueprint $table) {
            $table->dropColumn('24_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pharmacies', function (Blueprint $table) {
            $table->boolean('24_hours')->default(false);
        });
        
        Schema::table('pharmacies', function (Blueprint $table) {
            $table->dropColumn([
                'name_ar',
                'email',
                'opening_time',
                'closing_time',
                'is_24_hours',
                'accepts_insurance',
            ]);
        });
    }
};
