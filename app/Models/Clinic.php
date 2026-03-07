<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Clinic extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $casts = [
        'approval_status' => ApprovalStatus::class,
        'working_hours' => 'array',
        'specializations' => 'array',
        'accepts_emergency' => 'boolean',
        'is_active' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the user that owns the clinic
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the doctors for the clinic
     */
    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    /**
     * Get the receptionists for the clinic
     */
    public function receptionists(): HasMany
    {
        return $this->hasMany(User::class, 'clinic_id')
            ->where('role', \App\Enums\UserRole::Receptionist);
    }

    /**
     * Get the appointments for the clinic
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the queue entries for the clinic
     */
    public function queueEntries(): HasMany
    {
        return $this->hasMany(QueueEntry::class);
    }

    /**
     * Check if clinic is approved
     */
    public function isApproved(): bool
    {
        return $this->approval_status === ApprovalStatus::Approved;
    }

    /**
     * Check if clinic is pending approval
     */
    public function isPending(): bool
    {
        return $this->approval_status === ApprovalStatus::Pending;
    }

    /**
     * Approve the clinic
     */
    public function approve(): void
    {
        $this->update([
            'approval_status' => ApprovalStatus::Approved,
            'approved_at' => now(),
        ]);
    }

    /**
     * Reject the clinic
     */
    public function reject(string $reason): void
    {
        $this->update([
            'approval_status' => ApprovalStatus::Rejected,
            'rejection_reason' => $reason,
        ]);
    }
}
