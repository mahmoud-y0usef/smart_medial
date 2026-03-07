<?php

namespace App\Filament\Clinic\Resources;

use App\Enums\AppointmentStatus;
use App\Enums\QueueStatus;
use App\Filament\Clinic\Resources\ConsultationResource\Pages;
use App\Models\Consultation;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
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

class ConsultationResource extends Resource
{
    protected static ?string $model = Consultation::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'الكشوفات الطبية';

    protected static ?string $modelLabel = 'كشف طبي';

    protected static ?string $pluralModelLabel = 'الكشوفات الطبية';

    protected static string | UnitEnum | null $navigationGroup = 'الكشوفات';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('معلومات المريض')
                    ->schema([
                        TextInput::make('appointment.patient.user.name')
                            ->label('اسم المريض')
                            ->disabled(),
                        TextInput::make('appointment.queue_entry.queue_number')
                            ->label('رقم الدور')
                            ->disabled(),
                        Hidden::make('appointment_id'),
                        Hidden::make('doctor_id')
                            ->default(fn () => auth()->user()->doctor?->id),
                    ])
                    ->columns(2)
                    ->visible(fn ($livewire) => $livewire instanceof Pages\CreateConsultation),

                Section::make('بيانات الكشف')
                    ->description('يمكنك استخدام ميزة AI Scribe لتسجيل الكشف صوتياً')
                    ->schema([
                        Textarea::make('chief_complaint')
                            ->label('الشكوى الرئيسية')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('examination_findings')
                            ->label('نتائج الفحص')
                            ->rows(4)
                            ->columnSpanFull(),
                        Textarea::make('diagnosis')
                            ->label('التشخيص')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('treatment_plan')
                            ->label('خطة العلاج')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
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
                // Only show consultations for current user's clinic
                $query->whereHas('doctor', function (Builder $q) {
                    $q->where('user_id', auth()->id());
                });
            })
            ->columns([
                TextColumn::make('appointment.patient.user.name')
                    ->label('المريض')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('chief_complaint')
                    ->label('الشكوى الرئيسية')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('diagnosis')
                    ->label('التشخيص')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('doctor.user.name')
                    ->label('الطبيب')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('تاريخ الكشف')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                // No filters needed - doctor only sees their own consultations
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
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
            'index' => Pages\ListConsultations::route('/'),
            'create' => Pages\CreateConsultation::route('/create'),
            'view' => Pages\ViewConsultation::route('/{record}'),
            'edit' => Pages\EditConsultation::route('/{record}/edit'),
        ];
    }
}
