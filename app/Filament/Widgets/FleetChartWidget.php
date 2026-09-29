<?php

namespace App\Filament\Widgets;

use App\Models\Revenue;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class FleetChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Évolution des Recettes Mensuelles';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // Récupération des recettes groupées par mois sur les derniers mois
        $revenues = Revenue::select(
                DB::raw('SUM(amount) as total'),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month")
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->pluck('total', 'month');

        return [
            'datasets' => [
                [
                    'label' => 'Recettes Mensuelles (FCFA)',
                    'data' => $revenues->values()->toArray(),
                    'borderColor' => '#d4af37', // Accent doré
                    'backgroundColor' => 'rgba(212, 175, 55, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $revenues->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}