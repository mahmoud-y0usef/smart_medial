<?php

namespace App\Filament\Resources;

use App\Enums\AppointmentStatus;
use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use BackedEnum;
use Filament\Forms;
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

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'المواعيد';

    protected static ?string $modelLabel = 'موعد';

    protected static ?string $pluralModelLabel = 'المواعيد';

    protected static string | UnitEnum | null $navigationGroup = 'إدارة المنصة';

    protected static ?int $navigationSort = 4;

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
                TextColumn::make('queue_number')
                    ->label('رقم الدور')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('patient.name')
                    ->label('المريض')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('clinic.name')
                    ->label('العيادة')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('doctor.name')
                    ->label('الطبيب')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->sortable(),
                TextColumn::make('priority')
                    ->label('الأولوية')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 8 => 'danger',
                        $state >= 5 => 'warning',
                        default => 'success',
                    })
                    ->sortable(),
                TextColumn::make('scheduled_at')
                    ->label('الموعد')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('تاريخ الحجز')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(AppointmentStatus::class),
                SelectFilter::make('clinic_id')
                    ->label('العيادة')
                    ->relationship('clinic', 'name'),
                Tables\Filters\Filter::make('high_priority')
                    ->label('أولوية عالية')
                    ->query(fn ($query) => $query->where('priority', '>=', 8)),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('scheduled_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('معلومات الموعد')
                    ->schema([
                        TextEntry::make('queue_number')
                            ->label('رقم الدور')
                            ->badge(),
                        TextEntry::make('status')
                            ->label('الحالة')
                            ->badge(),
                        TextEntry::make('scheduled_at')
                            ->label('الموعد المحدد')
                            ->dateTime('Y-m-d H:i'),
                        TextEntry::make('priority')
                            ->label('الأولوية')
                            ->badge()
                            ->color(fn (int $state): string => match (true) {
                                $state >= 8 => 'danger',
                                $state >= 5 => 'warning',
                                default => 'success',
                            }),
                    ])->columns(2),

                Section::make('معلومات المريض')
                    ->schema([
                        TextEntry::make('patient.name')
                            ->label('اسم المريض'),
                        TextEntry::make('patient.phone')
                            ->label('رقم الهاتف'),
                        TextEntry::make('patient.age')
                            ->label('العمر')
                            ->suffix(' سنة'),
                    ])->columns(3),

                Section::make('معلومات العيادة والطبيب')
                    ->schema([
                        TextEntry::make('clinic.name')
                            ->label('العيادة'),
                        TextEntry::make('clinic.city')
                            ->label('المدينة'),
                        TextEntry::make('doctor.name')
                            ->label('الطبيب'),
                        TextEntry::make('doctor.specialization')
                            ->label('التخصص'),
                    ])->columns(2),

                Section::make('التقييم الأولي')
                    ->schema([
                        TextEntry::make('triageAssessment.severity_level')
                            ->label('مستوى الخطورة')
                            ->badge(),
                        TextEntry::make('triageAssessment.priority_score')
                            ->label('درجة الأولوية'),
                        TextEntry::make('triageAssessment.symptoms')
                            ->label('الأعراض')
                            ->listWithLineBreaks()
                            ->columnSpanFull(),
                    ])->columns(2)
                    ->visible(fn (Appointment $record): bool => $record->triageAssessment !== null),

                Section::make('الأوقات')
                    ->schema([
                        TextEntry::make('checked_in_at')
                            ->label('وقت الوصول')
                            ->dateTime('Y-m-d H:i'),
                        TextEntry::make('started_at')
                            ->label('وقت البدء')
                            ->dateTime('Y-m-d H:i'),
                        TextEntry::make('completed_at')
                            ->label('وقت الانتهاء')
                            ->dateTime('Y-m-d H:i'),
                    ])->columns(3),
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
            'index' => Pages\ListAppointments::route('/'),
            'view' => Pages\ViewAppointment::route('/{record}'),
        ];
    }
}
