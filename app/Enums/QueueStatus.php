<?php

namespace App\Enums;

enum QueueStatus: string
{
    case Waiting = 'waiting';
    case Called = 'called';
    case InConsultation = 'in_consultation';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Skipped = 'skipped';
    
    public function label(): string
    {
        return match ($this) {
            self::Waiting => 'في الانتظار',
            self::Called => 'تم الاستدعاء',
            self::InConsultation => 'جاري الكشف',
            self::Completed => 'منتهي',
            self::Cancelled => 'ملغي',
            self::Skipped => 'متخطي',
        };
    }
}
