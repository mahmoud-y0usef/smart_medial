<?php

namespace App\Enums;

enum PrescriptionStatus: string
{
    case New = 'new';
    case Preparing = 'preparing';
    case Ready = 'ready';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
    
    public function label(): string
    {
        return match ($this) {
            self::New => 'جديد',
            self::Preparing => 'جاري التحضير',
            self::Ready => 'جاهز',
            self::Delivered => 'تم التسليم',
            self::Cancelled => 'ملغي',
        };
    }
    
    public function color(): string
    {
        return match ($this) {
            self::New => 'gray',
            self::Preparing => 'warning',
            self::Ready => 'info',
            self::Delivered => 'success',
            self::Cancelled => 'danger',
        };
    }
}
