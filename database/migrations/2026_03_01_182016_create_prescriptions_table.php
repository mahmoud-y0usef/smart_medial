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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pharmacy_id')->nullable()->constrained()->nullOnDelete();
            $table->string('prescription_number')->unique();
            $table->text('qr_code');
            $table->string('qr_signature');
            $table->string('status')->default('new');
            $table->text('notes')->nullable();
            $table->text('special_instructions')->nullable();
            $table->date('valid_until');
            $table->timestamp('dispensed_at')->nullable();
            $table->text('pharmacist_notes')->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->boolean('is_emergency')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('prescription_number');
            $table->index('patient_id');
            $table->index('doctor_id');
            $table->index('pharmacy_id');
            $table->index('status');
            $table->index('valid_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
