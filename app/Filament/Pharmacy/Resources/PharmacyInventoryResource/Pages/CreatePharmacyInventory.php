<?php

namespace App\Filament\Pharmacy\Resources\PharmacyInventoryResource\Pages;

use App\Filament\Pharmacy\Resources\PharmacyInventoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePharmacyInventory extends CreateRecord
{
    protected static string $resource = PharmacyInventoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
