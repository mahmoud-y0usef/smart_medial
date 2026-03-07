<?php

namespace App\Filament\Clinic\Resources\QueueResource\Pages;

use App\Filament\Clinic\Resources\QueueResource;
use Filament\Resources\Pages\ListRecords;

class ListQueues extends ListRecords
{
    protected static string $resource = QueueResource::class;

    public function getHeading(): string
    {
        $clinic = auth()->user()->clinic ?? auth()->user()->clinicEmployer;
        
        return $clinic ? $clinic->name : 'قائمة الانتظار';
    }

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
