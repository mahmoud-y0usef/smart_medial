<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case Pending = 'pending';
    case Scheduled = 'scheduled';
    case Confirmed = 'confirmed';
    case Waiting = 'waiting';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case NoShow = 'no_show';
    
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'قيد الانتظار',
            self::Scheduled => 'محجوز',
            self::Confirmed => 'مؤكد',
            self::Waiting => 'في الطابور',
            self::InProgress => 'جاري الآن',
            self::Completed => 'مكتمل',
            self::Cancelled => 'ملغي',
            self::NoShow => 'لم يحضر',
        };
    }
    
    public function color(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Scheduled, self::Confirmed => 'info',
            self::Waiting => 'warning',
            self::InProgress => 'primary',
            self::Completed => 'success',
            self::Cancelled, self::NoShow => 'danger',
        };
    }
}
