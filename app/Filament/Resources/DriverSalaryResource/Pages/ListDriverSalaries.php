<?php

namespace App\Filament\Resources\DriverSalaryResource\Pages;

use App\Filament\Resources\DriverSalaryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDriverSalaries extends ListRecords
{
    protected static string $resource = DriverSalaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
