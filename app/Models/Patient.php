<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'date_of_birth' => 'date',
        'chronic_diseases' => 'array',
        'allergies' => 'array',
        'current_medications' => 'array',
        'last_whatsapp_interaction' => 'datetime',
    ];

    /**
     * Get the user that owns the patient profile
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the appointments for the patient
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the triage assessments for the patient
     */
    public function triageAssessments(): HasMany
    {
        return $this->hasMany(TriageAssessment::class);
    }

    /**
     * Get the consultations for the patient
     */
    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    /**
     * Get the prescriptions for the patient
     */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    /**
     * Get the conversation state for the patient
     */
    public function conversationState(): HasMany
    {
        return $this->hasMany(ConversationState::class);
    }

    /**
     * Calculate patient age
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }
}
