<?php

namespace App\Filament\Pharmacy\Pages;

use App\Filament\Pharmacy\Widgets\PharmacyStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'الرئيسية';

    protected string $view = 'filament.pharmacy.pages.dashboard';

    protected static ?string $title = 'لوحة التحكم - الصيدلية';

    public function getHeading(): string
    {
        return 'مرحباً، ' . auth()->user()->name;
    }

    public function getSubheading(): string | null
    {
        $pharmacy = auth()->user()->pharmacist?->pharmacy;
        
        return $pharmacy ? "صيدلية {$pharmacy->name}" : null;
    }

    public function getWidgets(): array
    {
        return [
            PharmacyStatsWidget::class,
        ];
    }
}
