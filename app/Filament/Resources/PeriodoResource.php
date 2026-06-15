<?php

namespace App\Filament\Resources;

use App\Models\Periodo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\PeriodoResource\Pages;

class PeriodoResource extends Resource
{
    protected static ?string $model            = Periodo::class;
    protected static ?string $navigationIcon   = null;
    protected static ?string $navigationLabel  = 'Periodos académicos';
    protected static ?string $modelLabel       = 'Periodo';
    protected static ?string $pluralModelLabel = 'Periodos';
    protected static ?string $navigationGroup  = 'Administración';
    protected static ?int    $navigationSort   = 2;

    // Solo administrador puede ver este recurso
    public static function canViewAny(): bool
    {
        return Auth::user()?->esAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        // Genera opciones de año: 2 años atrás hasta 3 años adelante
        $anioActual = (int) date('Y');
        $anios = [];
        for ($a = $anioActual - 2; $a <= $anioActual + 3; $a++) {
            $anios[$a] = $a;
        }

        return $form->schema([
            Forms\Components\Section::make('Datos del periodo')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('anio')
                        ->label('Año')
                        ->options($anios)
                        ->default($anioActual)
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn($state, Forms\Set $set, Forms\Get $get) =>
                            $set('nombre', $state . '-' . ($get('semestre') ?? 'I'))
                        ),

                    Forms\Components\Select::make('semestre')
                        ->label('Semestre')
                        ->options(['I' => 'I', 'II' => 'II'])
                        ->default('I')
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn($state, Forms\Set $set, Forms\Get $get) =>
                            $set('nombre', ($get('anio') ?? date('Y')) . '-' . $state)
                        ),

                    Forms\Components\TextInput::make('nombre')
                        ->label('Nombre del periodo')
                        ->placeholder('Ej: 2026-I')
                        ->required()
                        ->maxLength(20)
                        ->columnSpanFull()
                        ->helperText('Se genera automáticamente al elegir año y semestre, pero puedes editarlo.'),

                    Forms\Components\DatePicker::make('fecha_inicio')
                        ->label('Fecha de inicio')
                        ->required()
                        ->displayFormat('d/m/Y'),

                    Forms\Components\DatePicker::make('fecha_fin')
                        ->label('Fecha de fin')
                        ->required()
                        ->displayFormat('d/m/Y')
                        ->after('fecha_inicio'),

                    Forms\Components\Toggle::make('activo')
                        ->label('Periodo activo')
                        ->default(true)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Periodo')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_fin')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean(),

                Tables\Columns\TextColumn::make('entrevistas_count')
                    ->label('Entrevistas')
                    ->counts('entrevistas')
                    ->sortable(),
            ])
            ->defaultSort('fecha_inicio', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        // No borrar si tiene entrevistas
                        if ($record->entrevistas()->count() > 0) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('No se puede eliminar')
                                ->body('Este periodo tiene entrevistas registradas.')
                                ->send();
                            $this->halt();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPeriodos::route('/'),
            'create' => Pages\CreatePeriodo::route('/create'),
            'edit'   => Pages\EditPeriodo::route('/{record}/edit'),
        ];
    }
}
