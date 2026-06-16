<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConfiguracionRiesgoResource\Pages;
use App\Models\ConfiguracionRiesgo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

/**
 * Recurso exclusivo del ADMIN.
 * Permite ajustar el PESO de cada indicador y los UMBRALES de riesgo.
 * El nombre del indicador es de solo lectura — no se puede cambiar.
 */
class ConfiguracionRiesgoResource extends Resource
{
    protected static ?string $model           = ConfiguracionRiesgo::class;
    protected static ?string $navigationIcon  = null;
    protected static ?string $navigationLabel = 'Configuración de Riesgo';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $modelLabel      = 'Indicador de riesgo';
    protected static ?string $pluralModelLabel = 'Indicadores de riesgo';
    protected static ?int    $navigationSort  = 10;

    /* Solo visible para el rol admin */
    public static function canViewAny(): bool
    {
        return Auth::user()?->esAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return false; // Los 6 indicadores son fijos — no se crean desde el panel
    }

    public static function canDelete($record): bool
    {
        return false; // Tampoco se eliminan
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Indicador')
                ->columns(2)
                ->schema([

                    /* Nombre — SOLO LECTURA, el admin no lo modifica */
                    Forms\Components\TextInput::make('indicador')
                        ->label('Nombre del indicador')
                        ->disabled()
                        ->dehydrated(false), // no se envía al guardar

                    /* Peso — editable por el admin */
                    Forms\Components\TextInput::make('peso')
                        ->label('Peso (debe sumar 1.00 entre todos los indicadores)')
                        ->numeric()
                        ->minValue(0.01)
                        ->maxValue(1.00)
                        ->step(0.01)
                        ->required()
                        ->helperText('Ejemplo: 0.25 = 25 % de influencia en el puntaje total'),

                    /* Umbral ALTO — editable */
                    Forms\Components\TextInput::make('umbral_alto')
                        ->label('Umbral ALTO (puntaje ponderado menor a este valor → ALTO)')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(10)
                        ->step(0.1)
                        ->required()
                        ->helperText('Recomendado: 3.0'),

                    /* Umbral MEDIO — editable */
                    Forms\Components\TextInput::make('umbral_medio')
                        ->label('Umbral MEDIO (puntaje ponderado menor a este valor → MEDIO)')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(10)
                        ->step(0.1)
                        ->required()
                        ->helperText('Recomendado: 5.0'),

                    /* Activo */
                    Forms\Components\Toggle::make('activo')
                        ->label('Activo')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('indicador')
                    ->label('Indicador')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('peso')
                    ->label('Peso')
                    ->formatStateUsing(fn($state) => number_format($state * 100, 0) . ' %')
                    ->sortable(),

                Tables\Columns\TextColumn::make('umbral_alto')
                    ->label('Umbral ALTO')
                    ->numeric(1),

                Tables\Columns\TextColumn::make('umbral_medio')
                    ->label('Umbral MEDIO')
                    ->numeric(1),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar peso'),
            ])
            ->paginated(false) // Los 6 indicadores caben en una sola página
            ->defaultSort('id');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConfiguracionRiesgo::route('/'),
            'edit'  => Pages\EditConfiguracionRiesgo::route('/{record}/edit'),
        ];
    }
}
