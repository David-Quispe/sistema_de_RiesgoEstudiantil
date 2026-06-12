<?php

namespace App\Filament\Resources\EntrevistaResource\Pages;

use App\Filament\Resources\EntrevistaResource;
use App\Services\AlertaService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateEntrevista extends CreateRecord
{
    protected static string $resource = EntrevistaResource::class;

    protected function afterCreate(): void
    {
        // 1. Calcular riesgo ponderado con los indicadores guardados
        $this->record->calcularRiesgo();

        // 2. Recargar el registro para leer el nivel_riesgo ya calculado
        $this->record->refresh();

        // 3. Evaluar y generar alertas si corresponde
        $nivelAntes = null; // nueva entrevista, no había nivel previo
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
