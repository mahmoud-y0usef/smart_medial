<?php

namespace App\Enums\Triage;

enum SeverityLevel: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Emergency = 'emergency';
    
    public function label(): string
    {
        return match ($this) {
            self::Low => 'عادي',
            self::Medium => 'متوسط',
            self::High => 'مرتفع',
            self::Emergency => 'طوارئ',
        };
    }
    
    public function color(): string
    {
        return match ($this) {
            self::Low => 'success',
            self::Medium => 'warning',
            self::High => 'danger',
            self::Emergency => 'danger',
        };
    }
    
    public function priority(): int
    {
        return match ($this) {
            self::Low => 1,
            self::Medium => 5,
            self::High => 8,
            self::Emergency => 10,
        };
    }
}
