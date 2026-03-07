<?php

namespace App\Filament\Widgets;

use App\Models\Clinic;
use App\Models\Pharmacy;
use App\Models\Appointment;
use App\Models\Prescription;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('العيادات', Clinic::count())
                ->description('إجمالي العيادات المسجلة')
                ->descriptionIcon('heroicon-o-building-office-2')
                ->color('primary')
                ->chart([7, 10, 12, 15, 18, 20, Clinic::count()]),
            
            Stat::make('الصيدليات', Pharmacy::count())
                ->description('إجمالي الصيدليات المسجلة')
                ->descriptionIcon('heroicon-o-building-storefront')
                ->color('success')
                ->chart([3, 5, 7, 9, 11, 12, Pharmacy::count()]),
            
            Stat::make('المواعيد', Appointment::count())
                ->description('إجمالي المواعيد في النظام')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('info')
                ->chart([10, 20, 30, 40, 50, 60, Appointment::count()]),
            
            Stat::make('الروشتات', Prescription::count())
                ->description('إجمالي الروشتات المُصدرة')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('warning')
                ->chart([5, 15, 25, 35, 45, 55, Prescription::count()]),
        ];
    }
}
