<?php

namespace App\Models;

use App\Enums\QueueStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QueueEntry extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => QueueStatus::class,
        'called_at' => 'datetime',
        'entered_at' => 'datetime',
        'completed_at' => 'datetime',
        'skipped_at' => 'datetime',
    ];

    /**
     * Get the appointment that owns the queue entry
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the clinic that owns the queue entry
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Call the patient
     */
    public function call(): void
    {
        $this->update([
            'status' => QueueStatus::Called,
            'called_at' => now(),
        ]);
    }

    /**
     * Mark as in consultation
     */
    public function startConsultation(): void
    {
        $this->update([
            'status' => QueueStatus::InConsultation,
            'entered_at' => now(),
        ]);
    }

    /**
     * Mark as completed
     */
    public function complete(): void
    {
        $this->update([
            'status' => QueueStatus::Completed,
            'completed_at' => now(),
        ]);
    }
}
