<?php

namespace App\Filament\Pharmacy\Pages;

use App\Models\Pharmacy;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PharmacySettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pharmacy.pages.pharmacy-settings';

    protected static ?string $navigationLabel = 'الإعدادات';

    protected static ?string $title = 'إعدادات الصيدلية';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return 'إدارة الصيدلية';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $pharmacist = auth()->user()->pharmacist;
        $pharmacy = $pharmacist?->pharmacy;

        if ($pharmacy) {
            $this->form->fill([
                'name_ar' => $pharmacy->name_ar,
                'name_en' => $pharmacy->name_en,
                'phone' => $pharmacy->phone,
                'email' => $pharmacy->email,
                'address' => $pharmacy->address,
                'license_number' => $pharmacy->license_number,
                'opening_time' => $pharmacy->opening_time,
                'closing_time' => $pharmacy->closing_time,
                'is_24_hours' => $pharmacy->is_24_hours ?? false,
                'accepts_insurance' => $pharmacy->accepts_insurance ?? true,
                'delivery_available' => $pharmacy->delivery_available ?? false,
            ]);
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('معلومات الصيدلية الأساسية')
                    ->schema([
                        TextInput::make('name_ar')
                            ->label('اسم الصيدلية (عربي)')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('name_en')
                            ->label('اسم الصيدلية (English)')
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->required()
                            ->maxLength(20),

                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->maxLength(255),

                        Textarea::make('address')
                            ->label('العنوان')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('license_number')
                            ->label('رقم الترخيص')
                            ->required()
                            ->maxLength(100),
                    ])
                    ->columns(2),

                Section::make('أوقات العمل')
                    ->schema([
                        Toggle::make('is_24_hours')
                            ->label('صيدلية تعمل 24 ساعة')
                            ->live()
                            ->columnSpanFull(),

                        TimePicker::make('opening_time')
                            ->label('وقت الفتح')
                            ->required(fn ($get) => !$get('is_24_hours'))
                            ->visible(fn ($get) => !$get('is_24_hours'))
                            ->native(false)
                            ->seconds(false),

                        TimePicker::make('closing_time')
                            ->label('وقت الإغلاق')
                            ->required(fn ($get) => !$get('is_24_hours'))
                            ->visible(fn ($get) => !$get('is_24_hours'))
                            ->native(false)
                            ->seconds(false),
                    ])
                    ->columns(2),

                Section::make('الخدمات المتاحة')
                    ->schema([
                        Toggle::make('accepts_insurance')
                            ->label('قبول التأمين الصحي')
                            ->default(true)
                            ->inline(false),

                        Toggle::make('delivery_available')
                            ->label('خدمة التوصيل متاحة')
                            ->default(false)
                            ->inline(false),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $pharmacist = auth()->user()->pharmacist;
        $pharmacy = $pharmacist?->pharmacy;

        if (!$pharmacy) {
            Notification::make()
                ->title('خطأ')
                ->body('لم يتم العثور على بيانات الصيدلية')
                ->danger()
                ->send();
            return;
        }

        // إذا كانت الصيدلية تعمل 24 ساعة، قم بتعيين القيم الافتراضية
        if ($data['is_24_hours']) {
            $data['opening_time'] = '00:00:00';
            $data['closing_time'] = '23:59:59';
        }

        $pharmacy->update($data);

        Notification::make()
            ->title('تم الحفظ بنجاح')
            ->body('تم تحديث إعدادات الصيدلية')
            ->success()
            ->send();
    }
}
