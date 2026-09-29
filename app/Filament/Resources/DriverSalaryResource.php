<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DriverSalaryResource\Pages;
use App\Models\DriverSalary;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DriverSalaryResource extends Resource
{
    protected static ?string $model = DriverSalary::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes'; // Icône de paie/monnaie

    protected static ?string $navigationLabel = 'Salaires & Règlements';

    protected static ?string $modelLabel = 'Salaire Chauffeur';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('driver_id')
                    ->relationship('driver', 'nom_prenom') // Assurez-vous que le champ nom correspond dans votre modèle Driver
                    ->required()
                    ->label('Chauffeur'),
                
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('FCFA')
                    ->label('Montant du Salaire'),

                Forms\Components\Select::make('month')
                    ->options([
                        'Janvier' => 'Janvier',
                        'Février' => 'Février',
                        'Mars' => 'Mars',
                        'Avril' => 'Avril',
                        'Mai' => 'Mai',
                        'Juin' => 'Juin',
                        'Juillet' => 'Juillet',
                        'Août' => 'Août',
                        'Septembre' => 'Septembre',
                        'Octobre' => 'Octobre',
                        'Novembre' => 'Novembre',
                        'Décembre' => 'Décembre',
                    ])
                    ->required()
                    ->label('Mois concerné'),

                Forms\Components\TextInput::make('year')
                    ->required()
                    ->numeric()
                    ->default(date('Y'))
                    ->label('Année'),

                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'En attente',
                        'paid' => 'Payé',
                    ])
                    ->default('pending')
                    ->required()
                    ->label('Statut du règlement'),

                Forms\Components\DatePicker::make('payment_date')
                    ->label('Date de paiement'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('driver.nom_prenom')
                    ->searchable()
                    ->sortable()
                    ->label('Chauffeur'),

                Tables\Columns\TextColumn::make('amount')
                    ->money('XOF') // Format monétaire adapté
                    ->sortable()
                    ->label('Montant'),

                Tables\Columns\TextColumn::make('month')
                    ->sortable()
                    ->label('Mois'),

                Tables\Columns\TextColumn::make('year')
                    ->sortable()
                    ->label('Année'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'pending',
                        'success' => 'paid',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'paid' => 'Payé',
                        default => $state,
                    })
                    ->label('Statut'),

                Tables\Columns\TextColumn::make('payment_date')
                    ->date()
                    ->label('Date de versement'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListDriverSalaries::route('/'),
            'create' => Pages\CreateDriverSalary::route('/create'),
            'edit' => Pages\EditDriverSalary::route('/{record}/eidt'),
        ];
    }
}