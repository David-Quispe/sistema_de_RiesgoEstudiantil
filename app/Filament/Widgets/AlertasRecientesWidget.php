<?php

namespace App\Filament\Widgets;

use App\Models\Alerta;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class AlertasRecientesWidget extends BaseWidget
{
    protected static ?int    $sort    = 2;
    protected static ?string $heading = '🔔 Alertas recientes no leídas';

    protected int|string|array $columnSpan = 'full';
    protected static ?string   $pollingInterval = '30s';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Alerta::query()
                    ->with(['estudiante', 'usuario'])
                    ->where('usuario_id', Auth::id())
                    ->where('leida', 0)
                    ->orderByDesc('created_at')
                    ->limit(15)
            )
            ->columns([
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'RIESGO_ALTO'          => 'danger',
                        'DETERIORO_PROGRESIVO' => 'warning',
                        'DERIVACION'           => 'primary',
                        default                => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'RIESGO_ALTO'          => 'heroicon-m-exclamation-triangle',
                        'DETERIORO_PROGRESIVO' => 'heroicon-m-arrow-trending-up',
                        'DERIVACION'           => 'heroicon-m-arrow-right-circle',
                        default                => 'heroicon-m-bell',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'RIESGO_ALTO'          => 'Riesgo Alto',
                        'DETERIORO_PROGRESIVO' => 'Deterioro',
                        'DERIVACION'           => 'Derivación',
                        'SISTEMA'              => 'Sistema',
                        default                => $state,
                    }),

                Tables\Columns\TextColumn::make('estudiante.nombre_completo')
                    ->label('Estudiante')
                    ->searchable()
                    ->weight('bold')
                    ->icon('heroicon-m-user'),

                Tables\Columns\TextColumn::make('mensaje')
                    ->label('Mensaje')
                    ->limit(90)
                    ->tooltip(fn(Alerta $record): string => $record->mensaje)
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Generada')
                    ->since()
                    ->sortable()
                    ->color('gray'),
            ])
            ->recordClasses(fn(Alerta $record): string => match ($record->tipo) {
                'RIESGO_ALTO'          => 'border-l-4 border-l-red-500',
                'DETERIORO_PROGRESIVO' => 'border-l-4 border-l-amber-400',
                'DERIVACION'           => 'border-l-4 border-l-blue-500',
                default                => '',
            })
            ->actions([
                Tables\Actions\Action::make('marcar_leida')
                    ->label('Leída')
                    ->color('success')
                    ->icon('heroicon-m-check')
                    ->action(fn(Alerta $record) => $record->marcarLeida())
                    ->requiresConfirmation(false),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('marcar_todas_leidas')
                    ->label('Marcar todas como leídas')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->action(fn(\Illuminate\Support\Collection $records) =>
                        $records->each(fn(Alerta $a) => $a->marcarLeida())
                    )
                    ->deselectRecordsAfterCompletion(),
            ])
            ->emptyStateHeading('Sin alertas pendientes')
            ->emptyStateDescription('No tienes alertas sin leer. ¡Todo en orden!')
            ->emptyStateIcon('heroicon-o-bell-slash');
    }
}
