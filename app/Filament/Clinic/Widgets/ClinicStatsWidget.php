<?php

namespace App\Filament\Clinic\Widgets;

use App\Enums\QueueStatus;
use App\Models\Consultation;
use App\Models\Prescription;
use App\Models\QueueEntry;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ClinicStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $doctorId = auth()->user()->doctor?->id;

        if (!$doctorId) {
            return [];
        }

        // Queue waiting count
        $waitingCount = QueueEntry::whereHas('appointment', function ($q) use ($doctorId) {
            $q->whereHas('doctor', function ($dq) use ($doctorId) {
                $dq->where('id', $doctorId);
            });
        })
        ->where('status', QueueStatus::Waiting)
        ->count();

        // Consultations today
        $consultationsToday = Consultation::where('doctor_id', $doctorId)
            ->whereDate('created_at', today())
            ->count();

        // Prescriptions today
        $prescriptionsToday = Prescription::where('doctor_id', $doctorId)
            ->whereDate('created_at', today())
            ->count();

        // High priority cases waiting
        $emergencyCases = QueueEntry::whereHas('appointment', function ($q) use ($doctorId) {
            $q->whereHas('doctor', function ($dq) use ($doctorId) {
                $dq->where('id', $doctorId);
            })->where('priority', '>=', 8);
        })
        ->where('status', '!=', QueueStatus::Completed)
        ->count();

        return [
            Stat::make('قائمة الانتظار', $waitingCount)
                ->description('مريض في الانتظار')
                ->descriptionIcon('heroicon-m-queue-list')
                ->color('primary')
                ->chart([7, 5, 10, 8, 12, 15, $waitingCount]),

            Stat::make('الكشوفات اليوم', $consultationsToday)
                ->description('كشف طبي مكتمل')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('success')
                ->chart([3, 5, 7, 10, 12, 15, $consultationsToday]),

            Stat::make('الروشتات اليوم', $prescriptionsToday)
                ->description('روشتة صادرة')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info')
                ->chart([2, 4, 6, 8, 10, 13, $prescriptionsToday]),

            Stat::make('حالات طارئة', $emergencyCases)
                ->description('أولوية عالية (≥8)')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->chart([1, 0, 2, 1, 3, 2, $emergencyCases]),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
