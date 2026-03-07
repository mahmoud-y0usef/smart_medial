<?php

namespace App\Filament\Clinic\Resources;

use App\Enums\QueueStatus;
use App\Filament\Clinic\Resources\QueueResource\Pages;
use App\Models\QueueEntry;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class QueueResource extends Resource
{
    protected static ?string $model = QueueEntry::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationLabel = 'قائمة الانتظار';

    protected static ?string $modelLabel = 'مريض';

    protected static ?string $pluralModelLabel = 'قائمة الانتظار';

    protected static string | UnitEnum | null $navigationGroup = 'قائمة الانتظار';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // View-only resource for queue management
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                // Only show entries for current user's clinic
                $query->whereHas('appointment', function (Builder $q) {
                    $q->whereHas('doctor', function (Builder $doctorQuery) {
                        $doctorQuery->where('user_id', auth()->id());
                    });
                })->where('status', '!=', QueueStatus::Completed);
            })
            ->columns([
                TextColumn::make('queue_number')
                    ->label('رقم الدور')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('appointment.patient.user.name')
                    ->label('اسم المريض')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('appointment.patient.user.phone')
                    ->label('رقم الهاتف')
                    ->searchable(),
                TextColumn::make('appointment.priority')
                    ->label('الأولوية')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 8 => 'danger',
                        $state >= 5 => 'warning',
                        default => 'success',
                    })
                    ->sortable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('وقت الانتظار')
                    ->since()
                    ->sortable(),
                TextColumn::make('called_at')
                    ->label('وقت الاستدعاء')
                    ->dateTime('H:i')
                    ->placeholder('لم يُستدعى بعد')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(QueueStatus::class),
                Tables\Filters\Filter::make('high_priority')
                    ->label('أولوية عالية')
                    ->query(fn (Builder $query) => $query->whereHas('appointment', fn ($q) => $q->where('priority', '>=', 8))),
            ])
            ->actions([
                Action::make('call')
                    ->label('استدعاء')
                    ->icon(Heroicon::PhoneArrowUpRight)
                    ->color('success')
                    ->visible(fn (QueueEntry $record): bool => $record->status === QueueStatus::Waiting)
                    ->requiresConfirmation()
                    ->action(function (QueueEntry $record) {
                        $record->update([
                            'status' => QueueStatus::Called,
                            'called_at' => now(),
                        ]);

                        // Send WhatsApp notification
                        // $whatsappService->notifyPatientCalled($record);
                    }),
                Action::make('start')
                    ->label('بدء الكشف')
                    ->icon(Heroicon::PlayCircle)
                    ->color('primary')
                    ->visible(fn (QueueEntry $record): bool => $record->status === QueueStatus::Called)
                    ->url(fn (QueueEntry $record): string => ConsultationResource::getUrl('create', ['queue' => $record->id])),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('queue_number', 'asc')
            ->poll('10s'); // Auto-refresh every 10 seconds
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
            'index' => Pages\ListQueues::route('/'),
        ];
    }
}
