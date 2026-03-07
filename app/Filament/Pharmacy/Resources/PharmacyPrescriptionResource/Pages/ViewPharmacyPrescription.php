<?php

namespace App\Filament\Pharmacy\Resources\PharmacyPrescriptionResource\Pages;

use App\Filament\Pharmacy\Resources\PharmacyPrescriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPharmacyPrescription extends ViewRecord
{
    protected static string $resource = PharmacyPrescriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('dispense')
                ->label('صرف الروشتة')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn ($record) => $record->status === 'pending')
                ->action(function () {
                    $this->record->update(['status' => 'dispensed']);
                    
                    \Filament\Notifications\Notification::make()
                        ->title('تم صرف الروشتة بنجاح')
                        ->success()
                        ->send();
                        
                    return redirect()->route('filament.pharmacy.resources.pharmacy-prescriptions.index');
                }),

            Actions\Action::make('print')
                ->label('طباعة')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn ($record) => route('prescription.print', $record)),
        ];
    }
}
