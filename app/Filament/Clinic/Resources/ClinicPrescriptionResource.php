<?php

namespace App\Filament\Clinic\Resources;

use App\Enums\AppointmentStatus;
use App\Enums\PrescriptionStatus;
use App\Enums\QueueStatus;
use App\Filament\Clinic\Resources\ClinicPrescriptionResource\Pages;
use App\Models\Medicine;
use App\Models\Prescription;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ClinicPrescriptionResource extends Resource
{
    protected static ?string $model = Prescription::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'الروشتات';

    protected static ?string $modelLabel = 'روشتة';

    protected static ?string $pluralModelLabel = 'الروشتات';

    protected static string | UnitEnum | null $navigationGroup = 'الروشتات';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('معلومات المريض')
                    ->schema([
                        TextInput::make('consultation.appointment.patient.user.name')
                            ->label('اسم المريض')
                            ->disabled(),
                        Hidden::make('consultation_id'),
                        Hidden::make('doctor_id')
                            ->default(fn () => auth()->user()->doctor?->id),
                    ])
                    ->visible(fn ($livewire) => $livewire instanceof Pages\CreateClinicPrescription),

                Section::make('الأدوية')
                    ->schema([
                        Repeater::make('medicines')
                            ->relationship('prescriptionMedicines')
                            ->schema([
                                Select::make('medicine_id')
                                    ->label('الدواء')
                                    ->options(Medicine::pluck('name_ar', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        $medicine = Medicine::find($state);
                                        if ($medicine) {
                                            $set('medicine_name', $medicine->name_ar);
                                        }
                                    }),
                                TextInput::make('dosage')
                                    ->label('الجرعة')
                                    ->required()
                                    ->placeholder('مثال: قرص واحد'),
                                TextInput::make('frequency')
                                    ->label('عدد المرات')
                                    ->required()
                                    ->placeholder('مثال: 3 مرات يومياً'),
                                TextInput::make('duration')
                                    ->label('المدة')
                                    ->required()
                                    ->placeholder('مثال: 5 أيام'),
                                Textarea::make('instructions')
                                    ->label('التعليمات')
                                    ->placeholder('مثال: بعد الأكل')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->addActionLabel('إضافة دواء')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => 
                                Medicine::find($state['medicine_id'])?->name_ar ?? 'دواء جديد'
                            )
                            ->minItems(1)
                            ->defaultItems(1),
                    ]),

                Section::make('ملاحظات')
                    ->schema([
                        Textarea::make('notes')
                            ->label('ملاحظات إضافية')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                // Only show prescriptions for current user's clinic
                $query->whereHas('doctor', function (Builder $q) {
                    $q->where('user_id', auth()->id());
                });
            })
            ->columns([
                TextColumn::make('prescription_number')
                    ->label('رقم الروشتة')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('consultation.appointment.patient.user.name')
                    ->label('المريض')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('prescriptionMedicines')
                    ->label('عدد الأدوية')
                    ->counts('prescriptionMedicines')
                    ->badge(),
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
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListClinicPrescriptions::route('/'),
            'create' => Pages\CreateClinicPrescription::route('/create'),
            'view' => Pages\ViewClinicPrescription::route('/{record}'),
        ];
    }
}
