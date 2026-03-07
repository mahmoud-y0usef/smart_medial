<?php

namespace App\Models;

use App\Enums\Consultation\AIProvider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Consultation extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $casts = [
        'structured_notes' => 'array',
        'ai_provider' => AIProvider::class,
        'ai_verified' => 'boolean',
        'doctor_approved' => 'boolean',
        'transcribed_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the appointment that owns the consultation
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the doctor for the consultation
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the patient for the consultation
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the prescription for the consultation
     */
    public function prescription(): HasOne
    {
        return $this->hasOne(Prescription::class);
    }

    /**
     * Approve the consultation notes
     */
    public function approve(): void
    {
        $this->update([
            'doctor_approved' => true,
            'approved_at' => now(),
        ]);
    }
}
