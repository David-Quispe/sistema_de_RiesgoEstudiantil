<?php

namespace App\Filament\Resources\ConfiguracionRiesgoResource\Pages;

use App\Filament\Resources\ConfiguracionRiesgoResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditConfiguracionRiesgo extends EditRecord
{
    protected static string $resource = ConfiguracionRiesgoResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function afterSave(): void
    {
        $nombre = $this->record->indicador;

        Notification::make()
            ->title('Peso actualizado')
            ->body("El indicador '{$nombre}' fue actualizado. Los nuevos pesos se aplicaran en las proximas entrevistas.")
            ->success()
            ->send();
    }
}
