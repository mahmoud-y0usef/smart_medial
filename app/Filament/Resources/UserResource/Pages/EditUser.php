<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected static ?string $title = 'تعديل المستخدم';

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('عرض'),
            Actions\DeleteAction::make()
                ->label('حذف')
                ->visible(fn () => $this->record->id !== auth()->id()),
        ];
    }

    public function mount(int | string $record): void
    {
        parent::mount($record);
        
        // منع الأدمن من تعديل حسابه الخاص
        if ($this->record->id === auth()->id()) {
            abort(403, 'لا يمكنك تعديل حسابك الخاص من هنا. استخدم صفحة الملف الشخصي.');
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
