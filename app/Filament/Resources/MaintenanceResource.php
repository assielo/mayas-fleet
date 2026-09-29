<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenanceResource\Pages;
use App\Models\Maintenance;
use App\Models\Vehicle;
use App\Models\Driver;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Support\Enums\MaxWidth;

class MaintenanceResource extends Resource
{
    protected static ?string $model = Maintenance::class;

    protected static ?string $navigationGroup = 'Exploitation';
    protected static ?string $navigationLabel = 'Maintenances & Pannes';
    protected static ?string $modelLabel = 'Intervention Maintenance';
    protected static ?string $pluralModelLabel = 'Suivi des Maintenances';
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Détails de l\'intervention')
                    ->schema([
                        Select::make('vehicle_id')
                            ->label('Véhicule concerné')
                            ->relationship('vehicle', 'plate_number')
                            ->getOptionLabelFromRecordUsing(fn (Vehicle $record) => "{$record->plate_number} - {$record->brand} {$record->model}")
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('driver_id')
                            ->label('Chauffeur (Ayant signalé / conduit)')
                            ->relationship('driver', 'last_name')
                            ->getOptionLabelFromRecordUsing(fn (Driver $record) => "{$record->first_name} {$record->last_name}")
                            ->searchable()
                            ->preload(),

                        Select::make('type')
                            ->label('Type d\'intervention')
                            ->options([
                                'reparation' => 'Réparation / Panne',
                                'vidange' => 'Vidange & Entretien courant',
                                'visite_technique' => 'Visite Technique',
                                'assurance' => 'Renouvellement Assurance',
                                'pneus' => 'Changement de pneus',
                                'autre' => 'Autre',
                            ])
                            ->required(),

                        TextInput::make('cost')
                            ->label('Coût total (FCFA)')
                            ->numeric()
                            ->prefix('FCFA')
                            ->required(),

                        TextInput::make('mileage')
                            ->label('Kilométrage au compteur (km)')
                            ->numeric()
                            ->suffix('km'),

                        DatePicker::make('date')
                            ->label('Date de l\'intervention')
                            ->default(now())
                            ->required(),

                        TextInput::make('provider')
                            ->label('Garage / Prestataire')
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Description des travaux / Pièces changées')
                            ->columnSpanFull()
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vehicle.plate_number')
                    ->label('Véhicule')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'reparation' => 'danger',
                        'vidange' => 'warning',
                        'visite_technique', 'assurance' => 'success',
                        'pneus' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'reparation' => 'Réparation',
                        'vidange' => 'Vidange',
                        'visite_technique' => 'Visite Technique',
                        'assurance' => 'Assurance',
                        'pneus' => 'Pneus',
                        default => 'Autre',
                    })
                    ->searchable(),

                TextColumn::make('cost')
                    ->label('Coût')
                    ->money('XOF', divideBy: false)
                    ->sortable(),

                TextColumn::make('mileage')
                    ->label('Kilométrage')
                    ->suffix(' km')
                    ->sortable(),

                TextColumn::make('provider')
                    ->label('Garage / Prestataire')
                    ->searchable(),

                TextColumn::make('date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Travaux / Pièces')
                    ->limit(30)
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type d\'intervention')
                    ->options([
                        'reparation' => 'Réparation',
                        'vidange' => 'Vidange',
                        'visite_technique' => 'Visite Technique',
                        'assurance' => 'Assurance',
                        'pneus' => 'Pneus',
                        'autre' => 'Autre',
                    ]),
                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->relationship('vehicle', 'plate_number')
                    ->label('Filtrer par Véhicule'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                ViewAction::make()
                    ->slideOver()
                    ->modalWidth(MaxWidth::ThreeExtraLarge)
                    ->modalHeading('Détails de l\'Intervention & Maintenance'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaintenances::route('/'),
            'create' => Pages\CreateMaintenance::route('/create'),
            'edit' => Pages\EditMaintenance::route('/{record}/edit'),
        ];
    }
}