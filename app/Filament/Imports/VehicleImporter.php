<?php

namespace App\Filament\Imports;

use App\Models\Vehicle;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class VehicleImporter extends Importer
{
    protected static ?string $model = Vehicle::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('immatriculation')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),
            ImportColumn::make('marque')
                ->rules(['nullable', 'string']),
            ImportColumn::make('modele')
                ->rules(['nullable', 'string']),
            ImportColumn::make('type')
                ->rules(['nullable', 'string']),
            ImportColumn::make('kilometrage')
                ->numeric()
                ->rules(['nullable', 'integer']),
            ImportColumn::make('statut')
                ->rules(['nullable', 'string']),
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'L\'importation des véhicules est terminée. ' . number_format($import->successful_rows) . ' ligne(s) importée(s).';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ligne(s) ont échoué.';
        }

        return $body;
    }
}