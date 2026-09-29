<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MissionResource\Pages;
use App\Models\Mission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Support\Enums\MaxWidth;
class MissionResource extends Resource
{
    protected static ?string $model = Mission::class;

    protected static ?string $navigationGroup = 'Opérations';
    protected static ?string $navigationLabel = 'Missions & Trajets';
    protected static ?string $navigationIcon = 'heroicon-o-map';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informations générales & Client')
                    ->schema([
                        TextInput::make('reference')
                            ->label('Référence / Bon de transport')
                            ->required()
                            ->default('MS-' . random_int(10000, 99999))
                            ->unique(ignoreRecord: true),
                        TextInput::make('client_name')
                            ->label('Nom du Client')
                            ->required()
                            ->maxLength(255),
                        Select::make('status')
                            ->label('Statut de la mission')
                            ->options([
                                'pending' => 'En attente',
                                'in_progress' => 'En cours',
                                'delivered' => 'Livré / Terminé',
                                'cancelled' => 'Annulé',
                            ])
                            ->default('pending')
                            ->required(),
                    ])->columns(3),

                Section::make('Ressources affectées (Véhicule & Chauffeur)')
                    ->schema([
                        Select::make('vehicle_id')
                            ->label('Véhicule')
                            ->relationship('vehicle', 'plate_number')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('driver_id')
                            ->label('Chauffeur')
                            ->relationship('driver', 'last_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->last_name} {$record->first_name}")
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                Section::make('Itinéraire et Planning')
                    ->schema([
                        TextInput::make('departure_location')
                            ->label('Lieu de départ')
                            ->required(),
                        TextInput::make('arrival_location')
                            ->label('Lieu d\'arrivée (Destination)')
                            ->required(),
                        DateTimePicker::make('start_date')
                            ->label('Date et heure de départ')
                            ->required(),
                        DateTimePicker::make('end_date')
                            ->label('Date et heure d\'arrivée effective')
                            ->nullable(),
                    ])->columns(2),

                Section::make('Financier & Notes')
                    ->schema([
                        TextInput::make('amount')
                            ->label('Montant / Recette (XOF)')
                            ->numeric()
                            ->prefix('FCFA')
                            ->required(),
                        Textarea::make('notes')
                            ->label('Notes / Observations')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->label('Réf.')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('client_name')
                    ->label('Client')
                    ->searchable()
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

                TextColumn::make('departure_location')
                    ->label('Départ -> Arrivée')
                    ->formatStateUsing(fn ($record) => "{$record->departure_location} ➔ {$record->arrival_location}")
                    ->searchable(false),

                TextColumn::make('amount')
                    ->label('Montant')
                    ->money('XOF', divideBy: false)
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'in_progress' => 'warning',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'in_progress' => 'En cours',
                        'delivered' => 'Livré',
                        'cancelled' => 'Annulé',
                        default => $state,
                    }),

                TextColumn::make('start_date')
                    ->label('Date Départ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'in_progress' => 'En cours',
                        'delivered' => 'Livré',
                        'cancelled' => 'Annulé',
                    ]),
                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->label('Véhicule')
                    ->relationship('vehicle', 'plate_number'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->actions([
    ViewAction::make()
        ->slideOver()
        ->modalWidth(MaxWidth::FourExtraLarge)
        ->modalHeading('Suivi détaillé de la Mission'),
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
            'index' => Pages\ListMissions::route('/'),
            'create' => Pages\CreateMission::route('/create'),
            'edit' => Pages\EditMission::route('/{record}/edit'),
        ];
    }
}