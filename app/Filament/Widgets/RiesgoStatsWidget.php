<?php

namespace App\Filament\Widgets;

use App\Models\Entrevista;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class RiesgoStatsWidget extends BaseWidget
{
    protected static ?int    $sort            = 1;
    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $user = Auth::user();

        $ultimasIds = Entrevista::selectRaw('MAX(id) as id')
            ->groupBy('estudiante_id')
            ->pluck('id');

        $base = Entrevista::whereIn('id', $ultimasIds);

        if ($user?->esConsejero()) {
            $base->where('consejero_id', $user->id);
        }

        $alto  = (clone $base)->where('nivel_riesgo', 'ALTO')->count();
        $medio = (clone $base)->where('nivel_riesgo', 'MEDIO')->count();
        $bajo  = (clone $base)->where('nivel_riesgo', 'BAJO')->count();
        $total = $alto + $medio + $bajo;

        $pctAlto  = $total > 0 ? round(($alto  / $total) * 100, 1) : 0;
        $pctMedio = $total > 0 ? round(($medio / $total) * 100, 1) : 0;
        $pctBajo  = $total > 0 ? round(($bajo  / $total) * 100, 1) : 0;

        $derivacionesPendientes = 0;
        if ($user && !$user->esConsejero()) {
            $derivacionesPendientes = \App\Models\Derivacion::where('estado', 'PENDIENTE')->count();
        }

        $stats = [
            Stat::make('Riesgo alto', $alto)
                ->description("{$pctAlto}% de {$total} evaluados")
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->chart($this->tendenciaUltimos7Dias('ALTO')),

            Stat::make('Riesgo medio', $medio)
                ->description("{$pctMedio}% de {$total} evaluados")
                ->descriptionIcon('heroicon-m-minus-circle')
                ->color('warning')
                ->chart($this->tendenciaUltimos7Dias('MEDIO')),

            Stat::make('Riesgo bajo', $bajo)
                ->description("{$pctBajo}% de {$total} evaluados")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart($this->tendenciaUltimos7Dias('BAJO')),

            Stat::make('Total evaluados', $total)
                ->description('Estudiantes con al menos 1 entrevista')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
        ];

        if ($user && !$user->esConsejero()) {
            $stats[] = Stat::make('Derivaciones pendientes', $derivacionesPendientes)
                ->description('Casos sin atender en Bienestar')
                ->descriptionIcon('heroicon-m-arrow-right-circle')
                ->color($derivacionesPendientes > 0 ? 'danger' : 'success');
        }

        return $stats;
    }

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
