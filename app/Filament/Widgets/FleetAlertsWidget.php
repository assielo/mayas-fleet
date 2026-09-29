<?php

namespace App\Filament\Widgets;

use App\Models\Vehicle;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class FleetAlertsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Véhicules dont l'assurance expire dans moins de 30 jours ou est dépassée
        $expiringInsurance = Vehicle::where('insurance_expiry_date', '<=', Carbon::now()->addDays(30))->count();
        
        // Véhicules dont la visite technique expire bientôt
        $expiringInspection = Vehicle::where('technical_inspection_expiry_date', '<=', Carbon::now()->addDays(30))->count();

        return [
            Stat::make('Assurances à renouveler', $expiringInsurance)
                ->description('Échéance < 30 jours ou expirée')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($expiringInsurance > 0 ? 'danger' : 'success'),

            Stat::make('Visites Techniques proches', $expiringInspection)
                ->description('Contrôle requis prochainement')
                ->descriptionIcon('heroicon-m-shield-exclamation')
                ->color($expiringInspection > 0 ? 'warning' : 'success'),
        ];
    }
}