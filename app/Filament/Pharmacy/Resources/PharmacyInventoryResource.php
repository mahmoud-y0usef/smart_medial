<?php

namespace App\Filament\Pharmacy\Resources;

use App\Filament\Pharmacy\Resources\PharmacyInventoryResource\Pages;
use App\Models\PharmacyInventory;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class PharmacyInventoryResource extends Resource
{
    protected static ?string $model = PharmacyInventory::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'المخزون';

    protected static ?string $modelLabel = 'مخزون';

    protected static ?string $pluralModelLabel = 'المخزون';

    protected static string | \UnitEnum | null $navigationGroup = 'المخزون';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('معلومات الدواء')
                    ->schema([
                        Select::make('medicine_id')
                            ->label('الدواء')
                            ->relationship('medicine', 'name_ar')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name_ar')
                                    ->label('اسم الدواء (عربي)')
                                    ->required()
                                    ->maxLength(255),
                                
                                TextInput::make('name_en')
                                    ->label('اسم الدواء (إنجليزي)')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('scientific_name')
                                    ->label('الاسم العلمي')
                                    ->maxLength(255),

                                TextInput::make('active_ingredient')
                                    ->label('المادة الفعالة')
                                    ->required()
                                    ->maxLength(255),

                                Select::make('category')
                                    ->label('الفئة')
                                    ->options([
                                        'Pain Relief' => 'مسكنات',
                                        'Antibiotics' => 'مضادات حيوية',
                                        'Cardiovascular' => 'أمراض القلب',
                                        'Diabetes' => 'السكري',
                                        'Vitamins' => 'فيتامينات',
                                        'Dermatology' => 'أمراض جلدية',
                                        'Ophthalmology' => 'أمراض العيون',
                                    ])
                                    ->required(),

                                Textarea::make('description')
                                    ->label('الوصف')
                                    ->rows(3),

                                Select::make('form')
                                    ->label('الشكل الدوائي')
                                    ->options([
                                        'tablet' => 'أقراص',
                                        'capsule' => 'كبسولات',
                                        'syrup' => 'شراب',
                                        'injection' => 'حقن',
                                        'cream' => 'كريم',
                                        'drops' => 'قطرة',
                                        'other' => 'أخرى',
                                    ])
                                    ->required(),

                                TextInput::make('strength')
                                    ->label('التركيز')
                                    ->maxLength(100),

                                TextInput::make('manufacturer')
                                    ->label('الشركة المصنعة')
                                    ->maxLength(255),
                            ])
                            ->columnSpanFull(),

                        Select::make('pharmacy_id')
                            ->label('الصيدلية')
                            ->relationship('pharmacy', 'name')
                            ->default(fn () => auth()->user()->pharmacist?->pharmacy_id)
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('الكميات والأسعار')
                    ->schema([
                        TextInput::make('quantity')
                            ->label('الكمية المتاحة')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(0),

                        TextInput::make('reorder_level')
                            ->label('حد إعادة الطلب')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(10)
                            ->helperText('سيتم التنبيه عند وصول الكمية لهذا الحد'),

                        TextInput::make('unit_price')
                            ->label('سعر الوحدة')
                            ->numeric()
                            ->required()
                            ->prefix('ج.م')
                            ->minValue(0),

                        TextInput::make('selling_price')
                            ->label('سعر البيع')
                            ->numeric()
                            ->required()
                            ->prefix('ج.م')
                            ->minValue(0),
                    ])
                    ->columns(2),

                Section::make('معلومات إضافية')
                    ->schema([
                        TextInput::make('batch_number')
                            ->label('رقم التشغيلة')
                            ->maxLength(100),

                        DatePicker::make('expiry_date')
                            ->label('تاريخ الانتهاء')
                            ->required()
                            ->minDate(now()),

                        TextInput::make('supplier')
                            ->label('المورد')
                            ->maxLength(255),

                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('medicine.name_ar')
                    ->label('الدواء')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('medicine.strength')
                    ->label('التركيز')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('الكمية المتاحة')
                    ->badge()
                    ->color(fn ($state, $record) => $state <= $record->reorder_level ? 'danger' : 'success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reorder_level')
                    ->label('حد إعادة الطلب')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('unit_price')
                    ->label('سعر الوحدة')
                    ->money('EGP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('selling_price')
                    ->label('سعر البيع')
                    ->money('EGP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('batch_number')
                    ->label('رقم التشغيلة')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('تاريخ الانتهاء')
                    ->date('Y-m-d')
                    ->sortable()
                    ->color(fn ($state) => $state->lte(now()->addMonths(3)) ? 'danger' : null)
                    ->icon(fn ($state) => $state->lte(now()->addMonths(3)) ? 'heroicon-o-exclamation-triangle' : null),

                Tables\Columns\TextColumn::make('supplier')
                    ->label('المورد')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('low_stock')
                    ->label('أدوية قليلة')
                    ->query(fn ($query) => $query->whereColumn('quantity', '<=', 'reorder_level'))
                    ->toggle(),

                Tables\Filters\Filter::make('expiring_soon')
                    ->label('قريبة الانتهاء')
                    ->query(fn ($query) => $query->where('expiry_date', '<=', now()->addMonths(3)))
                    ->toggle(),
            ])
            ->actions([
                Actions\EditAction::make()
                    ->label('تعديل'),
                Actions\Action::make('adjust_quantity')
                    ->label('تعديل الكمية')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->color('info')
                    ->form([
                        TextInput::make('adjustment')
                            ->label('التعديل')
                            ->numeric()
                            ->required()
                            ->helperText('استخدم قيمة موجبة للزيادة أو سالبة للنقصان'),

                        Textarea::make('reason')
                            ->label('السبب')
                            ->required()
                            ->rows(2),
                    ])
                    ->action(function (array $data, PharmacyInventory $record) {
                        $newQuantity = $record->quantity + $data['adjustment'];
                        
                        if ($newQuantity < 0) {
                            \Filament\Notifications\Notification::make()
                                ->title('خطأ')
                                ->body('الكمية لا يمكن أن تكون سالبة')
                                ->danger()
                                ->send();
                            return;
                        }

                        $record->update(['quantity' => $newQuantity]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('تم تعديل الكمية بنجاح')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('medicine.name_ar', 'asc')
            ->modifyQueryUsing(fn ($query) => $query->where('pharmacy_id', auth()->user()->pharmacist?->pharmacy_id));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPharmacyInventories::route('/'),
            'create' => Pages\CreatePharmacyInventory::route('/create'),
            'edit' => Pages\EditPharmacyInventory::route('/{record}/edit'),
        ];
    }
}
