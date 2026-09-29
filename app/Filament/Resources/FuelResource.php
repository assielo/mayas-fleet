<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FuelResource\Pages;
use App\Models\Fuel;
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
use Filament\Support\Enums\MaxWidth;

class FuelResource extends Resource
{
    protected static ?string $model = Fuel::class;

    protected static ?string $navigationGroup = 'Exploitation';
    protected static ?string $navigationLabel = 'Suivi Carburant';
    protected static ?string $modelLabel = 'Plein de Carburant';
    protected static ?string $pluralModelLabel = 'Suivi des Carburants';
    protected static ?string $navigationIcon = 'heroicon-o-fire';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations du Plein')
                    ->schema([
                        Select::make('vehicle_id')
                            ->label('Véhicule concerné')
                            ->relationship('vehicle', 'plate_number')
                            ->getOptionLabelFromRecordUsing(fn (Vehicle $record) => "{$record->plate_number} - {$record->brand} {$record->model}")
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('driver_id')
                            ->label('Chauffeur')
                            ->relationship('driver', 'nom_prenom')
                            ->searchable()
                            ->preload(),

                        TextInput::make('liters')
                            ->label('Quantité (Litres)')
                            ->numeric()
                            ->suffix('L')
                            ->required(),

                        TextInput::make('total_cost')
                            ->label('Coût total du plein (FCFA)')
                            ->numeric()
                            ->prefix('FCFA')
                            ->required(),

                        TextInput::make('mileage')
                            ->label('Kilométrage actuel (km)')
                            ->numeric()
                            ->suffix('km'),

                        DatePicker::make('fuel_date')
                            ->label('Date du plein')
                            ->default(now())
                            ->required(),

                        Textarea::make('notes')
                            ->label('Notes / Station service')
                            ->columnSpanFull(),
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

                TextColumn::make('driver.nom_prenom')
                    ->label('Chauffeur')
                    ->searchable(),

                TextColumn::make('liters')
                    ->label('Volume')
                    ->suffix(' L')
                    ->sortable(),

                TextColumn::make('total_cost')
                    ->label('Coût Total')
                    ->money('XOF')
                    ->sortable(),

                TextColumn::make('mileage')
                    ->label('Kilométrage')
                    ->suffix(' km')
                    ->sortable(),

                TextColumn::make('fuel_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->relationship('vehicle', 'plate_number')
                    ->label('Filtrer par Véhicule'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()
                    ->slideOver()
                    ->modalWidth(MaxWidth::ThreeExtraLarge)
                    ->modalHeading('Détails du Plein de Carburant'),
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
            'index' => Pages\ListFuels::route('/'),
            'create' => Pages\CreateFuel::route('/create'),
            'edit' => Pages\EditFuel::route('/{record}/edit'),
        ];
    }
}