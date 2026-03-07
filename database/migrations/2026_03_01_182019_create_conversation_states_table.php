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
        Schema::create('conversation_states', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->foreignId('patient_id')->nullable()->constrained()->nullOnDelete();
            $table->string('current_state')->default('welcome');
            $table->json('context')->nullable();
            $table->integer('step')->default(0);
            $table->timestamp('last_interaction_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index('phone');
            $table->index('current_state');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_states');
    }
};
