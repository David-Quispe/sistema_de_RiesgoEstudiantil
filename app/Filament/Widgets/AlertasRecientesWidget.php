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

    // Ocupa todo el ancho
    protected int|string|array $columnSpan = 'full';

    // Refresca cada 30 segundos para mostrar alertas nuevas sin recargar
    protected static ?string $pollingInterval = '30s';

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
                // Tipo con badge de color — API correcta Filament 3
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'RIESGO_ALTO'          => 'danger',
                        'DETERIORO_PROGRESIVO' => 'warning',
                        'DERIVACION'           => 'primary',
                        default                => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'RIESGO_ALTO'          => '⚠️ Riesgo Alto',
                        'DETERIORO_PROGRESIVO' => '📈 Deterioro',
                        'DERIVACION'           => '📋 Derivación',
                        'SISTEMA'              => '🔧 Sistema',
                        default                => $state,
                    }),

                Tables\Columns\TextColumn::make('estudiante.nombre_completo')
                    ->label('Estudiante')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('mensaje')
                    ->label('Mensaje')
                    ->limit(90)
                    ->tooltip(fn(Alerta $record): string => $record->mensaje),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Generada')
                    ->since()           // Muestra "hace 5 min", "hace 2 horas", etc.
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('marcar_leida')
                    ->label('✓ Leída')
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
