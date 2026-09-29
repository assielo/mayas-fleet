<?php

namespace App\Filament\Widgets;

use App\Models\Vehicle;
use App\Models\Mission;
use App\Models\FuelLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FleetStatsOverview extends BaseWidget
{
    // Permet d'actualiser les données automatiquement (ex: toutes les 30 secondes pour un effet "Live")
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        // 1. Calculs des métriques de la flotte
        $totalVehicles = Vehicle::count();
        $activeVehicles = Vehicle::where('status', 'active')->count(); // Ajustez selon votre colonne de statut
        $maintenanceVehicles = Vehicle::where('status', 'maintenance')->count();

        // 2. Missions en cours
        $activeMissions = Mission::where('status', 'in_progress')->count();

        // 3. Coût total du carburant du mois en cours
        $currentMonthFuelCost = FuelLog::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('total_cost');

        return [
            Stat::make('Véhicules Opérationnels', "{$activeVehicles} / {$totalVehicles}")
                ->description("{$maintenanceVehicles} en maintenance technique")
                ->descriptionIcon('heroicon-m-truck')
                ->color('success')
                ->chart([7, 12, 10, 15, 18, 22, $activeVehicles]), // Mini-graphique dynamique

            Stat::make('Missions en Cours', $activeMissions)
                ->description('Suivi des trajets en temps réel')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('warning')
                ->chart([3, 5, 8, 4, 6, 9, $activeMissions]),

            Stat::make('Carburant (Ce mois)', number_format($currentMonthFuelCost, 0, ',', ' ') . ' FCFA')
                ->description('Dépenses globales de carburant')
                ->descriptionIcon('heroicon-m-fire')
                ->color('danger'),
        ];
    }
}