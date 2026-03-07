<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" icon="heroicon-o-check-circle">
                حفظ التغييرات
            </x-filament::button>
        </div>
    </form>

    <div class="mt-8 space-y-4">
        <x-filament::section>
            <x-slot name="heading">
                ℹ️ معلومات إضافية
            </x-slot>

            <div class="prose dark:prose-invert max-w-none text-sm">
                <ul class="space-y-2">
                    <li><strong>رقم الترخيص:</strong> يجب أن يكون ساري المفعول وفقاً للقوانين المحلية</li>
                    <li><strong>أوقات العمل:</strong> سيتم عرضها للمرضى عند البحث عن الصيدلية</li>
                    <li><strong>قبول التأمين:</strong> سيساعد المرضى على معرفة إمكانية استخدام تأمينهم الصحي</li>
                    <li><strong>خدمة التوصيل:</strong> في حالة تفعيلها، قد تحتاج إلى تحديد مناطق التغطية ورسوم التوصيل</li>
                </ul>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                🔔 التنبيهات التلقائية
            </x-slot>

            <div class="space-y-3 text-sm">
                <div class="flex items-start gap-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="text-blue-600 dark:text-blue-400">✓</div>
                    <div>
                        <div class="font-medium text-blue-900 dark:text-blue-100">تنبيه المخزون المنخفض</div>
                        <div class="text-blue-700 dark:text-blue-300 text-xs mt-1">سيتم تنبيهك تلقائياً عند وصول أي دواء لحد إعادة الطلب</div>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <div class="text-yellow-600 dark:text-yellow-400">✓</div>
                    <div>
                        <div class="font-medium text-yellow-900 dark:text-yellow-100">تنبيه تواريخ الانتهاء</div>
                        <div class="text-yellow-700 dark:text-yellow-300 text-xs mt-1">سيتم تنبيهك عند اقتراب انتهاء صلاحية أي دواء (6 أشهر)</div>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <div class="text-green-600 dark:text-green-400">✓</div>
                    <div>
                        <div class="font-medium text-green-900 dark:text-green-100">تنبيه الروشتات الجديدة</div>
                        <div class="text-green-700 dark:text-green-300 text-xs mt-1">سيتم تنبيهك فور وصول روشتة جديدة تحتاج للصرف</div>
                    </div>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
