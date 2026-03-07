<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected static ?string $title = 'إضافة مستخدم';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // إزالة الحقول المؤقتة
        unset($data['user_type']);
        
        // التأكد من وجود كلمة المرور
        if (empty($data['password'])) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make('password123');
        }
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
