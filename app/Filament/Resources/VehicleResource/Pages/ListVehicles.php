<?php

namespace App\Filament\Resources\VehicleResource\Pages;

use App\Filament\Resources\VehicleResource;
use App\Filament\Imports\VehicleImporter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVehicles extends ListRecords
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Bouton pour importer des véhicules (CSV / Excel)
            Actions\ImportAction::make()
                ->importer(VehicleImporter::class)
                ->label('Importer des Véhicules')
                ->color('success')
                ->icon('heroicon-o-arrow-up-tray'),

            // Bouton de création classique
            Actions\CreateAction::make(),
            
            // Bouton de téléchargement du bilan global
            \Filament\Actions\Action::make('global_fleet_report')
                ->label('Télécharger le Bilan Global de la Flotte')
                ->icon('heroicon-o-printer')
                ->color('warning')
                ->action(function () {
                    \Filament\Notifications\Notification::make()
                        ->title('Compilation du rapport global en cours...')
                        ->success()
                        ->send();
                }),
        ];
    }
}