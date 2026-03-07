<?php

namespace App\Services\Booking;

use App\Enums\AppointmentStatus;
use App\Enums\QueueStatus;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\QueueEntry;
use App\Models\TriageAssessment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentBookingService
{
    /**
     * Book an appointment for a patient
     */
    public function bookAppointment(
        Patient $patient,
        Clinic $clinic,
        ?int $triageId = null,
        ?Carbon $scheduledAt = null
    ): Appointment {
        return DB::transaction(function () use ($patient, $clinic, $triageId, $scheduledAt) {
            // Get triage if provided
            $triage = $triageId ? TriageAssessment::find($triageId) : null;

            // Determine priority based on triage
            $priorityLevel = $triage?->isHighPriority() ? 'high' : 'normal';

            // Create appointment
            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'clinic_id' => $clinic->id,
                'doctor_id' => $this->getAvailableDoctor($clinic),
                'triage_id' => $triageId,
                'scheduled_at' => $scheduledAt ?? now(),
                'status' => AppointmentStatus::Scheduled,
                'checked_in_at' => now(), // Auto check-in for WhatsApp bookings
                'is_whatsapp_booking' => true,
                'priority_level' => $priorityLevel,
            ]);

            // Create queue entry
            $queuePosition = $this->calculateQueuePosition($clinic, $priorityLevel);

            $queueEntry = QueueEntry::create([
                'appointment_id' => $appointment->id,
                'clinic_id' => $clinic->id,
                'position' => $queuePosition,
                'status' => QueueStatus::Waiting,
                'estimated_wait_time' => $this->calculateWaitTime($clinic, $queuePosition),
                'estimated_wait_minutes' => $this->calculateWaitTime($clinic, $queuePosition),
            ]);

            // Update patient
            $patient->update([
                'last_whatsapp_interaction' => now(),
            ]);

            return $appointment->load('queueEntry', 'clinic', 'doctor', 'patient');
        });
    }

    /**
     * Get available doctor for clinic
     */
    protected function getAvailableDoctor(Clinic $clinic): ?int
    {
        // Get doctor with least appointments today
        return $clinic->doctors()
            ->whereHas('user', fn ($q) => $q->where('is_active', true))
            ->withCount([
                'appointments' => fn ($q) => $q->whereDate('scheduled_at', today()),
            ])
            ->orderBy('appointments_count')
            ->first()
            ?->id;
    }

    /**
     * Calculate queue position
     */
    protected function calculateQueuePosition(Clinic $clinic, string $priority): int
    {
        $waitingCount = $clinic->queueEntries()
            ->whereIn('status', [QueueStatus::Waiting, QueueStatus::Called])
            ->count();

        // High priority patients get placed before normal priority
        if ($priority === 'high') {
            // Count high priority waiting ahead
            $highPriorityCount = $clinic->queueEntries()
                ->whereIn('status', [QueueStatus::Waiting, QueueStatus::Called])
                ->whereHas('appointment', fn ($q) => $q->where('priority', 'high'))
                ->count();

            return $highPriorityCount + 1;
        }

        return $waitingCount + 1;
    }

    /**
     * Calculate estimated wait time
     */
    protected function calculateWaitTime(Clinic $clinic, int $position): int
    {
        // Average consultation time (in minutes)
        $avgConsultationTime = 15;

        // Get current queue status
        $inConsultationCount = $clinic->queueEntries()
            ->where('status', QueueStatus::InConsultation)
            ->count();

        // If there are people being served, reduce wait time slightly
        $adjustment = $inConsultationCount > 0 ? 5 : 0;

        return max(5, ($position * $avgConsultationTime) - $adjustment);
    }

    /**
     * Get available clinics with their queue status
     */
    public function getAvailableClinics(): array
    {
        $clinics = Clinic::query()
            ->where('is_active', true)
            ->where('approval_status', \App\Enums\ApprovalStatus::Approved)
            ->withCount([
                'queueEntries' => fn ($q) => $q->whereIn('status', [
                    QueueStatus::Waiting,
                    QueueStatus::Called,
                ]),
            ])
            ->get();

        \Log::info('getAvailableClinics query result', [
            'count' => $clinics->count(),
            'clinics' => $clinics->toArray(),
        ]);

        return $clinics
            ->map(function (Clinic $clinic) {
                return [
                    'id' => $clinic->id,
                    'name' => $clinic->name_ar ?? $clinic->name,
                    'waiting_count' => $clinic->queue_entries_count,
                    'estimated_wait' => $this->calculateWaitTime(
                        $clinic,
                        $clinic->queue_entries_count + 1
                    ),
                    'address' => $clinic->full_address ?? $clinic->address,
                    'accepts_emergency' => $clinic->accepts_emergency,
                ];
            })
            ->toArray();
    }

    /**
     * Cancel appointment
     */
    public function cancelAppointment(Appointment $appointment, string $reason = 'Patient cancelled'): bool
    {
        return DB::transaction(function () use ($appointment, $reason) {
            $appointment->update([
                'status' => AppointmentStatus::Cancelled,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            // Remove from queue if exists
            if ($appointment->queueEntry) {
                $appointment->queueEntry->update([
                    'status' => QueueStatus::Cancelled,
                ]);

                // Reorder remaining queue
                $this->reorderQueue($appointment->clinic_id);
            }

            return true;
        });
    }

    /**
     * Reorder queue after cancellation
     */
    protected function reorderQueue(int $clinicId): void
    {
        $waitingEntries = QueueEntry::where('clinic_id', $clinicId)
            ->whereIn('status', [QueueStatus::Waiting, QueueStatus::Called])
            ->orderBy('position')
            ->get();

        $position = 1;
        foreach ($waitingEntries as $entry) {
            $entry->update([
                'position' => $position,
                'estimated_wait_time' => $this->calculateWaitTime(
                    Clinic::find($clinicId),
                    $position
                ),
            ]);
            $position++;
        }
    }

    /**
     * Get appointment details for confirmation
     */
    public function getAppointmentDetails(Appointment $appointment): array
    {
        return [
            'appointment_id' => $appointment->id,
            'clinic_name' => $appointment->clinic->name,
            'clinic_address' => $appointment->clinic->address ?? $appointment->clinic->city,
            'doctor_name' => $appointment->doctor?->user?->name ?? 'سيتم تحديده',
            'date' => $appointment->scheduled_at->format('Y-m-d'),
            'time' => $appointment->scheduled_at->format('H:i'),
            'queue_number' => $appointment->queueEntry->position,
            'estimated_wait' => $appointment->queueEntry->estimated_wait_time,
            'tracking_url' => route('queue.track', ['appointment' => $appointment->id]),
            'priority' => $appointment->priority_level,
        ];
    }

    /**
     * Reschedule appointment
     */
    public function rescheduleAppointment(Appointment $appointment, Carbon $newDate): bool
    {
        return $appointment->update([
            'scheduled_at' => $newDate,
            'status' => AppointmentStatus::Scheduled,
        ]);
    }

    /**
     * Get patient's upcoming appointments
     */
    public function getPatientAppointments(Patient $patient): array
    {
        return $patient->appointments()
            ->with(['clinic', 'doctor', 'queueEntry'])
            ->whereIn('status', [
                AppointmentStatus::Scheduled,
                AppointmentStatus::Confirmed,
                AppointmentStatus::Waiting,
            ])
            ->orderBy('scheduled_at')
            ->get()
            ->map(fn ($apt) => $this->getAppointmentDetails($apt))
            ->toArray();
    }
}
