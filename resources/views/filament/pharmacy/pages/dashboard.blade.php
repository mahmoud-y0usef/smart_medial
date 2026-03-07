<x-filament-panels::page>
    <x-filament-widgets::widgets
        :widgets="$this->getWidgets()"
        :columns="[
            'md' => 2,
            'xl' => 4,
        ]"
    />
</x-filament-panels::page>
