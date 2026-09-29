<?php

namespace App\Filament\Widgets;

use App\Models\FuelLog;
use App\Models\Maintenance;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class FleetExpensesChart extends ChartWidget
{
    protected static ?string $heading = 'Évolution des Dépenses (Carburant & Maintenance)';
    
    protected static ?int $sort = 2; // Position sur le dashboard

    protected function getData(): array
    {
        // Générer les 6 derniers mois
        $months = collect(range(5, 0))->map(function ($sub) {
            return Carbon::now()->subMonths($sub);
        });

        // Libellés des mois (ex: "Janvier 2026", "Février 2026")
        $labels = $months->map(fn ($date) => ucfirst($date->translatedFormat('F Y')))->toArray();

        // Données du carburant par mois
        $fuelData = $months->map(function ($date) {
            return FuelLog::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('cost');
        })->toArray();

        // Données de la maintenance par mois
        $maintenanceData = $months->map(function ($date) {
            return Maintenance::whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('cost');
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Carburant (XOF)',
                    'data' => $fuelData,
                    'borderColor' => '#f59e0b', // Couleur ambre/jaune
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                ],
                [
                    'label' => 'Maintenance (XOF)',
                    'data' => $maintenanceData,
                    'borderColor' => '#ef4444', // Couleur rouge
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Type de graphique : 'line' (courbes) ou 'bar' (histogramme)
    }
}