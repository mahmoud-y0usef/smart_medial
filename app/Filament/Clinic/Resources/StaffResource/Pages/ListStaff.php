<?php

namespace App\Filament\Clinic\Resources\StaffResource\Pages;

use App\Filament\Clinic\Resources\StaffResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStaff extends ListRecords
{
    protected static string $resource = StaffResource::class;

    public function getHeading(): string
    {
        $clinic = auth()->user()->clinic ?? auth()->user()->clinicEmployer;
        
        return $clinic ? $clinic->name . ' - الموظفين' : 'الموظفين';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('إضافة موظف'),
        ];
    }
}
