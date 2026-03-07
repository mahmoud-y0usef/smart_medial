<?php

namespace App\Filament\Resources;

use App\Enums\ApprovalStatus;
use App\Filament\Resources\PharmacyResource\Pages;
use App\Models\Pharmacy;
use BackedEnum;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use UnitEnum;

class PharmacyResource extends Resource
{
    protected static ?string $model = Pharmacy::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'الصيدليات';

    protected static ?string $modelLabel = 'صيدلية';

    protected static ?string $pluralModelLabel = 'الصيدليات';

    protected static string | UnitEnum | null $navigationGroup = 'إدارة المنصة';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('معلومات الصيدلية')
                    ->schema([
                        TextInput::make('name')
                            ->label('اسم الصيدلية')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('name_en')
                            ->label('الاسم بالإنجليزية')
                            ->maxLength(255),
                        TextInput::make('license_number')
                            ->label('رقم الترخيص')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        FileUpload::make('license_file')
                            ->label('ملف الترخيص')
                            ->directory('pharmacy-licenses')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(5120),
                    ])->columns(2),

                Section::make('معلومات الاتصال')
                    ->schema([
                        TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('city')
                            ->label('المدينة')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('address')
                            ->label('العنوان')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('الموقع الجغرافي')
                    ->schema([
                        TextInput::make('latitude')
                            ->label('خط العرض')
                            ->numeric()
                            ->step(0.0000001),
                        TextInput::make('longitude')
                            ->label('خط الطول')
                            ->numeric()
                            ->step(0.0000001),
                    ])->columns(2),

                Section::make('الخدمات')
                    ->schema([
                        Toggle::make('has_delivery')
                            ->label('خدمة التوصيل'),
                        Toggle::make('is_24_hours')
                            ->label('يعمل 24 ساعة'),
                        Toggle::make('is_active')
                            ->label('نشط'),
                    ])->columns(3),

                Section::make('حالة الموافقة')
                    ->schema([
                        Select::make('approval_status')
                            ->label('حالة الموافقة')
                            ->options(ApprovalStatus::class)
                            ->required()
                            ->default(ApprovalStatus::Pending),
                        Textarea::make('rejection_reason')
                            ->label('سبب الرفض')
                            ->visible(fn (Get $get): bool => $get('approval_status') === ApprovalStatus::Rejected->value)
                            ->rows(3)
                            ->columnSpanFull(),
                        DateTimePicker::make('approved_at')
                            ->label('تاريخ الموافقة')
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('اسم الصيدلية')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('city')
                    ->label('المدينة')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable(),
                TextColumn::make('approval_status')
                    ->label('الحالة')
                    ->badge()
                    ->sortable(),
                IconColumn::make('is_24_hours')
                    ->label('24 ساعة')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('approval_status')
                    ->label('حالة الموافقة')
                    ->options(ApprovalStatus::class),
                SelectFilter::make('is_active')
                    ->label('الحالة')
                    ->options([
                        '1' => 'نشط',
                        '0' => 'غير نشط',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('approve')
                    ->label('موافقة')
                    ->icon(Heroicon::CheckCircle)
                    ->color('success')
                    ->visible(fn (Pharmacy $record): bool => $record->approval_status === ApprovalStatus::Pending)
                    ->requiresConfirmation()
                    ->action(fn (Pharmacy $record) => $record->approve()),
                Action::make('reject')
                    ->label('رفض')
                    ->icon(Heroicon::XCircle)
                    ->color('danger')
                    ->visible(fn (Pharmacy $record): bool => $record->approval_status === ApprovalStatus::Pending)
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('سبب الرفض')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(fn (Pharmacy $record, array $data) => $record->reject($data['rejection_reason'])),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
                BulkAction::make('approve_bulk')
                        ->label('موافقة على المحدد')
                        ->icon(Heroicon::CheckCircle)
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->approve()),

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
            'index' => Pages\ListPharmacies::route('/'),
            'create' => Pages\CreatePharmacy::route('/create'),
            'view' => Pages\ViewPharmacy::route('/{record}'),
            'edit' => Pages\EditPharmacy::route('/{record}/edit'),
        ];
    }
}
