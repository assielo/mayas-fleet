<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FuelLogResource\Pages;
use App\Models\FuelLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;

class FuelLogResource extends Resource
{
    protected static ?string $model = FuelLog::class;

    protected static ?string $navigationGroup = 'Opérations';
    protected static ?string $navigationLabel = 'Suivi Carburant';
    protected static ?string $navigationIcon = 'heroicon-o-fire';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Détails du Plein')
                    ->schema([
                        Select::make('vehicle_id')
                            ->label('Véhicule')
                            ->relationship('vehicle', 'plate_number')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('driver_id')
                            ->label('Chauffeur (Optionnel)')
                            ->relationship('driver', 'last_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->last_name} {$record->first_name}")
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        DatePicker::make('date')
                            ->label('Date du plein')
                            ->default(now())
                            ->required(),
                        TextInput::make('liters')
                            ->label('Quantité (Litres)')
                            ->numeric()
                            ->required(),
                        TextInput::make('total_cost')
                            ->label('Coût Global (XOF)')
                            ->numeric()
                            ->prefix('FCFA')
                            ->required(),
                        TextInput::make('mileage')
                            ->label('Kilométrage actuel')
                            ->numeric()
                            ->nullable(),
                        Textarea::make('notes')
                            ->label('Notes / Stations-service')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('vehicle.plate_number')
                    ->label('Véhicule')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                TextColumn::make('driver.last_name')
                    ->label('Chauffeur')
                    ->formatStateUsing(fn ($record) => $record->driver ? "{$record->driver->last_name} {$record->driver->first_name}" : '-')
                    ->searchable(),
                TextColumn::make('liters')
                    ->label('Litres')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' L')
                    ->sortable(),
                TextColumn::make('total_cost')
                    ->label('Coût Total')
                    ->money('XOF', divideBy: false)
                    ->sortable(),
                TextColumn::make('mileage')
                    ->label('Kilométrage')
                    ->numeric()
                    ->suffix(' km')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->label('Véhicule')
                    ->relationship('vehicle', 'plate_number'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFuelLogs::route('/'),
            'create' => Pages\CreateFuelLog::route('/create'),
            'edit' => Pages\EditFuelLog::route('/{record}/edit'),
        ];
    }
}