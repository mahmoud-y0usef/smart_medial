<?php

namespace App\Filament\Pharmacy\Resources\PharmacyInventoryResource\Pages;

use App\Filament\Pharmacy\Resources\PharmacyInventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPharmacyInventory extends EditRecord
{
    protected static string $resource = PharmacyInventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('حذف'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
