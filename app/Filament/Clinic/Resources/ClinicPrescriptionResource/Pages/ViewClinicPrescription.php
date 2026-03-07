<?php

namespace App\Filament\Clinic\Resources\ClinicPrescriptionResource\Pages;

use App\Filament\Clinic\Resources\ClinicPrescriptionResource;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewClinicPrescription extends ViewRecord
{
    protected static string $resource = ClinicPrescriptionResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('معلومات الروشتة')
                    ->schema([
                        TextEntry::make('prescription_number')
                            ->label('رقم الروشتة'),
                        TextEntry::make('status')
                            ->label('الحالة')
                            ->badge(),
                        TextEntry::make('created_at')
                            ->label('تاريخ الإصدار')
                            ->dateTime('Y-m-d H:i'),
                        TextEntry::make('valid_until')
                            ->label('صالح حتى')
                            ->date('Y-m-d'),
                    ])
                    ->columns(2),

                Section::make('معلومات المريض والطبيب')
                    ->schema([
                        TextEntry::make('consultation.appointment.patient.user.name')
                            ->label('اسم المريض'),
                        TextEntry::make('consultation.appointment.patient.user.phone')
                            ->label('رقم الهاتف'),
                        TextEntry::make('doctor.user.name')
                            ->label('الطبيب'),
                        TextEntry::make('doctor.clinic.name')
                            ->label('العيادة'),
                    ])
                    ->columns(2),

                Section::make('الأدوية')
                    ->schema([
                        TextEntry::make('prescriptionMedicines')
                            ->label('')
                            ->listWithLineBreaks()
                            ->formatStateUsing(function ($record) {
                                return $record->prescriptionMedicines->map(function ($pm) {
                                    return "• {$pm->medicine->name_ar} - {$pm->dosage} - {$pm->frequency} - {$pm->duration}" .
                                           ($pm->instructions ? " ({$pm->instructions})" : '');
                                })->implode("\n");
                            })
                            ->columnSpanFull(),
                    ]),

                Section::make('رمز QR والتوقيع الرقمي')
                    ->schema([
                        ImageEntry::make('qr_code')
                            ->label('رمز QR')
                            ->size(200),
                        TextEntry::make('digital_signature')
                            ->label('التوقيع الرقمي')
                            ->copyable()
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Section::make('ملاحظات')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('ملاحظات إضافية')
                            ->placeholder('لا توجد ملاحظات')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->notes),

                Section::make('معلومات الصرف')
                    ->schema([
                        TextEntry::make('pharmacy.name')
                            ->label('الصيدلية'),
                        TextEntry::make('dispensed_at')
                            ->label('تاريخ الصرف')
                            ->dateTime('Y-m-d H:i'),
                        TextEntry::make('dispensed_by')
                            ->label('الصيدلي'),
                    ])
                    ->columns(3)
                    ->visible(fn ($record) => $record->dispensed_at),
            ]);
    }
}
