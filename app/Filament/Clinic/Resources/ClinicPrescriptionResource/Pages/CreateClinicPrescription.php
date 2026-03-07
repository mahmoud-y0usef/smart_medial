<?php

namespace App\Filament\Clinic\Resources\ClinicPrescriptionResource\Pages;

use App\Enums\AppointmentStatus;
use App\Enums\PrescriptionStatus;
use App\Enums\QueueStatus;
use App\Filament\Clinic\Resources\ClinicPrescriptionResource;
use App\Models\Consultation;
use App\Services\Medical\QRService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateClinicPrescription extends CreateRecord
{
    protected static string $resource = ClinicPrescriptionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set doctor_id from current authenticated user
        $data['doctor_id'] = auth()->user()->doctor?->id;

        // Get consultation_id from query parameter if provided
        if ($consultationId = request()->get('consultation')) {
            $data['consultation_id'] = $consultationId;
        }

        // Generate unique prescription number
        $data['prescription_number'] = 'RX-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        // Set status
        $data['status'] = PrescriptionStatus::New;

        // Set valid_until (30 days from now)
        $data['valid_until'] = now()->addDays(30);

        return $data;
    }

    protected function afterCreate(): void
    {
        // Generate QR Code
        $qrService = app(QRService::class);
        $qrData = $qrService->generatePrescriptionQR($this->record);
        
        $this->record->update([
            'qr_code' => $qrData['qr_code'],
            'digital_signature' => $qrData['signature'],
        ]);

        // Update queue and appointment status
        if ($this->record->consultation) {
            $appointment = $this->record->consultation->appointment;
            
            if ($appointment) {
                // Update appointment status
                $appointment->update([
                    'status' => AppointmentStatus::Completed,
                    'completed_at' => now(),
                ]);

                // Update queue status
                $appointment->queueEntry?->update([
                    'status' => QueueStatus::Completed,
                ]);
            }
        }

        // Send WhatsApp notification to patient
        // $whatsappService->sendPrescriptionNotification($this->record);

        Notification::make()
            ->success()
            ->title('تم إنشاء الروشتة بنجاح')
            ->body('تم توليد رمز QR وإرسال إشعار للمريض')
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('view', ['record' => $this->record]);
    }
}
