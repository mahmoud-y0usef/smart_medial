<?php

namespace App\Filament\Clinic\Resources\ConsultationResource\Pages;

use App\Filament\Clinic\Resources\ConsultationResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewConsultation extends ViewRecord
{
    protected static string $resource = ConsultationResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('معلومات المريض')
                    ->schema([
                        TextEntry::make('appointment.patient.user.name')
                            ->label('اسم المريض'),
                        TextEntry::make('appointment.patient.user.phone')
                            ->label('رقم الهاتف'),
                        TextEntry::make('appointment.queue_entry.queue_number')
                            ->label('رقم الدور')
                            ->badge(),
                        TextEntry::make('doctor.user.name')
                            ->label('الطبيب'),
                    ])
                    ->columns(2),

                Section::make('بيانات الكشف')
                    ->schema([
                        TextEntry::make('chief_complaint')
                            ->label('الشكوى الرئيسية')
                            ->columnSpanFull(),
                        TextEntry::make('examination_findings')
                            ->label('نتائج الفحص')
                            ->columnSpanFull(),
                        TextEntry::make('diagnosis')
                            ->label('التشخيص')
                            ->columnSpanFull(),
                        TextEntry::make('treatment_plan')
                            ->label('خطة العلاج')
                            ->columnSpanFull(),
                        TextEntry::make('notes')
                            ->label('ملاحظات إضافية')
                            ->columnSpanFull()
                            ->placeholder('لا توجد ملاحظات'),
                    ]),

                Section::make('معلومات إضافية')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('تاريخ الكشف')
                            ->dateTime('Y-m-d H:i'),
                        TextEntry::make('updated_at')
                            ->label('آخر تحديث')
                            ->dateTime('Y-m-d H:i'),
                    ])
                    ->columns(2),
            ]);
    }
}
