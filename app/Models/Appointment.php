<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use HasFactory;

    protected $casts = [
        'scheduled_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'status' => AppointmentStatus::class,
    ];

    /**
     * Get the patient that owns the appointment
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the clinic that owns the appointment
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Get the doctor for the appointment
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the triage assessment
     */
    public function triage(): BelongsTo
    {
        return $this->belongsTo(TriageAssessment::class, 'triage_id');
    }

    /**
     * Get the queue entry
     */
    public function queueEntry(): HasOne
    {
        return $this->hasOne(QueueEntry::class);
    }

    /**
     * Get the consultation
     */
    public function consultation(): HasOne
    {
        return $this->hasOne(Consultation::class);
    }

    /**
     * Check if appointment is waiting
     */
    public function isWaiting(): bool
    {
        return $this->status === AppointmentStatus::Waiting;
    }

    /**
     * Check if appointment is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === AppointmentStatus::Completed;
    }

    /**
     * Mark appointment as started
     */
    public function markAsStarted(): void
    {
        $this->update([
            'status' => AppointmentStatus::InProgress,
            'started_at' => now(),
        ]);
    }

    /**
     * Mark appointment as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => AppointmentStatus::Completed,
            'completed_at' => now(),
        ]);
    }
}
