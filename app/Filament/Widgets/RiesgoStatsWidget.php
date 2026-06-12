<?php

namespace App\Filament\Widgets;

use App\Models\Entrevista;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class RiesgoStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    // Refresca cada 60 segundos automáticamente
    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $user = Auth::user();

        // IDs de la última entrevista por estudiante
        $ultimasIds = Entrevista::selectRaw('MAX(id) as id')
            ->groupBy('estudiante_id')
            ->pluck('id');

        $base = Entrevista::whereIn('id', $ultimasIds);

        // Consejero solo ve sus propios estudiantes
        if ($user?->esConsejero()) {
            $base->where('consejero_id', $user->id);
        }

        $alto  = (clone $base)->where('nivel_riesgo', 'ALTO')->count();
        $medio = (clone $base)->where('nivel_riesgo', 'MEDIO')->count();
        $bajo  = (clone $base)->where('nivel_riesgo', 'BAJO')->count();
        $total = $alto + $medio + $bajo;

        // Derivaciones pendientes (solo coordinador/bienestar/admin)
        $derivacionesPendientes = 0;
        if ($user && ! $user->esConsejero()) {
            $derivacionesPendientes = \App\Models\Derivacion::where('estado', 'PENDIENTE')->count();
        }

        $stats = [
            Stat::make('🔴 Riesgo Alto', $alto)
                ->description('Estudiantes en riesgo alto')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->chart($this->tendenciaUltimos7Dias('ALTO')),

            Stat::make('🟡 Riesgo Medio', $medio)
                ->description('Estudiantes en riesgo medio')
                ->descriptionIcon('heroicon-m-minus-circle')
                ->color('warning')
                ->chart($this->tendenciaUltimos7Dias('MEDIO')),

            Stat::make('🟢 Riesgo Bajo', $bajo)
                ->description('Estudiantes en riesgo bajo')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart($this->tendenciaUltimos7Dias('BAJO')),

            Stat::make('👥 Total evaluados', $total)
                ->description('Estudiantes con al menos 1 entrevista')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
        ];

        // Stat extra solo para roles con visibilidad total
        if ($user && ! $user->esConsejero()) {
            $stats[] = Stat::make('📋 Derivaciones pendientes', $derivacionesPendientes)
                ->description('Casos sin atender en Bienestar')
                ->descriptionIcon('heroicon-m-arrow-right-circle')
                ->color($derivacionesPendientes > 0 ? 'danger' : 'success');
        }

        return $stats;
    }

    /**
     * Mini-gráfico de los últimos 7 días para cada nivel de riesgo.
     */
    private function tendenciaUltimos7Dias(string $nivel): array
    {
        $datos = [];
        for ($i = 6; $i >= 0; $i--) {
            $datos[] = Entrevista::where('nivel_riesgo', $nivel)
                ->whereDate('fecha_entrevista', now()->subDays($i)->toDateString())
                ->count();
        }
        return $datos;
    }
}
