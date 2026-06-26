<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DerivacionResource\Pages;
use App\Models\Derivacion;
use App\Models\Entrevista;
use App\Models\Usuario;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DerivacionResource extends Resource
{
    protected static ?string $model           = Derivacion::class;
    protected static ?string $navigationIcon  = 'heroicon-o-arrow-path-rounded-square';
    protected static ?string $navigationLabel = 'Derivaciones';
    protected static ?string $modelLabel      = 'Derivación';
    protected static ?string $pluralModelLabel = 'Derivaciones';
    protected static ?int    $navigationSort  = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Datos de la derivación')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('entrevista_id')
                        ->label('Entrevista')
                        ->options(fn() =>
                            Entrevista::with('estudiante')
                                ->get()
                                ->mapWithKeys(fn($e) => [
                                    $e->id => "{$e->estudiante->codigo} — {$e->estudiante->nombre_completo} ({$e->fecha_entrevista->format('d/m/Y')})"
                                ])
                        )
                        ->searchable()
                        ->required(),

                    Forms\Components\Select::make('consejero_id')
                        ->label('Consejero que deriva')
                        ->options(fn() =>
                            Usuario::whereIn('rol', ['consejero', 'coordinador'])
                                ->where('activo', 1)
                                ->get()
                                ->mapWithKeys(fn($u) => [$u->id => $u->nombre_completo])
                        )
                        ->default(fn() => Auth::id())
                        ->required(),

                    Forms\Components\Select::make('prioridad')
                        ->label('Prioridad')
                        ->options([
                            'BAJA'    => 'Baja',
                            'NORMAL'  => 'Normal',
                            'ALTA'    => 'Alta',
                            'URGENTE' => '🚨 Urgente',
                        ])
                        ->default('NORMAL')
                        ->required(),

                    Forms\Components\Select::make('estado')
                        ->label('Estado')
                        ->options([
                            'PENDIENTE'   => 'Pendiente',
                            'EN_ATENCION' => 'En atención',
                            'RESUELTA'    => 'Resuelta',
                            'CERRADA'     => 'Cerrada',
                        ])
                        ->default('PENDIENTE')
                        ->live()
                        ->required(),

                    Forms\Components\Textarea::make('motivo')
                        ->label('Motivo de la derivación')
                        ->required()
                        ->columnSpanFull()
                        ->rows(3),
                ]),

            Forms\Components\Section::make('Atención y resolución')
                ->columns(2)
                ->collapsible()
                ->collapsed(fn (Forms\Get $get) => $get('estado') !== 'CERRADA')
                ->schema([
                    Forms\Components\Select::make('bienestar_id')
                        ->label('Atendido por (Bienestar)')
                        ->options(fn() =>
                            Usuario::where('rol', 'bienestar')
                                ->where('activo', 1)
                                ->get()
                                ->mapWithKeys(fn($u) => [$u->id => $u->nombre_completo])
                        )
                        ->nullable(),

                    Forms\Components\DatePicker::make('fecha_cierre')
                        ->label('Fecha de cierre'),

                    Forms\Components\Textarea::make('resolucion')
                        ->label('Resolución')
                        ->columnSpanFull()
                        ->rows(3)
                        ->requiredIf('estado', 'CERRADA')
                        ->helperText('Obligatorio para cerrar la derivación: debe documentarse cómo se resolvió el caso.')
                        ->validationMessages([
                            'required_if' => 'Debes registrar la resolución antes de cerrar la derivación.',
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('entrevista.estudiante.nombre_completo')
                    ->label('Estudiante')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('entrevista.estudiante.carrera')
                    ->label('Carrera'),

                Tables\Columns\TextColumn::make('prioridad')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn(string $state): string => match($state) {
                        'URGENTE' => 'danger',
                        'ALTA'    => 'warning',
                        'NORMAL'  => 'primary',
                        default   => 'gray',
                    }),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match($state) {
                        'PENDIENTE'   => 'warning',
                        'EN_ATENCION' => 'primary',
                        'RESUELTA'    => 'success',
                        default       => 'gray',
                    }),

                Tables\Columns\TextColumn::make('consejero.nombre_completo')
                    ->label('Consejero'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha derivación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'PENDIENTE'   => 'Pendiente',
                        'EN_ATENCION' => 'En atención',
                        'RESUELTA'    => 'Resuelta',
                        'CERRADA'     => 'Cerrada',
                    ]),

                Tables\Filters\SelectFilter::make('prioridad')
                    ->options([
                        'BAJA'    => 'Baja',
                        'NORMAL'  => 'Normal',
                        'ALTA'    => 'Alta',
                        'URGENTE' => 'Urgente',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->hidden(fn() => Auth::user()?->esCoordinador()),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['entrevista.estudiante', 'consejero', 'atendiendoPor']);

        $user = Auth::user();

        // Consejero solo ve sus propias derivaciones
        if ($user?->esConsejero()) {
            $query->where('consejero_id', $user->id);
        }

        // Bienestar solo ve las pendientes y en atención
        if ($user?->esBienestar()) {
            $query->whereIn('estado', ['PENDIENTE', 'EN_ATENCION']);
        }

        return $query;
    }

    // Solo consejero puede crear derivaciones — coordinador NO
    public static function canCreate(): bool
    {
        $user = Auth::user();
        return $user && $user->esConsejero();
    }

    // Coordinador solo puede ver — no editar
    public static function canEdit($record): bool
    {
        $user = Auth::user();
        if ($user?->esAdmin()) return true;
        if ($user?->esBienestar()) return true;
        return false;
    }

    public static function canView($record): bool
    {
        return Auth::check();
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDerivaciones::route('/'),
            'create' => Pages\CreateDerivacion::route('/create'),
            'edit'   => Pages\EditDerivacion::route('/{record}/edit'),
            'view'   => Pages\ViewDerivacion::route('/{record}'),
        ];
    }
}
