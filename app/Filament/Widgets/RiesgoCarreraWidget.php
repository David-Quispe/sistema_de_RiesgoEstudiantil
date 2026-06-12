<?php

namespace App\Filament\Widgets;

use App\Models\Entrevista;
use App\Models\Estudiante;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

/**
 * RiesgoCarreraWidget
 *
 * Gráfico de barras apiladas que muestra la distribución de niveles
 * de riesgo (ALTO / MEDIO / BAJO) por cada carrera disponible.
 * Solo visible para coordinador, bienestar y admin.
 */
class RiesgoCarreraWidget extends ChartWidget
{
    protected static ?int    $sort    = 4;
    protected static ?string $heading = '🏫 Distribución de riesgo por carrera';

    protected int|string|array $columnSpan = 'full';
    protected static ?string   $maxHeight  = '300px';   // ← ?string, no ?int

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && ! $user->esConsejero();
    }

    protected function getData(): array
    {
        $carreras = Estudiante::where('activo', 1)
            ->distinct()
            ->orderBy('carrera')
            ->pluck('carrera')
            ->toArray();

        $ultimasIds = Entrevista::selectRaw('MAX(id) as id')
            ->groupBy('estudiante_id')
            ->pluck('id');

        $alto  = [];
        $medio = [];
        $bajo  = [];

        foreach ($carreras as $carrera) {
            $base = Entrevista::whereIn('id', $ultimasIds)
                ->whereHas('estudiante', fn($q) => $q->where('carrera', $carrera));

            $alto[]  = (clone $base)->where('nivel_riesgo', 'ALTO')->count();
            $medio[] = (clone $base)->where('nivel_riesgo', 'MEDIO')->count();
            $bajo[]  = (clone $base)->where('nivel_riesgo', 'BAJO')->count();
        }

        return [
            'datasets' => [
                [
                    'label'           => '🔴 Alto',
                    'data'            => $alto,
                    'backgroundColor' => '#ef4444',
                    'borderRadius'    => 4,
                ],
                [
                    'label'           => '🟡 Medio',
                    'data'            => $medio,
                    'backgroundColor' => '#f59e0b',
                    'borderRadius'    => 4,
                ],
                [
                    'label'           => '🟢 Bajo',
                    'data'            => $bajo,
                    'backgroundColor' => '#22c55e',
                    'borderRadius'    => 4,
                ],
            ],
            'labels' => $carreras,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend'  => ['display' => true, 'position' => 'top'],
                'tooltip' => ['mode' => 'index', 'intersect' => false],
            ],
            'scales' => [
                'x' => [
                    'stacked' => true,
                    'title'   => ['display' => true, 'text' => 'Carrera'],
                ],
                'y' => [
                    'stacked'     => true,
                    'beginAtZero' => true,
                    'ticks'       => ['stepSize' => 1],
                    'title'       => ['display' => true, 'text' => 'N° de estudiantes'],
                ],
            ],
        ];
    }
}
