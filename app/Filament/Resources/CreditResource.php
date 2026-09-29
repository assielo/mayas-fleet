<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CreditResource\Pages;
use App\Models\Credit;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Support\Enums\MaxWidth;

class CreditResource extends Resource
{
    protected static ?string $model = Credit::class;

    protected static ?string $navigationGroup = 'Finances';
    protected static ?string $navigationLabel = 'Suivi des Crédits';
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

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

                TextInput::make('financier_name')
                    ->label('Organisme / Banquier (Créancier)')
                    ->required()
                    ->maxLength(255),

                TextInput::make('total_amount')
                    ->label('Montant total du crédit (FCFA)')
                    ->numeric()
                    ->prefix('FCFA')
                    ->required(),

                TextInput::make('monthly_payment')
                    ->label('Mensualité (FCFA)')
                    ->numeric()
                    ->prefix('FCFA')
                    ->required(),

                TextInput::make('total_installments')
                    ->label('Nombre total d\'échéances (mois)')
                    ->numeric()
                    ->required(),

                TextInput::make('paid_installments')
                    ->label('Échéances déjà réglées')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Select::make('status')
                    ->label('Statut du Financement')
                    ->options([
                        'active' => 'En cours',
                        'completed' => 'Soldé / Remboursé',
                        'defaulted' => 'En retard / Impayé',
                    ])
                    ->default('active')
                    ->required(),

                DatePicker::make('start_date')
                    ->label('Date de début du crédit')
                    ->default(now())
                    ->required(),

                Textarea::make('notes')
                    ->label('Notes et conditions particulières')
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
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('financier_name')
                    ->label('Créancier')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('Montant Total')
                    ->money('XOF', divideBy: false)
                    ->sortable(),

                TextColumn::make('monthly_payment')
                    ->label('Mensualité')
                    ->money('XOF', divideBy: false),

                TextColumn::make('progress')
                    ->label('Progression')
                    ->formatStateUsing(fn ($record) => "{$record->paid_installments} / {$record->total_installments} mois")
                    ->badge()
                    ->color('gray'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'warning',
                        'completed' => 'success',
                        'defaulted' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'En cours',
                        'completed' => 'Soldé',
                        'defaulted' => 'Impayé',
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'active' => 'En cours',
                        'completed' => 'Soldé',
                        'defaulted' => 'Impayé',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                ViewAction::make()
                    ->slideOver()
                    ->modalWidth(MaxWidth::ThreeExtraLarge)
                    ->modalHeading('Détails & Suivi du Crédit Véhicule'),
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
            'index' => Pages\ListCredits::route('/'),
            'create' => Pages\CreateCredit::route('/create'),
            'edit' => Pages\EditCredit::route('/{record}/edit'),
        ];
    }
}