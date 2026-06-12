<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsuarioResource\Pages;
use App\Models\Usuario;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UsuarioResource extends Resource
{
    protected static ?string $model = Usuario::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $modelLabel = 'Usuario';
    protected static ?string $pluralModelLabel = 'Usuarios';
    protected static ?int $navigationSort = 10;

    // Solo administradores pueden ver este Resource
    public static function canAccess(): bool
    {
        return Auth::user()?->esAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Datos del usuario')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('institucion_id')
                        ->label('Institución')
                        ->relationship('institucion', 'nombre')
                        ->required()
                        ->default(1),

                    Forms\Components\Select::make('rol')
                        ->label('Rol')
                        ->options([
                            'consejero'   => 'Consejero / Tutor',
                            'coordinador' => 'Coordinador',
                            'bienestar'   => 'Bienestar Estudiantil',
                            'admin'       => 'Administrador',
                        ])
                        ->required(),

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
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(150),

                    Forms\Components\TextInput::make('password')
                        ->label('Contraseña')
                        ->password()
                        ->required(fn($operation) => $operation === 'create')
                        ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                        ->dehydrated(fn($state) => filled($state))
                        ->maxLength(255),

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
                Tables\Columns\TextColumn::make('nombre_completo')
                    ->label('Nombre completo')
                    ->searchable(['nombre', 'apellidos'])
                    ->sortable('apellidos'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('rol')
                    ->label('Rol')
                    ->badge()
                    ->color(fn(string $state): string => match($state) {
                        'consejero'   => 'primary',
                        'coordinador' => 'warning',
                        'bienestar'   => 'success',
                        'admin'       => 'danger',
                        default       => 'gray',
                    }),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean(),

                Tables\Columns\TextColumn::make('ultimo_acceso')
                    ->label('Último acceso')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rol')
                    ->options([
                        'consejero'   => 'Consejero',
                        'coordinador' => 'Coordinador',
                        'bienestar'   => 'Bienestar',
                        'admin'       => 'Admin',
                    ]),
                Tables\Filters\TernaryFilter::make('activo')->label('Estado'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsuarios::route('/'),
            'create' => Pages\CreateUsuario::route('/create'),
            'edit'   => Pages\EditUsuario::route('/{record}/edit'),
        ];
    }
}
