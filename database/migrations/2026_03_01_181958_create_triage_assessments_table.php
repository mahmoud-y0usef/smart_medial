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
        Schema::create('triage_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->json('questions_answers');
            $table->decimal('temperature', 4, 1)->nullable();
            $table->integer('pain_level')->nullable();
            $table->json('symptoms')->nullable();
            $table->json('dangerous_symptoms')->nullable();
            $table->boolean('has_chronic_disease')->default(false);
            $table->integer('priority_score');
            $table->string('severity_level');
            $table->text('triage_notes')->nullable();
            $table->text('ai_recommendation')->nullable();
            $table->timestamps();
            
            $table->index('patient_id');
            $table->index('severity_level');
            $table->index('priority_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('triage_assessments');
    }
};
