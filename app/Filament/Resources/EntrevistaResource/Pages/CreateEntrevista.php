<?php

namespace App\Filament\Resources\EntrevistaResource\Pages;

use App\Filament\Resources\EntrevistaResource;
use App\Models\ConfiguracionRiesgo;
use App\Services\AlertaService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateEntrevista extends CreateRecord
{
    protected static string $resource = EntrevistaResource::class;

    /**
     * Se ejecuta antes de renderizar el formulario vacío.
     * Inyecta los 6 indicadores desde CONFIGURACION_RIESGO para que
     * aparezcan pre-cargados (nombre y peso fijos, puntaje vacío).
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['indicadores'] = ConfiguracionRiesgo::where('activo', 1)
            ->orderBy('id')
            ->get()
            ->map(fn($config) => [
                'nombre'      => $config->indicador,
                'peso'        => $config->peso,
                'puntaje'     => null,
                'observacion' => null,
            ])
            ->toArray();

        return $data;
    }

    protected function afterCreate(): void
    {
        // 1. Calcular riesgo ponderado con los indicadores guardados
        $this->record->calcularRiesgo();

        // 2. Recargar el registro para leer el nivel_riesgo ya calculado
        $this->record->refresh();

        // 3. Evaluar y generar alertas si corresponde
        app(AlertaService::class)->evaluarEntrevista($this->record);

        // 4. Notificación visual en el panel según el nivel resultante
        match ($this->record->nivel_riesgo) {
            'ALTO'  => Notification::make()
                            ->title('⚠️ Riesgo ALTO detectado')
                            ->body("El estudiante fue clasificado con riesgo ALTO (puntaje: {$this->record->puntaje_total}). Se generaron alertas automáticamente.")
                            ->danger()
                            ->persistent()
                            ->send(),

            'MEDIO' => Notification::make()
                            ->title('🟡 Riesgo MEDIO')
                            ->body("El estudiante fue clasificado con riesgo MEDIO (puntaje: {$this->record->puntaje_total}). Monitorear evolución.")
                            ->warning()
                            ->send(),

            default => Notification::make()
                            ->title('🟢 Riesgo BAJO')
                            ->body("El estudiante fue clasificado con riesgo BAJO (puntaje: {$this->record->puntaje_total}).")
                            ->success()
                            ->send(),
        };
    }
}
