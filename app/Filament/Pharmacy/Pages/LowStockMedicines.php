<?php

namespace App\Filament\Pharmacy\Pages;

use App\Models\PharmacyInventory;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class LowStockMedicines extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-archive-box-x-mark';

    protected string $view = 'filament.pharmacy.pages.low-stock-medicines';

    protected static ?string $navigationLabel = 'الأدوية القليلة';

    protected static ?string $title = 'الأدوية القليلة - تحتاج إعادة طلب';

    protected static ?int $navigationSort = 2;

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
                    ->whereColumn('quantity', '<=', 'reorder_level')
                    ->orderBy('quantity', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('medicine.name_ar')
                    ->label('الدواء')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('medicine.category')
                    ->label('الفئة')
                    ->badge()
                    ->translateLabel(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('الكمية الحالية')
                    ->badge()
                    ->color('danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reorder_level')
                    ->label('حد إعادة الطلب')
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('suggested_order')
                    ->label('الكمية المقترح طلبها')
                    ->badge()
                    ->color('success')
                    ->state(fn ($record) => max(50, $record->reorder_level * 3 - $record->quantity)),

                Tables\Columns\TextColumn::make('unit_price')
                    ->label('سعر الوحدة')
                    ->money('EGP'),

                Tables\Columns\TextColumn::make('estimated_cost')
                    ->label('التكلفة المتوقعة')
                    ->money('EGP')
                    ->state(fn ($record) => $record->unit_price * max(50, $record->reorder_level * 3 - $record->quantity)),

                Tables\Columns\TextColumn::make('supplier')
                    ->label('المورد الأخير')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Action::make('quick_restock')
                    ->label('طلب سريع')
                    ->icon('heroicon-o-shopping-cart')
                    ->color('success')
                    ->form([
                        TextInput::make('quantity')
                            ->label('الكمية المطلوبة')
                            ->numeric()
                            ->required()
                            ->default(fn ($record) => max(50, $record->reorder_level * 3 - $record->quantity))
                            ->minValue(1),

                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(2),
                    ])
                    ->action(function (array $data, $record) {
                        // هنا يمكن إضافة منطق إنشاء طلب شراء
                        \Filament\Notifications\Notification::make()
                            ->title('تم إنشاء طلب الشراء')
                            ->body("تم طلب {$data['quantity']} وحدة من {$record->medicine->name_ar}")
                            ->success()
                            ->send();
                    }),

                Action::make('add_stock')
                    ->label('إضافة مخزون')
                    ->icon('heroicon-o-plus-circle')
                    ->color('info')
                    ->form([
                        TextInput::make('quantity')
                            ->label('الكمية المضافة')
                            ->numeric()
                            ->required()
                            ->minValue(1),

                        Textarea::make('reason')
                            ->label('السبب')
                            ->required()
                            ->default('استلام شحنة جديدة'),
                    ])
                    ->action(function (array $data, PharmacyInventory $record) {
                        $record->update([
                            'quantity' => $record->quantity + $data['quantity']
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('تم تحديث المخزون')
                            ->body("تمت إضافة {$data['quantity']} وحدة إلى {$record->medicine->name_ar}")
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkAction::make('create_purchase_order')
                    ->label('إنشاء أمر شراء جماعي')
                    ->icon('heroicon-o-shopping-cart')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($records) {
                        $totalItems = $records->count();
                        $totalCost = $records->sum(fn ($record) => 
                            $record->unit_price * max(50, $record->reorder_level * 3 - $record->quantity)
                        );
                        
                        \Filament\Notifications\Notification::make()
                            ->title('تم إنشاء أمر شراء جماعي')
                            ->body("عدد الأصناف: {$totalItems} | التكلفة المتوقعة: " . number_format($totalCost, 2) . " ج.م")
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyStateHeading('لا توجد أدوية قليلة')
            ->emptyStateDescription('جميع الأدوية متوفرة بكميات كافية')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->poll('30s');
    }
}
