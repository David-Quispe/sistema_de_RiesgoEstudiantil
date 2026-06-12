<?php

namespace App\Filament\Widgets;

use App\Models\Entrevista;
use App\Models\Periodo;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class TendenciaRiesgoWidget extends ChartWidget
{
    protected static ?int    $sort    = 3;
    protected static ?string $heading = '📈 Tendencia de riesgo por periodo';

    protected int|string|array $columnSpan = 'full';
    protected static ?string   $maxHeight  = '280px';   // ← ?string, no ?int

    protected function getData(): array
    {
        $periodos = Periodo::orderBy('fecha_inicio')->get();

        $labels = [];
        $alto   = [];
        $medio  = [];
        $bajo   = [];

        foreach ($periodos as $periodo) {
            $labels[] = $periodo->nombre;

            $base = Entrevista::where('periodo_id', $periodo->id);

            if (Auth::user()?->esConsejero()) {
                $base->where('consejero_id', Auth::id());
            }

            $alto[]  = (clone $base)->where('nivel_riesgo', 'ALTO')->count();
            $medio[] = (clone $base)->where('nivel_riesgo', 'MEDIO')->count();
            $bajo[]  = (clone $base)->where('nivel_riesgo', 'BAJO')->count();
        }

        return [
            'datasets' => [
                [
                    'label'           => '🔴 Riesgo Alto',
                    'data'            => $alto,
                    'borderColor'     => '#ef4444',
                    'backgroundColor' => 'rgba(239,68,68,0.12)',
                    'borderWidth'     => 2,
                    'pointRadius'     => 4,
                    'pointHoverRadius'=> 6,
                    'tension'         => 0.4,
                    'fill'            => true,
                ],
                [
                    'label'           => '🟡 Riesgo Medio',
                    'data'            => $medio,
                    'borderColor'     => '#f59e0b',
                    'backgroundColor' => 'rgba(245,158,11,0.12)',
                    'borderWidth'     => 2,
                    'pointRadius'     => 4,
                    'pointHoverRadius'=> 6,
                    'tension'         => 0.4,
                    'fill'            => true,
                ],
                [
                    'label'           => '🟢 Riesgo Bajo',
                    'data'            => $bajo,
                    'borderColor'     => '#22c55e',
                    'backgroundColor' => 'rgba(34,197,94,0.12)',
                    'borderWidth'     => 2,
                    'pointRadius'     => 4,
                    'pointHoverRadius'=> 6,
                    'tension'         => 0.4,
                    'fill'            => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend'  => ['display' => true, 'position' => 'top'],
                'tooltip' => ['mode' => 'index', 'intersect' => false],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks'       => ['stepSize' => 1],
                    'title'       => ['display' => true, 'text' => 'N° de estudiantes'],
                ],
                'x' => [
                    'title' => ['display' => true, 'text' => 'Periodo académico'],
                ],
            ],
        ];
    }
}
