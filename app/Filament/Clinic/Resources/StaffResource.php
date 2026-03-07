<?php

namespace App\Filament\Clinic\Resources;

use App\Enums\UserRole;
use App\Filament\Clinic\Resources\StaffResource\Pages;
use App\Models\User;
use Filament\Actions\BulkAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class StaffResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'الموظفين';

    protected static ?string $modelLabel = 'موظف';

    protected static ?string $pluralModelLabel = 'الموظفين';

    protected static string | \UnitEnum | null $navigationGroup = 'إدارة العيادة';

    public static function getEloquentQuery(): Builder
    {
        $clinic = auth()->user()->clinic ?? auth()->user()->clinicEmployer;
        
        return parent::getEloquentQuery()
            ->where('clinic_id', $clinic?->id)
            ->where('role', UserRole::Receptionist);
    }

    public static function canAccess(): bool
    {
        // Only clinic owners (doctors) can manage staff
        return auth()->user()->isDoctor() && auth()->user()->clinic;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('معلومات الموظف')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->label('كلمة المرور')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255)
                            ->helperText('اتركها فارغة لعدم التغيير'),

                        Forms\Components\Toggle::make('email_verified')
                            ->label('البريد الإلكتروني موثق')
                            ->default(true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('email_verified_at', now());
                                } else {
                                    $set('email_verified_at', null);
                                }
                            }),

                        Forms\Components\Hidden::make('role')
                            ->default(UserRole::Receptionist->value),

                        Forms\Components\Hidden::make('clinic_id')
                            ->default(fn () => auth()->user()->clinic?->id),

                        Forms\Components\Hidden::make('email_verified_at')
                            ->default(now()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('موثق')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()
                    ->label('تعديل'),
                DeleteAction::make()
                    ->label('حذف'),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->label('حذف المحدد'),
            ])
            ->emptyStateHeading('لا يوجد موظفين')
            ->emptyStateDescription('ابدأ بإضافة موظفين للعيادة')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('إضافة موظف')
                    ->icon('heroicon-o-plus'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            'edit' => Pages\EditStaff::route('/{record}/edit'),
        ];
    }
}
