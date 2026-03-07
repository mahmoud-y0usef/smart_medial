<?php

namespace App\Filament\Clinic\Resources\ConsultationResource\Pages;

use App\Enums\AppointmentStatus;
use App\Enums\QueueStatus;
use App\Filament\Clinic\Resources\ClinicPrescriptionResource;
use App\Filament\Clinic\Resources\ConsultationResource;
use App\Models\QueueEntry;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateConsultation extends CreateRecord
{
    protected static string $resource = ConsultationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set doctor_id from current authenticated user
        $data['doctor_id'] = auth()->user()->doctor?->id;

        // Get appointment_id from queue parameter if provided
        if ($queueId = request()->get('queue')) {
            $queueEntry = QueueEntry::find($queueId);
            if ($queueEntry) {
                $data['appointment_id'] = $queueEntry->appointment_id;
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Update queue status to "In Progress"
        if ($queueId = request()->get('queue')) {
            $queueEntry = QueueEntry::find($queueId);
            if ($queueEntry) {
                $queueEntry->update([
                    'status' => QueueStatus::InProgress,
                ]);

                // Update appointment status
                $queueEntry->appointment?->update([
                    'status' => AppointmentStatus::InProgress,
                    'started_at' => now(),
                ]);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return ClinicPrescriptionResource::getUrl('create', [
            'consultation' => $this->record->id,
        ]);
    }
}
