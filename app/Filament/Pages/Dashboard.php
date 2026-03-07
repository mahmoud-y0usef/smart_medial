<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\PlatformStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?int $navigationSort = -2;

    protected static ?string $title = 'لوحة التحكم';

    protected static ?string $navigationLabel = 'الرئيسية';

    protected ?string $heading = 'مرحباً بك في منصة الشفاء الذكي';

    protected ?string $subheading = 'نظام إدارة طبي متكامل';

    public function getWidgets(): array
    {
        return [
            PlatformStatsWidget::class,
        ];
    }
}
