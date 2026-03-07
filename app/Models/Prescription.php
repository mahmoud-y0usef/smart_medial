<?php

namespace App\Models;

use App\Enums\PrescriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'status' => PrescriptionStatus::class,
        'is_emergency' => 'boolean',
        'valid_until' => 'date',
        'dispensed_at' => 'datetime',
        'total_price' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Prescription $prescription) {
            $prescription->prescription_number = 'RX' . now()->format('YmdHis') . rand(1000, 9999);
            $prescription->valid_until = now()->addDays(config('medical.prescription.qr_expiry_days', 30));
        });
    }

    /**
     * Get the consultation that owns the prescription
     */
    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    /**
     * Get the patient that owns the prescription
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor that created the prescription
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the pharmacy that filled the prescription
     */
    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }

    /**
     * Get the medicines for the prescription
     */
    public function medicines(): HasMany
    {
        return $this->hasMany(PrescriptionMedicine::class);
    }

    /**
     * Get the prescription medicines (alias for medicines relationship)
     */
    public function prescriptionMedicines(): HasMany
    {
        return $this->hasMany(PrescriptionMedicine::class);
    }

    /**
     * Check if prescription is expired
     */
    public function isExpired(): bool
    {
        return $this->valid_until < now();
    }

    /**
     * Check if prescription is dispensed
     */
    public function isDispensed(): bool
    {
        return $this->status === PrescriptionStatus::Delivered;
    }

    /**
     * Mark prescription as dispensed
     */
    public function markAsDispensed(Pharmacy $pharmacy): void
    {
        $this->update([
            'status' => PrescriptionStatus::Delivered,
            'pharmacy_id' => $pharmacy->id,
            'dispensed_at' => now(),
        ]);
    }
}
