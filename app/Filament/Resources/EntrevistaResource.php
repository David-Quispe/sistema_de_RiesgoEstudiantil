<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntrevistaResource\Pages;
use App\Models\Entrevista;
use App\Models\Estudiante;
use App\Models\Usuario;
use App\Models\Periodo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EntrevistaResource extends Resource
{
    protected static ?string $model           = Entrevista::class;
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Entrevistas';
    protected static ?string $modelLabel      = 'Entrevista';
    protected static ?string $pluralModelLabel = 'Entrevistas';
    protected static ?int    $navigationSort  = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Datos de la entrevista')
                ->columns(2)
                ->schema([
                    // ── fn() => lazy: se ejecuta solo al abrir el form, no al registrar el panel ──
                    Forms\Components\Select::make('estudiante_id')
                        ->label('Estudiante')
                        ->options(fn() =>
                            Estudiante::where('activo', 1)
                                ->get()
                                ->mapWithKeys(fn($e) => [$e->id => "{$e->codigo} — {$e->nombre_completo}"])
                        )
                        ->searchable()
                        ->required(),

                    Forms\Components\Select::make('consejero_id')
                        ->label('Consejero')
                        ->options(fn() =>
                            Usuario::whereIn('rol', ['consejero', 'coordinador'])
                                ->where('activo', 1)
                                ->get()
                                ->mapWithKeys(fn($u) => [$u->id => $u->nombre_completo])
                        )
                        ->default(fn() => Auth::id())
                        ->required(),

                    Forms\Components\Select::make('periodo_id')
                        ->label('Periodo')
                        ->relationship('periodo', 'nombre')
                        ->required(),

                    Forms\Components\DatePicker::make('fecha_entrevista')
                        ->label('Fecha de entrevista')
                        ->required()
                        ->default(now()),

                    Forms\Components\Textarea::make('observaciones')
                        ->label('Observaciones generales')
                        ->columnSpanFull()
                        ->rows(3),
                ]),

            Forms\Components\Section::make('Indicadores de riesgo (puntaje del 0 al 10)')
                ->description('Evalúe cada indicador. El sistema calculará el nivel de riesgo automáticamente.')
                ->schema([
                    Forms\Components\Repeater::make('indicadores')
                        ->relationship('indicadores')
                        ->label('')
                        ->columns(3)
                        ->defaultItems(6)
                        ->schema([
                            Forms\Components\TextInput::make('nombre')
                                ->label('Indicador')
                                ->required()
                                ->maxLength(80),

                            Forms\Components\TextInput::make('puntaje')
                                ->label('Puntaje (0-10)')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(10)
                                ->step(0.5)
                                ->required(),

                            Forms\Components\TextInput::make('peso')
                                ->label('Peso (0-1)')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(1)
                                ->step(0.05)
                                ->required(),

                            Forms\Components\TextInput::make('observacion')
                                ->label('Observación')
                                ->columnSpanFull()
                                ->maxLength(500),
                        ]),
                ]),

            Forms\Components\Section::make('Resultado del cálculo de riesgo')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('puntaje_total')
                        ->label('Puntaje ponderado')
                        ->numeric()
                        ->disabled()
                        ->dehydrated(),

                    Forms\Components\Select::make('nivel_riesgo')
                        ->label('Nivel de riesgo')
                        ->options([
                            'BAJO'  => '🟢 BAJO',
                            'MEDIO' => '🟡 MEDIO',
                            'ALTO'  => '🔴 ALTO',
                        ])
                        ->disabled()
                        ->dehydrated()
                        ->default('BAJO'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('estudiante.codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('estudiante.nombre_completo')
                    ->label('Estudiante')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('estudiante.carrera')
                    ->label('Carrera')
                    ->searchable(),

                Tables\Columns\TextColumn::make('consejero.nombre_completo')
                    ->label('Consejero')
                    ->searchable(),

                Tables\Columns\TextColumn::make('periodo.nombre')
                    ->label('Periodo')
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_entrevista')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('puntaje_total')
                    ->label('Puntaje')
                    ->numeric(2)
                    ->sortable(),

                // BadgeColumn → TextColumn + ->badge()
                Tables\Columns\TextColumn::make('nivel_riesgo')
                    ->label('Riesgo')
                    ->badge()
                    ->color(fn(string $state): string => match($state) {
                        'ALTO'  => 'danger',
                        'MEDIO' => 'warning',
                        default => 'success',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('nivel_riesgo')
                    ->label('Nivel de riesgo')
                    ->options([
                        'BAJO'  => 'Bajo',
                        'MEDIO' => 'Medio',
                        'ALTO'  => 'Alto',
                    ]),

                Tables\Filters\SelectFilter::make('periodo_id')
                    ->label('Periodo')
                    ->relationship('periodo', 'nombre'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['estudiante', 'consejero', 'periodo']);
        $user  = Auth::user();

        if ($user?->esConsejero()) {
            $query->where('consejero_id', $user->id);
        }

        return $query;
    }

    // Solo consejero y coordinador pueden crear entrevistas
    public static function canCreate(): bool
    {
        $user = Auth::user();
        return $user && ($user->esConsejero() || $user->esCoordinador());
    }

    // Bienestar y Admin solo pueden ver, no editar
    public static function canEdit($record): bool
    {
        $user = Auth::user();
        return $user && ($user->esConsejero() || $user->esCoordinador() || $user->esAdmin());
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEntrevistas::route('/'),
            'create' => Pages\CreateEntrevista::route('/create'),
            'edit'   => Pages\EditEntrevista::route('/{record}/edit'),
            'view'   => Pages\ViewEntrevista::route('/{record}'),
        ];
    }
}
