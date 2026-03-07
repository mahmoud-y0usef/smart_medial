<?php

namespace App\Filament\Pharmacy\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-circle';

    protected string $view = 'filament.pages.edit-profile';

    protected static ?string $navigationLabel = 'الملف الشخصي';

    protected static ?string $title = 'الملف الشخصي';

    protected static ?int $navigationSort = 100;

    protected static string | \UnitEnum | null $navigationGroup = 'إدارة الصيدلية';

    public ?array $data = [];

    public function mount(): void
    {
        $this->data = auth()->user()->only(['name', 'email']);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('المعلومات الشخصية')
                    ->schema([
                        TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique('users', 'email', ignoreRecord: true),
                    ]),

                Section::make('تغيير كلمة المرور')
                    ->description('اترك الحقول فارغة إذا كنت لا تريد تغيير كلمة المرور')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('كلمة المرور الحالية')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->requiredWith('new_password'),

                        TextInput::make('new_password')
                            ->label('كلمة المرور الجديدة')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->dehydrated(false)
                            ->same('new_password_confirmation'),

                        TextInput::make('new_password_confirmation')
                            ->label('تأكيد كلمة المرور الجديدة')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->requiredWith('new_password'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $user = auth()->user();

        // Check current password if changing password
        if (! empty($data['current_password'])) {
            if (! Hash::check($data['current_password'], $user->password)) {
                Notification::make()
                    ->title('كلمة المرور الحالية غير صحيحة')
                    ->danger()
                    ->send();

                return;
            }

            $user->password = Hash::make($data['new_password']);
        }

        // Update name and email
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->save();

        Notification::make()
            ->title('تم تحديث الملف الشخصي بنجاح')
            ->success()
            ->send();

        // Refresh form data
        $this->data = $user->only(['name', 'email']);
    }
}
