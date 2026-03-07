<?php

namespace App\Filament\Pharmacy\Widgets;

use App\Models\PharmacyInventory;
use App\Models\Prescription;
use App\Models\PrescriptionMedicine;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PharmacyStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $pharmacist = auth()->user()->pharmacist;
        
        if (!$pharmacist || !$pharmacist->pharmacy) {
            return [];
        }

        $pharmacy = $pharmacist->pharmacy;

        $totalInventory = PharmacyInventory::where('pharmacy_id', $pharmacy->id)->count();
        
        $lowStock = PharmacyInventory::where('pharmacy_id', $pharmacy->id)
            ->whereRaw('quantity <= reorder_level')
            ->count();

        $todayPrescriptions = Prescription::whereDate('created_at', today())->count();

        $totalMedicines = PharmacyInventory::where('pharmacy_id', $pharmacy->id)
            ->sum('quantity');

        return [
            Stat::make('المخزون', $totalInventory)
                ->description('أصناف الأدوية المتوفرة')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('success')
                ->chart([30, 35, 32, 38, 34, 40, 36]),

            Stat::make('الأدوية القليلة', $lowStock)
                ->description('تحتاج إعادة طلب')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->chart([2, 3, 2, 4, 3, 5, 4]),

            Stat::make('الروشتات اليوم', $todayPrescriptions)
                ->description('روشتات اليوم')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info')
                ->chart([5, 7, 6, 8, 7, 9, 8]),

            Stat::make('إجمالي الكميات', $totalMedicines)
                ->description('إجمالي الأدوية بالمخزن')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary')
                ->chart([200, 220, 210, 240, 230, 260, 250]),
        ];
    }
}
