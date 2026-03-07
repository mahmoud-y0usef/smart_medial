<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Filament\Resources\UserResource\Pages;
use App\Models\Clinic;
use App\Models\Pharmacy;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'المستخدمين';

    protected static ?string $modelLabel = 'مستخدم';

    protected static ?string $pluralModelLabel = 'المستخدمين';

    protected static string | \UnitEnum | null $navigationGroup = 'إدارة المنصة';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('معلومات الحساب')
                    ->schema([
                        TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required()
                            ->disabled(fn ($record) => $record !== null)
                            ->dehydrated(fn ($record) => $record === null)
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('password')
                            ->label('كلمة المرور')
                            ->password()
                            ->revealable()
                            ->required(fn ($record) => $record === null)
                            ->minLength(8)
                            ->dehydrateStateUsing(fn ($state) => $state ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->visible(fn ($record) => $record === null),
                    ])
                    ->columns(2),

                Section::make('نوع المستخدم')
                    ->schema([
                        Select::make('user_type')
                            ->label('نوع الحساب')
                            ->options([
                                'admin' => 'مدير نظام',
                                'clinic' => 'موظف عيادة',
                                'pharmacy' => 'موظف صيدلية',
                            ])
                            ->required()
                            ->live()
                            ->visible(fn ($record) => $record === null),

                        Select::make('clinic_id')
                            ->label('العيادة')
                            ->relationship('clinicEmployer', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->visible(fn (Get $get, $record) => $record === null && $get('user_type') === 'clinic'),

                        Select::make('pharmacy_id')
                            ->label('الصيدلية')
                            ->relationship('pharmacy', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->visible(fn (Get $get, $record) => $record === null && $get('user_type') === 'pharmacy'),

                        Select::make('role')
                            ->label('الدور الوظيفي')
                            ->options(fn (Get $get) => match($get('user_type')) {
                                'admin' => [
                                    UserRole::Admin->value => 'مدير نظام',
                                ],
                                'clinic' => [
                                    UserRole::Doctor->value => 'طبيب',
                                    UserRole::Receptionist->value => 'سكرتير/استقبال',
                                ],
                                'pharmacy' => [
                                    UserRole::Pharmacy->value => 'صيدلي',
                                ],
                                default => [],
                            })
                            ->required()
                            ->visible(fn (Get $get, $record) => $record === null && filled($get('user_type')))
                            ->live(),
                    ])
                    ->columns(2)
                    ->visible(fn ($record) => $record === null),

                Section::make('معلومات الحساب الحالي')
                    ->schema([
                        Select::make('role')
                            ->label('الدور')
                            ->options(UserRole::class)
                            ->disabled()
                            ->dehydrated(false),
                        
                        TextInput::make('clinic_name')
                            ->label('العيادة')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn ($record) => $record?->clinicEmployer?->name ?? $record?->clinic?->name)
                            ->visible(fn ($record) => $record && ($record->clinicEmployer || $record->clinic)),
                        
                        TextInput::make('pharmacy_name')
                            ->label('الصيدلية')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn ($record) => $record?->pharmacy?->name)
                            ->visible(fn ($record) => $record && $record->pharmacy),
                    ])
                    ->columns(2)
                    ->visible(fn ($record) => $record !== null),
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
                    ->copyable()
                    ->copyMessage('تم نسخ البريد الإلكتروني!')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('role')
                    ->label('الدور')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('workplace')
                    ->label('المكان التابع له')
                    ->state(function (User $record): ?string {
                        if ($record->clinicEmployer) {
                            return $record->clinicEmployer->name;
                        }
                        if ($record->clinic) {
                            return $record->clinic->name;
                        }
                        if ($record->pharmacy) {
                            return $record->pharmacy->name;
                        }
                        return '-';
                    })
                    ->searchable(query: function ($query, string $search) {
                        return $query->whereHas('clinicEmployer', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })->orWhereHas('clinic', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })->orWhereHas('pharmacy', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make()
                    ->visible(fn (User $record) => $record->id !== auth()->id()),
                Actions\Action::make('resetPassword')
                    ->label('إعادة تعيين كلمة المرور')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('إعادة تعيين كلمة المرور')
                    ->modalDescription(fn (User $record) => "سيتم إنشاء كلمة مرور جديدة للمستخدم: {$record->name}")
                    ->modalSubmitActionLabel('إعادة تعيين')
                    ->visible(fn (User $record) => $record->id !== auth()->id())
                    ->action(function (User $record) {
                        $newPassword = Str::random(12);
                        $record->update([
                            'password' => Hash::make($newPassword),
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('تم إعادة تعيين كلمة المرور بنجاح')
                            ->success()
                            ->body("
                                <div class='space-y-2'>
                                    <p>كلمة المرور الجديدة:</p>
                                    <div class='flex items-center gap-2'>
                                        <code class='bg-gray-100 dark:bg-gray-800 px-3 py-2 rounded font-mono text-sm'>{$newPassword}</code>
                                        <button onclick=\"navigator.clipboard.writeText('{$newPassword}'); alert('تم نسخ كلمة المرور!')\" class='text-primary-600 hover:text-primary-700'>
                                            📋 نسخ
                                        </button>
                                    </div>
                                </div>
                            ")
                            ->persistent()
                            ->send();

                        return $newPassword;
                    })
                    ->successNotification(null),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
