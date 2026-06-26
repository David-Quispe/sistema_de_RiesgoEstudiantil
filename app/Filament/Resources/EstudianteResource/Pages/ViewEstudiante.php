<?php

namespace App\Filament\Resources\EstudianteResource\Pages;

use App\Filament\Resources\EstudianteResource;
use App\Filament\Widgets\IndicadoresEstudianteWidget;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
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

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generarFichaPdf')
                ->label('Generar ficha PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->action(fn () => $this->generarFichaPdf()),
        ];
    }

    /**
     * Genera la ficha individual del estudiante en PDF (CUS09 / RF-Ficha).
     * Incluye historial de entrevistas, indicadores de la última entrevista,
     * observaciones y derivaciones asociadas.
     */
    public function generarFichaPdf(): \Symfony\Component\HttpFoundation\Response
    {
        $estudiante = $this->record;
        $estudiante->loadMissing('institucion');

        $entrevistas = $estudiante->entrevistas()
            ->with(['periodo', 'consejero', 'indicadores'])
            ->orderByDesc('fecha_entrevista')
            ->get();

        $ultimaEntrevista = $entrevistas->first();

        $derivaciones = \App\Models\Derivacion::whereHas('entrevista', function ($q) use ($estudiante) {
            $q->where('estudiante_id', $estudiante->id);
        })->orderByDesc('created_at')->get();

        $pdf = Pdf::loadView('reportes.pdf-ficha-estudiante', [
            'estudiante'       => $estudiante,
            'entrevistas'      => $entrevistas,
            'ultimaEntrevista' => $ultimaEntrevista,
            'derivaciones'     => $derivaciones,
        ])->setPaper('a4', 'portrait');

        $filename = "SMER_Ficha_{$estudiante->codigo}.pdf";

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename
        );
    }
}
