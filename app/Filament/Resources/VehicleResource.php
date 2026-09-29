<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Filament\Tables\Actions\Action;
use Filament\Support\Enums\MaxWidth;
use Filament\Notifications\Notification;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationGroup = 'Flotte';
    protected static ?string $navigationLabel = 'Véhicules';
    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations Générales')
                    ->schema([
                        Forms\Components\TextInput::make('plate_number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->label('Immatriculation / Plaque'),
                        Forms\Components\TextInput::make('brand')
                            ->required()
                            ->label('Marque'),
                        Forms\Components\TextInput::make('model')
                            ->required()
                            ->label('Modèle'),
                        Forms\Components\Select::make('type')
                            ->options([
                                'camion' => 'Camion',
                                'liaison' => 'Véhicule de Liaison',
                                'utilitaire' => 'Utilitaire',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'disponible' => 'Disponible',
                                'en mission' => 'En mission',
                                'maintenance' => 'En maintenance',
                            ])
                            ->default('disponible')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Suivi Technique & Kilométrique')
                    ->schema([
                        Forms\Components\TextInput::make('mileage')
                            ->numeric()
                            ->default(0)
                            ->label('Kilométrage actuel (KM)'),
                        Forms\Components\DatePicker::make('insurance_expiry_date')
                            ->label('Expiration Assurance'),
                        Forms\Components\DatePicker::make('technical_visit_expiry_date')
                            ->label('Visite Technique'),
                        Forms\Components\DatePicker::make('last_oil_change_date')
                            ->label('Dernière Vidange'),
                        Forms\Components\TextInput::make('last_oil_change_mileage')
                            ->numeric()
                            ->label('Km de la dernière vidange'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plate_number')
                    ->label('Immatriculation')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('brand')
                    ->label('Marque')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('model')
                    ->label('Modèle')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->colors([
                        'primary' => 'camion',
                        'success' => 'liaison',
                        'warning' => 'utilitaire',
                    ]),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->colors([
                        'success' => 'disponible',
                        'warning' => 'en mission',
                        'danger' => 'maintenance',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'disponible' => 'Disponible',
                        'maintenance' => 'En Maintenance',
                        'en mission' => 'En Mission',
                        default => $state,
                    }),

                TextColumn::make('mileage')
                    ->label('Kilométrage')
                    ->numeric()
                    ->suffix(' km'),

                // Affichage direct du Coût par Kilomètre (CPK) grâce à l'accesseur du modèle
                TextColumn::make('cpk')
                    ->label('CPK (Coût/Km)')
                    ->money('XOF')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'disponible' => 'Disponible',
                        'maintenance' => 'En Maintenance',
                        'en mission' => 'En Mission',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'camion' => 'Camion',
                        'liaison' => 'Véhicule de Liaison',
                        'utilitaire' => 'Utilitaire',
                    ]),
            ])
            ->headerActions([
                ExportAction::make()->exports([
                    ExcelExport::make()->fromTable(),
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\ViewAction::make()
                    ->slideOver()
                    ->modalWidth(MaxWidth::ThreeExtraLarge)
                    ->modalHeading('Détails & Cockpit du Véhicule'),

                Action::make('export_report')
                    ->label('Rapport Exécutif')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function (Vehicle $record) {
                        $totalRevenue = $record->missions()->sum('amount');
                        $totalFuel = $record->fuels()->sum('total_cost');
                        $totalMaintenance = $record->maintenances()->sum('cost');
                        $netProfit = $totalRevenue - ($totalFuel + $totalMaintenance);

                        Notification::make()
                            ->title("Rapport généré pour {$record->plate_number}")
                            ->body("CA: {$totalRevenue} FCFA | Charges: " . ($totalFuel + $totalMaintenance) . " FCFA | Net: {$netProfit} FCFA")
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()->exports([
                        ExcelExport::make()->fromTable(),
                    ]),
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
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}