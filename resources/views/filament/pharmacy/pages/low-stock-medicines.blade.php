<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                تنبيه المخزون المنخفض
            </x-slot>
            
            <x-slot name="description">
                قائمة بالأدوية التي وصلت أو تجاوزت حد إعادة الطلب. يُنصح بطلب هذه الأدوية في أقرب وقت.
            </x-slot>
            
            <div class="text-sm text-gray-600 dark:text-gray-400">
                💡 الكمية المقترحة = (حد إعادة الطلب × 3) - الكمية الحالية (بحد أدنى 50 وحدة)
            </div>
        </x-filament::section>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
