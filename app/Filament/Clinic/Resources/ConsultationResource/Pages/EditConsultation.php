<?php

namespace App\Filament\Clinic\Resources\ConsultationResource\Pages;

use App\Filament\Clinic\Resources\ConsultationResource;
use Filament\Resources\Pages\EditRecord;

class EditConsultation extends EditRecord
{
    protected static string $resource = ConsultationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
