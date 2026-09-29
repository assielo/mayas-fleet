<?php

namespace App\Filament\Widgets;

use App\Models\Revenue;
use App\Models\Credit;
use App\Models\Maintenance;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FleetFinancialStats extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        // Calcul des totaux globaux
        $totalRevenue = Revenue::sum('amount');
        $totalCredits = Credit::where('status', 'active')->sum('total_amount');
        $totalMaintenance = Maintenance::sum('cost');
        
        // Balance globale (Recettes - Maintenances)
        $netBalance = $totalRevenue - $totalMaintenance;

        return [
            Stat::make('Recettes Totales', number_format($totalRevenue, 0, ',', ' ') . ' FCFA')
                ->description('Chiffre d\'affaires cumulé')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Crédits en cours', number_format($totalCredits, 0, ',', ' ') . ' FCFA')
                ->description('Total des financements actifs')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('warning'),

            Stat::make('Frais de Maintenance', number_format($totalMaintenance, 0, ',', ' ') . ' FCFA')
                ->description('Total pannes et entretiens')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('danger'),

            Stat::make('Balance Nette Exploitation', number_format($netBalance, 0, ',', ' ') . ' FCFA')
                ->description('Recettes moins charges de maintenance')
                ->descriptionIcon('heroicon-m-scale')
                ->color($netBalance >= 0 ? 'success' : 'danger'),
        ];
    }
}