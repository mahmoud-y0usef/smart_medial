<?php

namespace App\Filament\Pharmacy\Pages;

use App\Models\Prescription;
use App\Models\PrescriptionMedicine;
use App\Models\PharmacyInventory;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class PharmacyReports extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';

    protected string $view = 'filament.pharmacy.pages.pharmacy-reports';

    protected static ?string $navigationLabel = 'التقارير';

    protected static ?string $title = 'تقارير الصيدلية';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'إدارة الصيدلية';
    }

    public ?array $data = [];

    public $statsData = [];

    public function mount(): void
    {
        $this->form->fill([
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfDay(),
        ]);

        $this->loadStats();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('فترة التقرير')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('من تاريخ')
                            ->required()
                            ->default(now()->startOfMonth())
                            ->native(false),

                        DatePicker::make('end_date')
                            ->label('إلى تاريخ')
                            ->required()
                            ->default(now()->endOfDay())
                            ->native(false),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function loadStats(): void
    {
        $pharmacist = auth()->user()->pharmacist;
        $startDate = $this->data['start_date'] ?? now()->startOfMonth();
        $endDate = $this->data['end_date'] ?? now()->endOfDay();

        // إحصائيات الروشتات
        $prescriptionsQuery = Prescription::query()
            ->whereBetween('created_at', [$startDate, $endDate]);

        $dispensedPrescriptions = (clone $prescriptionsQuery)
            ->where('status', 'dispensed')
            ->count();

        $pendingPrescriptions = (clone $prescriptionsQuery)
            ->where('status', 'pending')
            ->count();

        // إجمالي قيمة المبيعات (تقديري)
        $totalSales = PrescriptionMedicine::query()
            ->whereHas('prescription', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'dispensed')
                  ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->sum(DB::raw('quantity * dosage'));

        // أكثر الأدوية مبيعاً
        $topMedicines = PrescriptionMedicine::query()
            ->select('medicine_id', DB::raw('COUNT(*) as prescription_count'), DB::raw('SUM(quantity) as total_quantity'))
            ->whereHas('prescription', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'dispensed')
                  ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->with('medicine')
            ->groupBy('medicine_id')
            ->orderByDesc('prescription_count')
            ->limit(10)
            ->get();

        // قيمة المخزون الحالي
        $inventoryValue = PharmacyInventory::query()
            ->where('pharmacy_id', $pharmacist?->pharmacy_id)
            ->sum(DB::raw('quantity * selling_price'));

        // عدد الأصناف
        $totalItems = PharmacyInventory::query()
            ->where('pharmacy_id', $pharmacist?->pharmacy_id)
            ->count();

        // أصناف قليلة
        $lowStockItems = PharmacyInventory::query()
            ->where('pharmacy_id', $pharmacist?->pharmacy_id)
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->count();

        // أدوية قاربت على الانتهاء
        $expiringItems = PharmacyInventory::query()
            ->where('pharmacy_id', $pharmacist?->pharmacy_id)
            ->where('expiry_date', '<=', now()->addMonths(6))
            ->where('expiry_date', '>=', now())
            ->count();

        $this->statsData = [
            'prescriptions' => [
                'dispensed' => $dispensedPrescriptions,
                'pending' => $pendingPrescriptions,
                'total' => $dispensedPrescriptions + $pendingPrescriptions,
            ],
            'sales' => [
                'total' => $totalSales,
            ],
            'inventory' => [
                'value' => $inventoryValue,
                'total_items' => $totalItems,
                'low_stock' => $lowStockItems,
                'expiring' => $expiringItems,
            ],
            'top_medicines' => $topMedicines,
        ];
    }

    public function refreshStats(): void
    {
        $this->loadStats();
        
        \Filament\Notifications\Notification::make()
            ->title('تم تحديث التقارير')
            ->success()
            ->send();
    }
}
