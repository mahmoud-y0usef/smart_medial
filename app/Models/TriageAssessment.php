<?php

namespace App\Models;

use App\Enums\Triage\SeverityLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TriageAssessment extends Model
{
    use HasFactory;

    protected $casts = [
        'questions_answers' => 'array',
        'symptoms' => 'array',
        'dangerous_symptoms' => 'array',
        'has_chronic_disease' => 'boolean',
        'severity_level' => SeverityLevel::class,
    ];

    /**
     * Get the patient that owns the assessment
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the appointments for this triage
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'triage_id');
    }

    /**
     * Check if assessment is high priority
     */
    public function isHighPriority(): bool
    {
        return $this->priority_score >= config('medical.triage.high_priority_min', 8);
    }

    /**
     * Check if assessment is emergency
     */
    public function isEmergency(): bool
    {
        return $this->priority_score >= config('medical.triage.emergency_threshold', 8);
    }
}
