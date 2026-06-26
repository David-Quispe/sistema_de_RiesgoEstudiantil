<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstudianteResource\Pages;
use App\Models\Carrera;
use App\Models\Estudiante;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EstudianteResource extends Resource
{
    protected static ?string $model            = Estudiante::class;
    protected static ?string $navigationIcon   = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel  = 'Estudiantes';
    protected static ?string $modelLabel       = 'Estudiante';
    protected static ?string $pluralModelLabel = 'Estudiantes';
    protected static ?int    $navigationSort   = 2;

    public static function form(Form $form): Form
    {
        // Carreras agrupadas por grupo desde la BD
        $carrerasAgrupadas = Carrera::where('activo', 1)
            ->orderBy('grupo')
            ->orderBy('nombre')
            ->get()
            ->groupBy('grupo')
            ->map(fn($grupo) => $grupo->pluck('nombre', 'nombre'))
            ->toArray();

        return $form->schema([
            Forms\Components\Section::make('Datos del Estudiante')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('codigo')
                        ->label('Código')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(20),

                    Forms\Components\Select::make('institucion_id')
                        ->label('Institución')
                        ->relationship('institucion', 'nombre')
                        ->required()
                        ->default(1),

                    Forms\Components\TextInput::make('nombre')
                        ->label('Nombres')
                        ->required()
                        ->maxLength(100),

                    Forms\Components\TextInput::make('apellidos')
                        ->label('Apellidos')
                        ->required()
                        ->maxLength(100),

                    Forms\Components\TextInput::make('email')
                        ->label('Correo electrónico')
                        ->email()
                        ->maxLength(150),

                    Forms\Components\Select::make('carrera')
                        ->label('Carrera')
                        ->options($carrerasAgrupadas)
                        ->searchable()
                        ->required()
                        ->placeholder('Selecciona una carrera'),

                    Forms\Components\Select::make('ciclo')
                        ->label('Ciclo')
                        ->options([
                            1 => 'Ciclo 1', 2 => 'Ciclo 2', 3 => 'Ciclo 3',
                            4 => 'Ciclo 4', 5 => 'Ciclo 5', 6 => 'Ciclo 6',
                        ])
                        ->required()
                        ->helperText('TECSUP tiene 6 ciclos por carrera'),

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
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nombre_completo')
                    ->label('Nombre completo')
                    ->searchable(['nombre', 'apellidos'])
                    ->sortable('apellidos'),

                Tables\Columns\TextColumn::make('carrera')
                    ->label('Carrera')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('ciclo')
                    ->label('Ciclo')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nivel_riesgo_actual')
                    ->label('Riesgo actual')
                    ->badge()
                    ->color(fn(?string $state): string => match($state) {
                        'ALTO'  => 'danger',
                        'MEDIO' => 'warning',
                        'BAJO'  => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('carrera')
                    ->label('Carrera')
                    ->options(fn() =>
                        Carrera::where('activo', 1)
                            ->orderBy('nombre')
                            ->pluck('nombre', 'nombre')
                            ->toArray()
                    ),

                Tables\Filters\SelectFilter::make('ciclo')
                    ->label('Ciclo')
                    ->options([
                        1 => 'Ciclo 1', 2 => 'Ciclo 2', 3 => 'Ciclo 3',
                        4 => 'Ciclo 4', 5 => 'Ciclo 5', 6 => 'Ciclo 6',
                    ]),

                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Estado'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->hidden(fn() => Auth::user()?->esCoordinador()),
                Tables\Actions\Action::make('fichaPdf')
                    ->label('Ficha PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->url(fn (Estudiante $record): string =>
                        static::getUrl('view', ['record' => $record])
                    )
                    ->tooltip('Abrir ficha del estudiante para generar PDF'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->hidden(fn() => Auth::user()?->esCoordinador()),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('ultimaEntrevista');
        $user  = Auth::user();

        if ($user && $user->esConsejero()) {
            $query->whereHas('entrevistas', function ($q) use ($user) {
                $q->where('consejero_id', $user->id);
            });
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();
        return $user && ($user->esConsejero() || $user->esCoordinador());
    }

    public static function canEdit($record): bool
    {
        $user = Auth::user();
        return $user && ($user->esConsejero() || $user->esCoordinador());
    }

    public static function canView($record): bool
    {
        return Auth::check();
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEstudiantes::route('/'),
            'create' => Pages\CreateEstudiante::route('/create'),
            'edit'   => Pages\EditEstudiante::route('/{record}/edit'),
            'view'   => Pages\ViewEstudiante::route('/{record}'),
        ];
    }
}
