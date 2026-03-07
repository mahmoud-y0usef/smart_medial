<?php

namespace App\Filament\Resources;

use App\Enums\PrescriptionStatus;
use App\Filament\Resources\PrescriptionResource\Pages;
use App\Models\Prescription;
use BackedEnum;
use Filament\Forms;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class PrescriptionResource extends Resource
{
    protected static ?string $model = Prescription::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'الروشتات';

    protected static ?string $modelLabel = 'روشتة';

    protected static ?string $pluralModelLabel = 'الروشتات';

    protected static string | UnitEnum | null $navigationGroup = 'إدارة المنصة';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // View-only resource
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('prescription_number')
                    ->label('رقم الروشتة')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('patient.name')
                    ->label('المريض')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('doctor.name')
                    ->label('الطبيب')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('consultation.clinic.name')
                    ->label('العيادة')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->sortable(),
                TextColumn::make('valid_until')
                    ->label('صالح حتى')
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('تاريخ الإصدار')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(PrescriptionStatus::class),
                Tables\Filters\Filter::make('expired')
                    ->label('منتهية الصلاحية')
                    ->query(fn ($query) => $query->where('valid_until', '<', now())),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
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
                    ])->columns(2),

                Section::make('معلومات المريض والطبيب')
                    ->schema([
                        TextEntry::make('patient.name')
                            ->label('المريض'),
                        TextEntry::make('patient.phone')
                            ->label('هاتف المريض'),
                        TextEntry::make('doctor.name')
                            ->label('الطبيب'),
                        TextEntry::make('consultation.clinic.name')
                            ->label('العيادة'),
                    ])->columns(2),

                Section::make('الأدوية')
                    ->schema([
                        TextEntry::make('medicines.name_ar')
                            ->label('الأدوية')
                            ->listWithLineBreaks()
                            ->bulleted(),
                    ]),

                Section::make('رمز QR')
                    ->schema([
                        ImageEntry::make('qr_code')
                            ->label('رمز QR')
                            ->size(200),
                        TextEntry::make('qr_signature')
                            ->label('التوقيع الرقمي')
                            ->columnSpanFull()
                            ->copyable(),
                    ])->columns(1),

                Section::make('معلومات الصرف')
                    ->schema([
                        TextEntry::make('pharmacy.name')
                            ->label('الصيدلية'),
                        TextEntry::make('dispensed_at')
                            ->label('تم الصرف في')
                            ->dateTime('Y-m-d H:i'),
                        IconEntry::make('is_dispensed')
                            ->label('تم الصرف')
                            ->boolean(),
                    ])->columns(3)
                    ->visible(fn (Prescription $record): bool => $record->is_dispensed),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrescriptions::route('/'),
            'view' => Pages\ViewPrescription::route('/{record}'),
        ];
    }
}
