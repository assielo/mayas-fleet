<?php

namespace App\Filament\Widgets;

use App\Models\Vehicle;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;

class VehicleAlertsTable extends BaseWidget
{
    protected static ?string $heading = '🚨 Alertes : Assurances et Visites Techniques (30 prochains jours)';
    
    // Position du widget sur le tableau de bord
    protected static ?int $sort = 2;
    
    // Occuper toute la largeur de la page
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $thresholdDate = now()->addDays(30);

        return $table
            ->query(
                Vehicle::query()
                    ->where(function (Builder $query) use ($thresholdDate) {
                        $query->where('insurance_expiry_date', '<=', $thresholdDate)
                              ->orWhere('technical_visit_expiry_date', '<=', $thresholdDate);
                    })
            )
            ->columns([
                TextColumn::make('plate_number')
                    ->label('Immatriculation')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('brand')
                    ->label('Marque / Modèle')
                    ->formatStateUsing(fn ($record) => "{$record->brand} {$record->model}")
                    ->searchable(),

                TextColumn::make('insurance_expiry_date')
                    ->label('Expiration Assurance')
                    ->date('d/m/Y')
                    ->badge()
                    ->color(fn ($state) => 
                        !$state ? 'gray' : (now()->greaterThan($state) ? 'danger' : (now()->addDays(7)->greaterThan($state) ? 'warning' : 'success'))
                    )
                    ->sortable(),

                TextColumn::make('technical_visit_expiry_date')
                    ->label('Expiration Visite Technique')
                    ->date('d/m/Y')
                    ->badge()
                    ->color(fn ($state) => 
                        !$state ? 'gray' : (now()->greaterThan($state) ? 'danger' : (now()->addDays(7)->greaterThan($state) ? 'warning' : 'success'))
                    )
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_vehicle')
                    ->label('Gérer')
                    ->url(fn (Vehicle $record): string => \App\Filament\Resources\VehicleResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}