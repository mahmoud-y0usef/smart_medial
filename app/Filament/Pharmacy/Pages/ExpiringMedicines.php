<?php

namespace App\Filament\Pharmacy\Pages;

use App\Models\PharmacyInventory;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ExpiringMedicines extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected string $view = 'filament.pharmacy.pages.expiring-medicines';

    protected static ?string $navigationLabel = 'الأدوية المنتهية قريباً';

    protected static ?string $title = 'الأدوية المنتهية قريباً';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'المخزون';
    }

    public function table(Table $table): Table
    {
        $pharmacist = auth()->user()->pharmacist;

        return $table
            ->query(
                PharmacyInventory::query()
                    ->where('pharmacy_id', $pharmacist?->pharmacy_id)
                    ->where('expiry_date', '<=', now()->addMonths(6))
                    ->orderBy('expiry_date', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('medicine.name_ar')
                    ->label('الدواء')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('medicine.strength')
                    ->label('التركيز'),

                Tables\Columns\TextColumn::make('batch_number')
                    ->label('رقم التشغيلة')
                    ->searchable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('الكمية')
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('تاريخ الانتهاء')
                    ->date('Y-m-d')
                    ->sortable()
                    ->color(fn ($state) => $state->lte(now()->addMonths(3)) ? 'danger' : 'warning')
                    ->icon(fn ($state) => $state->lte(now()->addMonths(3)) ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-exclamation-circle')
                    ->description(fn ($record) => $record->expiry_date->lte(now()) ? 'منتهي الصلاحية' : ($record->expiry_date->lte(now()->addMonths(3)) ? 'ينتهي خلال 3 أشهر' : 'ينتهي خلال 6 أشهر')),

                Tables\Columns\TextColumn::make('unit_price')
                    ->label('سعر الوحدة')
                    ->money('EGP')
                    ->description(fn ($record) => 'إجمالي: ' . number_format($record->quantity * $record->unit_price, 2) . ' ج.م'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'expired' => 'منتهي الصلاحية',
                        '3_months' => 'ينتهي خلال 3 أشهر',
                        '6_months' => 'ينتهي خلال 6 أشهر',
                    ])
                    ->query(function ($query, $state) {
                        if ($state['value'] === 'expired') {
                            return $query->where('expiry_date', '<=', now());
                        } elseif ($state['value'] === '3_months') {
                            return $query->whereBetween('expiry_date', [now(), now()->addMonths(3)]);
                        } elseif ($state['value'] === '6_months') {
                            return $query->whereBetween('expiry_date', [now()->addMonths(3), now()->addMonths(6)]);
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Action::make('mark_expired')
                    ->label('إتلاف')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->expiry_date->lte(now()))
                    ->action(function ($record) {
                        $record->update(['quantity' => 0]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('تم إتلاف الدواء منتهي الصلاحية')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('expiry_date', 'asc')
            ->poll('30s');
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
