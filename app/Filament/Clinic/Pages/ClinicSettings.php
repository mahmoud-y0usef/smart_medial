<?php

namespace App\Filament\Clinic\Pages;

use App\Enums\ApprovalStatus;
use App\Models\Clinic;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;

class ClinicSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.clinic.pages.clinic-settings';

    protected static ?string $navigationLabel = 'إعدادات العيادة';

    protected static ?string $title = 'إعدادات العيادة';

    protected static string | \UnitEnum | null $navigationGroup = 'إدارة العيادة';

    public ?array $data = [];

    public function mount(): void
    {
        $clinic = auth()->user()->clinic;
        
        if (!$clinic) {
            abort(403, 'ليس لديك عيادة مسجلة');
        }

        $this->data = $clinic->toArray();
    }

    public static function canAccess(): bool
    {
        // Only clinic owners can access settings
        return auth()->user()->isDoctor() && auth()->user()->clinic;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('معلومات العيادة الأساسية')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم العيادة (بالعربي)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name_en')
                            ->label('اسم العيادة (بالإنجليزي)')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('license_number')
                            ->label('رقم الترخيص')
                            ->required()
                            ->maxLength(255)
                            ->disabled(),

                        Forms\Components\TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('address')
                            ->label('العنوان التفصيلي')
                            ->required()
                            ->columnSpanFull()
                            ->rows(2),

                        Forms\Components\TextInput::make('city')
                            ->label('المدينة')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TagsInput::make('specializations')
                            ->label('التخصصات')
                            ->placeholder('اضف تخصص')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('الموقع الجغرافي')
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->label('خط العرض')
                            ->numeric()
                            ->step(0.0000001),

                        Forms\Components\TextInput::make('longitude')
                            ->label('خط الطول')
                            ->numeric()
                            ->step(0.0000001),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('ساعات العمل')
                    ->schema([
                        Forms\Components\Repeater::make('working_hours')
                            ->label('')
                            ->schema([
                                Forms\Components\Select::make('day')
                                    ->label('اليوم')
                                    ->options([
                                        'saturday' => 'السبت',
                                        'sunday' => 'الأحد',
                                        'monday' => 'الاثنين',
                                        'tuesday' => 'الثلاثاء',
                                        'wednesday' => 'الأربعاء',
                                        'thursday' => 'الخميس',
                                        'friday' => 'الجمعة',
                                    ])
                                    ->required(),

                                Forms\Components\TimePicker::make('from')
                                    ->label('من')
                                    ->required(),

                                Forms\Components\TimePicker::make('to')
                                    ->label('إلى')
                                    ->required(),
                            ])
                            ->columns(3)
                            ->columnSpanFull()
                            ->defaultItems(0),
                    ])
                    ->collapsible(),

                Section::make('إعدادات إضافية')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('وصف العيادة')
                            ->columnSpanFull()
                            ->rows(3),

                        Forms\Components\Toggle::make('accepts_emergency')
                            ->label('تقبل الحالات الطارئة')
                            ->inline(false),

                        Forms\Components\Toggle::make('is_active')
                            ->label('العيادة نشطة')
                            ->inline(false)
                            ->helperText('عند إيقاف النشاط، لن يستطيع المرضى حجز مواعيد'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            $clinic = auth()->user()->clinic;
            $clinic->update($data);

            Notification::make()
                ->success()
                ->title('تم حفظ التغييرات بنجاح')
                ->send();
                
            // Refresh the data array with updated values
            $this->data = $clinic->fresh()->toArray();
        } catch (Halt $exception) {
            return;
        }
    }
}
