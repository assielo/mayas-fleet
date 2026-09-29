<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssignmentResource\Pages;
use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;

class AssignmentResource extends Resource
{
    protected static ?string $model = Assignment::class;

    protected static ?string $navigationGroup = 'Opérations';
    protected static ?string $navigationLabel = 'Affectations';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('vehicle_id')
                    ->label('Véhicule')
                    ->relationship('vehicle', 'plate_number')
                    ->getOptionLabelFromRecordUsing(fn (Vehicle $record) => "{$record->plate_number} - {$record->brand} {$record->model}")
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('driver_id')
                    ->label('Chauffeur')
                    ->relationship('driver', 'last_name')
                    ->getOptionLabelFromRecordUsing(fn (Driver $record) => "{$record->first_name} {$record->last_name} ({$record->phone})")
                    ->searchable()
                    ->preload()
                    ->required(),

                DateTimePicker::make('assigned_at')
                    ->label('Date et heure de début')
                    ->default(now())
                    ->required(),

                DateTimePicker::make('returned_at')
                    ->label('Date et heure de fin (Retour)')
                    ->nullable(),

                Select::make('status')
                    ->label('Statut de l\'affectation')
                    ->options([
                        'active' => 'En cours',
                        'completed' => 'Terminée',
                        'cancelled' => 'Annulée',
                    ])
                    ->default('active')
                    ->required(),

                Textarea::make('notes')
                    ->label('Notes / Observations')
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
                    ->formatStateUsing(fn ($record) => "{$record->driver->first_name} {$record->driver->last_name}")
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assigned_at')
                    ->label('Début')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('returned_at')
                    ->label('Fin')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'gray',
                        'cancelled' => 'danger',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'En cours',
                        'completed' => 'Terminée',
                        'cancelled' => 'Annulée',
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'active' => 'En cours',
                        'completed' => 'Terminée',
                        'cancelled' => 'Annulée',
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
            'index' => Pages\ListAssignments::route('/'),
            'create' => Pages\CreateAssignment::route('/create'),
            'edit' => Pages\EditAssignment::route('/{record}/edit'),
        ];
    }
}