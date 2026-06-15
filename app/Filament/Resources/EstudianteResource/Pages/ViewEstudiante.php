<?php

namespace App\Filament\Resources\EstudianteResource\Pages;

use App\Filament\Resources\EstudianteResource;
use App\Filament\Widgets\IndicadoresEstudianteWidget;
use Filament\Resources\Pages\ViewRecord;

class ViewEstudiante extends ViewRecord
{
    protected static string $resource = EstudianteResource::class;

    /**
     * Muestra el widget de gráfico de indicadores debajo de los datos del estudiante.
     */
    protected function getFooterWidgets(): array
    {
        return [
            IndicadoresEstudianteWidget::make([
                'estudiante_id' => $this->record->id,
            ]),
        ];
    }
}
