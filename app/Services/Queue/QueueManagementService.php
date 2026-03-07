<?php

namespace App\Services\Queue;

use App\Enums\QueueStatus;
use App\Events\PatientCalled;
use App\Events\QueueUpdated;
use App\Models\Clinic;
use App\Models\QueueEntry;
use App\Services\WhatsApp\MetaWhatsAppService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class QueueManagementService
{
    public function __construct(
        protected MetaWhatsAppService $whatsapp
    ) {}

    /**
     * Get current queue status for a clinic
     */
    public function getQueueStatus(Clinic $clinic): array
    {
        $entries = $clinic->queueEntries()
            ->with(['appointment.patient', 'appointment.doctor'])
            ->whereIn('status', [QueueStatus::Waiting, QueueStatus::Called, QueueStatus::InConsultation])
            ->orderBy('position')
            ->get();

        return [
            'total_waiting' => $entries->where('status', QueueStatus::Waiting)->count(),
            'currently_serving' => $entries->where('status', QueueStatus::InConsultation)->count(),
            'entries' => $entries->map(fn ($entry) => [
                'id' => $entry->id,
                'position' => $entry->position,
                'patient_name' => $entry->appointment->patient->name,
                'patient_phone' => $entry->appointment->patient->phone,
                'status' => $entry->status->value,
                'estimated_wait' => $entry->estimated_wait_time,
                'priority' => $entry->appointment->priority_level,
            ])->toArray(),
        ];
    }

    /**
     * Call next patient in queue
     */
    public function callNextPatient(Clinic $clinic): ?QueueEntry
    {
        /** @var QueueEntry|null $nextEntry */
        $nextEntry = $clinic->queueEntries()
            ->with('appointment.patient')
            ->where('status', QueueStatus::Waiting)
            ->orderBy('position')
            ->first();

        if (! $nextEntry) {
            return null;
        }

        $nextEntry->call();

        // Broadcast event
        broadcast(new PatientCalled($nextEntry))->toOthers();

        // Send WhatsApp notification
        $patient = $nextEntry->appointment->patient;
        if ($patient->phone) {
            $this->whatsapp->sendYourTurnNow($patient->phone);
        }

        return $nextEntry;
    }

    /**
     * Start consultation for a queue entry
     */
    public function startConsultation(QueueEntry $entry): void
    {
        $entry->startConsultation();

        // Update appointment status
        $entry->appointment->update([
            'status' => \App\Enums\AppointmentStatus::InProgress,
            'started_at' => now(),
        ]);

        // Notify next 3 patients about updated wait time
        $this->notifyUpcomingPatients($entry->clinic_id);
    }

    /**
     * Complete consultation
     */
    public function completeConsultation(QueueEntry $entry): void
    {
        $entry->complete();

        // Broadcast event
        broadcast(new QueueUpdated($entry))->toOthers();

        // Update appointment status
        $entry->appointment->update([
            'status' => \App\Enums\AppointmentStatus::Completed,
            'completed_at' => now(),
        ]);

        // Reorder queue
        $this->reorderQueue($entry->clinic_id);

        // Notify next 3 patients
        $this->notifyUpcomingPatients($entry->clinic_id);
    }

    /**
     * Skip a patient in queue
     */
    public function skipPatient(QueueEntry $entry): void
    {
        $entry->update([
            'status' => QueueStatus::Skipped,
            'skipped_at' => now(),
        ]);

        // Move to end of queue
        $maxPosition = QueueEntry::where('clinic_id', $entry->clinic_id)
            ->whereIn('status', [QueueStatus::Waiting, QueueStatus::Called])
            ->max('position');

        $entry->update([
            'position' => $maxPosition + 1,
            'status' => QueueStatus::Waiting,
        ]);

        // Notify patient
        if ($entry->appointment->patient->phone) {
            $message = "⏭ تم تأخير دورك لآخر الطابور.\nالموقع الجديد: {$entry->position}";
            $this->whatsapp->sendMessage($entry->appointment->patient->phone, $message);
        }

        // Notify next patients
        $this->notifyUpcomingPatients($entry->clinic_id);
    }

    /**
     * Reorder queue after changes
     */
    protected function reorderQueue(int $clinicId): void
    {
        $entries = QueueEntry::where('clinic_id', $clinicId)
            ->whereIn('status', [QueueStatus::Waiting, QueueStatus::Called])
            ->orderBy('position')
            ->get();

        $position = 1;
        foreach ($entries as $entry) {
            $entry->update([
                'position' => $position,
                'estimated_wait_time' => $position * 15, // 15 min average
                'estimated_wait_minutes' => $position * 15,
            ]);

            // Broadcast update
            broadcast(new QueueUpdated($entry))->toOthers();

            $position++;
        }
    }

    /**
     * Notify upcoming patients about their queue status
     */
    public function notifyUpcomingPatients(int $clinicId): void
    {
        $upcomingEntries = QueueEntry::where('clinic_id', $clinicId)
            ->with('appointment.patient')
            ->where('status', QueueStatus::Waiting)
            ->orderBy('position')
            ->limit(3)
            ->get();

        foreach ($upcomingEntries as $entry) {
            $patient = $entry->appointment->patient;

            if (! $patient->phone) {
                continue;
            }

            // Check if we've notified recently to avoid spam
            $cacheKey = "queue_notify_{$entry->id}";
            if (Cache::has($cacheKey)) {
                continue;
            }

            // Notify if turn is soon (3 patients or less)
            if ($entry->position <= 3) {
                $this->whatsapp->sendYourTurnSoon(
                    $patient->phone,
                    $entry->estimated_wait_time
                );
            } else {
                // Regular update
                $this->whatsapp->sendQueueUpdate(
                    $patient->phone,
                    $entry->position - 1, // People ahead
                    $entry->estimated_wait_time
                );
            }

            // Cache for 5 minutes to avoid spam
            Cache::put($cacheKey, true, now()->addMinutes(5));
        }
    }

    /**
     * Get patient's position in queue
     */
    public function getPatientQueuePosition(string $phone): ?array
    {
        $entry = QueueEntry::whereHas('appointment.patient', fn ($q) => $q->where('phone', $phone))
            ->whereIn('status', [QueueStatus::Waiting, QueueStatus::Called])
            ->with(['appointment.clinic'])
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $entry) {
            return null;
        }

        return [
            'position' => $entry->position,
            'estimated_wait' => $entry->estimated_wait_time,
            'status' => $entry->status->value,
            'clinic_name' => $entry->appointment->clinic->name_ar,
            'people_ahead' => $entry->position - 1,
        ];
    }

    /**
     * Get queue statistics for a clinic
     */
    public function getQueueStatistics(Clinic $clinic, ?\Carbon\Carbon $date = null): array
    {
        $date = $date ?? today();

        $stats = QueueEntry::where('clinic_id', $clinic->id)
            ->whereDate('created_at', $date)
            ->selectRaw('
                COUNT(*) as total,
                COUNT(CASE WHEN status = ? THEN 1 END) as completed,
                COUNT(CASE WHEN status = ? THEN 1 END) as waiting,
                AVG(CASE WHEN completed_at IS NOT NULL 
                    THEN TIMESTAMPDIFF(MINUTE, created_at, completed_at) 
                END) as avg_wait_time
            ', [QueueStatus::Completed->value, QueueStatus::Waiting->value])
            ->first();

        return [
            'date' => $date->format('Y-m-d'),
            'total_patients' => $stats->total ?? 0,
            'completed' => $stats->completed ?? 0,
            'waiting' => $stats->waiting ?? 0,
            'average_wait_time' => round($stats->avg_wait_time ?? 0),
        ];
    }

    /**
     * Auto-notify patients at intervals
     */
    public function autoNotifyPatients(int $clinicId): void
    {
        $entries = QueueEntry::where('clinic_id', $clinicId)
            ->with('appointment.patient')
            ->where('status', QueueStatus::Waiting)
            ->where('position', '<=', 5) // Only notify first 5
            ->get();

        foreach ($entries as $entry) {
            if (! $entry->appointment->patient->phone) {
                continue;
            }

            $this->whatsapp->sendQueueUpdate(
                $entry->appointment->patient->phone,
                $entry->position - 1,
                $entry->estimated_wait_time
            );
        }
    }
}
