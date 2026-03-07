<?php

namespace App\Filament\Pharmacy\Resources;

use App\Filament\Pharmacy\Resources\PharmacyPrescriptionResource\Pages;
use App\Models\Prescription;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class PharmacyPrescriptionResource extends Resource
{
    protected static ?string $model = Prescription::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'الروشتات';

    protected static ?string $modelLabel = 'روشتة';

    protected static ?string $pluralModelLabel = 'الروشتات';

    protected static string | \UnitEnum | null $navigationGroup = 'الروشتات';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('معلومات الروشتة')
                    ->schema([
                        TextInput::make('prescription_number')
                            ->label('رقم الروشتة')
                            ->disabled()
                            ->dehydrated(false),

                        Placeholder::make('patient_name')
                            ->label('اسم المريض')
                            ->content(fn ($record) => $record?->patient?->user?->name ?? '-'),

                        Placeholder::make('doctor_name')
                            ->label('اسم الطبيب')
                            ->content(fn ($record) => $record?->doctor?->user?->name ?? '-'),

                        Placeholder::make('clinic_name')
                            ->label('العيادة')
                            ->content(fn ($record) => $record?->consultation?->clinic?->name ?? '-'),

                        DatePicker::make('created_at')
                            ->label('تاريخ التحرير')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),

                Section::make('الأدوية')
                    ->schema([
                        Repeater::make('medicines')
                            ->relationship('prescriptionMedicines')
                            ->schema([
                                TextInput::make('medicine.name_ar')
                                    ->label('الدواء')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('dosage')
                                    ->label('الجرعة')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('frequency')
                                    ->label('التكرار')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('duration')
                                    ->label('المدة')
                                    ->disabled()
                                    ->dehydrated(false),

                                Textarea::make('instructions')
                                    ->label('التعليمات')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->columnSpanFull(),
                            ])
                            ->columns(4)
                            ->disabled()
                            ->dehydrated(false),
                    ]),

                Section::make('ملاحظات الطبيب')
                    ->schema([
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->disabled()
                            ->dehydrated(false)
                            ->rows(3),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('prescription_number')
                    ->label('رقم الروشتة')
                    ->searchable()
                    ->copyable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('patient.user.name')
                    ->label('اسم المريض')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('doctor.user.name')
                    ->label('الطبيب')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('consultation.clinic.name')
                    ->label('العيادة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('prescriptionMedicines')
                    ->label('عدد الأدوية')
                    ->counts('prescriptionMedicines')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التحرير')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'pending' => 'قيد الانتظار',
                        'dispensed' => 'تم الصرف',
                        'cancelled' => 'ملغي',
                    ]),
            ])
            ->actions([
                Actions\ViewAction::make()
                    ->label('عرض'),
                Actions\Action::make('dispense')
                    ->label('صرف الروشتة')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function (Prescription $record) {
                        $record->update(['status' => 'dispensed']);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('تم صرف الروشتة بنجاح')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPharmacyPrescriptions::route('/'),
            'view' => Pages\ViewPharmacyPrescription::route('/{record}'),
        ];
    }
}
