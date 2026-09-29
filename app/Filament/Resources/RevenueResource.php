<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RevenueResource\Pages;
use App\Models\Revenue;
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

class RevenueResource extends Resource
{
    protected static ?string $model = Revenue::class;

    protected static ?string $navigationGroup = 'Finances';
    protected static ?string $navigationLabel = 'Recettes';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('vehicle_id')
                    ->label('Véhicule concerné')
                    ->relationship('vehicle', 'plate_number')
                    ->getOptionLabelFromRecordUsing(fn (Vehicle $record) => "{$record->plate_number} - {$record->brand} {$record->model}")
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('driver_id')
                    ->label('Chauffeur (Optionnel)')
                    ->relationship('driver', 'last_name')
                    ->getOptionLabelFromRecordUsing(fn (Driver $record) => "{$record->first_name} {$record->last_name}")
                    ->searchable()
                    ->preload(),

                TextInput::make('amount')
                    ->label('Montant de la recette (FCFA)')
                    ->numeric()
                    ->prefix('FCFA')
                    ->required(),

                DatePicker::make('revenue_date')
                    ->label('Date de la recette')
                    ->default(now())
                    ->required(),

                Select::make('source')
                    ->label('Source de la recette')
                    ->options([
                        'vtc' => 'Course VTC',
                        'fret' => 'Fret / Transport de marchandises',
                        'location' => 'Location de véhicule',
                        'autre' => 'Autre',
                    ])
                    ->required(),

                Textarea::make('description')
                    ->label('Détails / Commentaires')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vehicle.plate_number')
                    ->label('Véhicule')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('driver.last_name')
                    ->label('Chauffeur')
                    ->formatStateUsing(fn ($record) => $record->driver ? "{$record->driver->first_name} {$record->driver->last_name}" : 'N/A')
                    ->searchable(),
                TextColumn::make('source')
                    ->label('Source')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'vtc' => 'VTC',
                        'fret' => 'Fret',
                        'location' => 'Location',
                        default => 'Autre',
                    }),
                TextColumn::make('amount')
                    ->label('Montant')
                    ->money('XOF', divideBy: false)
                    ->sortable(),
                TextColumn::make('revenue_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('source')
                    ->label('Source')
                    ->options([
                        'vtc' => 'VTC',
                        'fret' => 'Fret',
                        'location' => 'Location',
                        'autre' => 'Autre',
                    ]),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRevenues::route('/'),
            'create' => Pages\CreateRevenue::route('/create'),
            'edit' => Pages\EditRevenue::route('/{record}/edit'),
        ];
    }
}