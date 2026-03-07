<x-filament-panels::page>
    <form wire:submit="refreshStats">
        {{ $this->form }}
        
        <div class="mt-4">
            <x-filament::button type="submit" icon="heroicon-o-arrow-path">
                تحديث التقارير
            </x-filament::button>
        </div>
    </form>

    <div class="mt-8 space-y-6">
        {{-- Prescriptions Stats --}}
        <x-filament::section>
            <x-slot name="heading">
                📋 إحصائيات الروشتات
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <div class="text-sm text-blue-600 dark:text-blue-400">إجمالي الروشتات</div>
                    <div class="text-3xl font-bold text-blue-700 dark:text-blue-300 mt-2">
                        {{ $statsData['prescriptions']['total'] ?? 0 }}
                    </div>
                </div>

                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <div class="text-sm text-green-600 dark:text-green-400">تم صرفها</div>
                    <div class="text-3xl font-bold text-green-700 dark:text-green-300 mt-2">
                        {{ $statsData['prescriptions']['dispensed'] ?? 0 }}
                    </div>
                </div>

                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                    <div class="text-sm text-yellow-600 dark:text-yellow-400">قيد الانتظار</div>
                    <div class="text-3xl font-bold text-yellow-700 dark:text-yellow-300 mt-2">
                        {{ $statsData['prescriptions']['pending'] ?? 0 }}
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- Inventory Stats --}}
        <x-filament::section>
            <x-slot name="heading">
                📦 إحصائيات المخزون
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                    <div class="text-sm text-purple-600 dark:text-purple-400">قيمة المخزون</div>
                    <div class="text-2xl font-bold text-purple-700 dark:text-purple-300 mt-2">
                        {{ number_format($statsData['inventory']['value'] ?? 0, 2) }} ج.م
                    </div>
                </div>

                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4">
                    <div class="text-sm text-indigo-600 dark:text-indigo-400">عدد الأصناف</div>
                    <div class="text-2xl font-bold text-indigo-700 dark:text-indigo-300 mt-2">
                        {{ $statsData['inventory']['total_items'] ?? 0 }}
                    </div>
                </div>

                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                    <div class="text-sm text-orange-600 dark:text-orange-400">أصناف قليلة</div>
                    <div class="text-2xl font-bold text-orange-700 dark:text-orange-300 mt-2">
                        {{ $statsData['inventory']['low_stock'] ?? 0 }}
                    </div>
                </div>

                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                    <div class="text-sm text-red-600 dark:text-red-400">قاربت على الانتهاء</div>
                    <div class="text-2xl font-bold text-red-700 dark:text-red-300 mt-2">
                        {{ $statsData['inventory']['expiring'] ?? 0 }}
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- Top Medicines --}}
        <x-filament::section>
            <x-slot name="heading">
                🏆 أكثر الأدوية مبيعاً
            </x-slot>

            @if(count($statsData['top_medicines'] ?? []) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-right">#</th>
                                <th class="px-4 py-3 text-right">اسم الدواء</th>
                                <th class="px-4 py-3 text-right">عدد الروشتات</th>
                                <th class="px-4 py-3 text-right">الكمية الإجمالية</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($statsData['top_medicines'] as $index => $medicine)
                                <tr>
                                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $medicine->medicine->name_ar ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ $medicine->prescription_count }}</td>
                                    <td class="px-4 py-3">{{ $medicine->total_quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    لا توجد بيانات للفترة المحددة
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
