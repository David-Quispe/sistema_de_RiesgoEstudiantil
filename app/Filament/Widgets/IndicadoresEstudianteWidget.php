<?php

namespace App\Filament\Widgets;

use App\Models\Entrevista;
use Filament\Widgets\Widget;

/**
 * Gráfico de radar con los 6 indicadores de la última entrevista de un estudiante.
 * Se usa en ViewEstudiante pasando $record->id como $estudiante_id.
 */
class IndicadoresEstudianteWidget extends Widget
{
    protected static bool $isDiscovered = false;
    protected static string $view = 'filament.widgets.indicadores-estudiante-widget';

    // ID del estudiante que se muestra (0 = sin contexto)
    public int $estudiante_id = 0;

    // Datos procesados para la vista
    public array $etiquetas = [];
    public array $puntajes  = [];
    public array $pesos     = [];
    public ?string $nivelRiesgo   = null;
    public ?float  $puntajeTotal  = null;
    public ?string $fechaEntrevista = null;

    public function mount(): void
    {
        $this->cargarDatos();
    }

    protected function cargarDatos(): void
    {
        if (!$this->estudiante_id) {
            return;
        }

        $entrevista = Entrevista::where('estudiante_id', $this->estudiante_id)
            ->with('indicadores')
            ->latest('fecha_entrevista')
            ->first();

        if (!$entrevista) {
            return;
        }

        $this->nivelRiesgo    = $entrevista->nivel_riesgo;
        $this->puntajeTotal   = $entrevista->puntaje_total;
        $this->fechaEntrevista = $entrevista->fecha_entrevista->format('d/m/Y');

        foreach ($entrevista->indicadores as $ind) {
            $this->etiquetas[] = $ind->nombre;
            $this->puntajes[]  = (float) $ind->puntaje;
            $this->pesos[]     = (float) $ind->peso;
        }
    }
}
