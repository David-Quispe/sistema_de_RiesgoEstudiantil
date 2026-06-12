<?php

namespace App\Filament\Pages;

use App\Models\Entrevista;
use App\Models\Periodo;
use App\Models\Estudiante;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class Reportes extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Reportes';
    protected static ?string $title           = 'Reportes y Exportaciones';
    protected static ?int    $navigationSort  = 8;
    protected static string  $view            = 'filament.pages.reportes';

    public ?string $periodo_id = null;
    public ?string $carrera    = null;
    public ?string $nivel      = null;

    // Solo coordinador, bienestar y admin
    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && ($user->esAdmin() || $user->esCoordinador() || $user->esBienestar());
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Filtros del reporte')
                ->columns(3)
                ->schema([
                    Select::make('periodo_id')
                        ->label('Periodo')
                        ->options(fn() => Periodo::pluck('nombre', 'id')->toArray())
                        ->placeholder('Todos los periodos'),

                    Select::make('carrera')
                        ->label('Carrera')
                        ->options(fn() => Estudiante::distinct()->pluck('carrera', 'carrera')->toArray())
                        ->placeholder('Todas las carreras'),

                    Select::make('nivel')
                        ->label('Nivel de riesgo')
                        ->options([
                            'ALTO'  => '🔴 Alto',
                            'MEDIO' => '🟡 Medio',
                            'BAJO'  => '🟢 Bajo',
                        ])
                        ->placeholder('Todos los niveles'),
                ]),
        ]);
    }

    // Obtiene los datos filtrados
    public function getDatosReporte(): \Illuminate\Database\Eloquent\Collection
    {
        $query = Entrevista::with(['estudiante', 'consejero', 'periodo'])
            ->when($this->periodo_id, fn($q) => $q->where('periodo_id', $this->periodo_id))
            ->when($this->nivel,      fn($q) => $q->where('nivel_riesgo', $this->nivel))
            ->when($this->carrera,    fn($q) => $q->whereHas('estudiante', fn($sq) =>
                $sq->where('carrera', $this->carrera)
            ))
            ->orderBy('nivel_riesgo')
            ->orderBy('fecha_entrevista', 'desc');

        return $query->get();
    }

    // Exportar PDF
    public function exportarPDF(): \Symfony\Component\HttpFoundation\Response
    {
        $datos   = $this->getDatosReporte();
        $periodo = $this->periodo_id ? Periodo::find($this->periodo_id)?->nombre : 'Todos';
        $carrera = $this->carrera ?? 'Todas';
        $nivel   = $this->nivel   ?? 'Todos';

        $pdf = Pdf::loadView('reportes.pdf-riesgo', compact('datos', 'periodo', 'carrera', 'nivel'))
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn() => print($pdf->output()),
            "SMER_Reporte_Riesgo_{$periodo}.pdf"
        );
    }

    // Exportar Excel (CSV compatible con Excel)
    public function exportarExcel(): \Symfony\Component\HttpFoundation\Response
    {
        $datos   = $this->getDatosReporte();
        $periodo = $this->periodo_id ? Periodo::find($this->periodo_id)?->nombre : 'Todos';

        $filename = "SMER_Reporte_{$periodo}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($datos) {
            $handle = fopen('php://output', 'w');

            // BOM para que Excel reconozca UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Cabecera
            fputcsv($handle, [
                'Código', 'Estudiante', 'Carrera', 'Ciclo',
                'Periodo', 'Fecha Entrevista', 'Consejero',
                'Puntaje Total', 'Nivel de Riesgo', 'Observaciones',
            ], ';');

            foreach ($datos as $e) {
                fputcsv($handle, [
                    $e->estudiante->codigo         ?? '',
                    $e->estudiante->nombre_completo ?? '',
                    $e->estudiante->carrera         ?? '',
                    $e->estudiante->ciclo           ?? '',
                    $e->periodo->nombre             ?? '',
                    $e->fecha_entrevista?->format('d/m/Y') ?? '',
                    $e->consejero->nombre_completo  ?? '',
                    $e->puntaje_total               ?? '',
                    $e->nivel_riesgo                ?? '',
                    $e->observaciones               ?? '',
                ], ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
