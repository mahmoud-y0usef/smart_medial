<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Doctor = 'doctor';
    case Receptionist = 'receptionist';
    case Pharmacy = 'pharmacy';
    case Patient = 'patient';
    
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'مدير النظام',
            self::Doctor => 'طبيب',
            self::Receptionist => 'سكرتير/ة',
            self::Pharmacy => 'صيدلية',
            self::Patient => 'مريض',
        };
    }
}
