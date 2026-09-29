<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\FleetStatsOverview;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function getWidgets(): array
{
    return [
        FleetStatsOverview::class,
    ];
}
}