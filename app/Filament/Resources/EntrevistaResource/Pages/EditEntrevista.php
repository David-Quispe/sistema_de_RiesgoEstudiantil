<?php

namespace App\Filament\Resources\EntrevistaResource\Pages;

use App\Filament\Resources\EntrevistaResource;
use App\Services\AlertaService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditEntrevista extends EditRecord
{
    protected static string $resource = EntrevistaResource::class;

    // Guardamos el nivel anterior para detectar cambios
    private ?string $nivelAntes = null;

    protected function beforeSave(): void
    {
        $this->nivelAntes = $this->record->getOriginal('nivel_riesgo');
    }

    protected function afterSave(): void
    {
        // 1. Recalcular riesgo con los indicadores actualizados
        $this->record->calcularRiesgo();

        // 2. Recargar para leer el nuevo nivel
        $this->record->refresh();

        // 3. Evaluar alertas (detecta riesgo alto y deterioro progresivo)
        app(AlertaService::class)->evaluarEntrevista($this->record);

        // 4. Solo notificar si el nivel cambió
        $nivelNuevo = $this->record->nivel_riesgo;

        if ($this->nivelAntes !== $nivelNuevo) {
            $emojis = ['BAJO' => '🟢', 'MEDIO' => '🟡', 'ALTO' => '🔴'];
            $emoji  = $emojis[$nivelNuevo] ?? '';

            $notif = Notification::make()
                ->title("{$emoji} Nivel de riesgo actualizado: {$nivelNuevo}")
                ->body("Cambió de {$this->nivelAntes} → {$nivelNuevo} (puntaje: {$this->record->puntaje_total}).");

            if ($nivelNuevo === 'ALTO') {
                $notif->danger()->persistent()->send();
            } elseif ($nivelNuevo === 'MEDIO') {
                $notif->warning()->send();
            } else {
                $notif->success()->send();
            }
        }
    }
}
