<?php

namespace App\Filament\Clinic\Resources\ConsultationResource\Pages;

use App\Filament\Clinic\Resources\ConsultationResource;
use Filament\Resources\Pages\ListRecords;

class ListConsultations extends ListRecords
{
    protected static string $resource = ConsultationResource::class;

    public function getHeading(): string
    {
        $clinic = auth()->user()->clinic ?? auth()->user()->clinicEmployer;
        
        return $clinic ? $clinic->name : 'الكشوفات';
    }

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
