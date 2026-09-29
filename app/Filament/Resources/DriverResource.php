<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DriverResource\Pages;
use App\Models\Driver;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Gestion Chauffeurs';
    protected static ?string $modelLabel = 'Chauffeur';
    protected static ?string $pluralModelLabel = 'Gestion des Chauffeurs & Salaires';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations Personnelles & Professionnelles')
                    ->schema([
                        Forms\Components\TextInput::make('matricule')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'CHF-' . str_pad(Driver::count() + 1, 3, '0', STR_PAD_LEFT))
                            ->maxLength(255)
                            ->label('Matricule'),
                        
                        Forms\Components\TextInput::make('nom_prenom')
                            ->label('Nom & Prénom')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('poste_vehicule')
                            ->label('Poste / Fonction')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('salaire_base')
                            ->label('Salaire de base mensuel (XOF)')
                            ->numeric()
                            ->default(100000)
                            ->required(),

                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->label('Téléphone'),

                        Forms\Components\Select::make('status')
                            ->options([
                                'actif' => 'Actif',
                                'en mission' => 'En mission',
                                'repos' => 'En repos',
                            ])
                            ->default('actif')
                            ->required()
                            ->label('Statut'),
                    ])->columns(2),

                Forms\Components\Section::make('Permis de Conduire & Affectation')
                    ->schema([
                        Forms\Components\TextInput::make('license_number')
                            ->label('Numéro de Permis'),

                        Forms\Components\TextInput::make('license_category')
                            ->label('Catégorie de Permis'),

                        Forms\Components\DatePicker::make('license_expiry_date')
                            ->label('Expiration du Permis'),

                        Forms\Components\Select::make('current_vehicle_id')
                            ->relationship('currentVehicle', 'immatriculation')
                            ->searchable()
                            ->preload()
                            ->label('Véhicule Attitré'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('matricule')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('nom_prenom')
                    ->label('Nom & Prénom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('currentVehicle.immatriculation')
                    ->label('Véhicule Actuel')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone'),

                Tables\Columns\TextColumn::make('salaire_base')
                    ->label('Salaire de Base')
                    ->money('XOF')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_annuel')
                    ->label('Total Annuel Versé')
                    ->getStateUsing(fn (Driver $record) => method_exists($record, 'salaries') ? $record->salaries()->sum('montant_paye') : 0)
                    ->money('XOF'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->colors([
                        'success' => 'actif',
                        'warning' => 'en mission',
                        'danger' => 'repos',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'actif' => 'Actif',
                        'en mission' => 'En mission',
                        'repos' => 'En repos',
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filtrer par Statut')
                    ->options([
                        'actif' => 'Actif',
                        'en mission' => 'En mission',
                        'repos' => 'En repos',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            // RelationManager pour l'historique des salaires ou des missions si nécessaire
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
        ];
    }
}