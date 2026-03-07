<?php

namespace App\Filament\Pharmacy\Resources\PharmacyInventoryResource\Pages;

use App\Filament\Pharmacy\Resources\PharmacyInventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPharmacyInventories extends ListRecords
{
    protected static string $resource = PharmacyInventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('إضافة دواء جديد'),
        ];
    }
}
