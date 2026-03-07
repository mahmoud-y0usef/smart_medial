<?php

namespace App\Filament\Clinic\Resources\ClinicPrescriptionResource\Pages;

use App\Filament\Clinic\Resources\ClinicPrescriptionResource;
use Filament\Resources\Pages\ListRecords;

class ListClinicPrescriptions extends ListRecords
{
    protected static string $resource = ClinicPrescriptionResource::class;

    public function getHeading(): string
    {
        $clinic = auth()->user()->clinic ?? auth()->user()->clinicEmployer;
        
        return $clinic ? $clinic->name . ' - الروشتات' : 'الروشتات';
    }

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
