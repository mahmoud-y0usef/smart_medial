<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button
                type="submit"
                wire:loading.attr="disabled"
            >
                <x-filament::loading-indicator wire:loading wire:target="save" class="ml-2 h-4 w-4" />
                حفظ التغييرات
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
