<?php

namespace App\Filament\Resources\ConfiguracionRiesgoResource\Pages;

use App\Filament\Resources\ConfiguracionRiesgoResource;
use Filament\Resources\Pages\ListRecords;

class ListConfiguracionRiesgo extends ListRecords
{
    protected static string $resource = ConfiguracionRiesgoResource::class;

    // Sin botón "Crear" — los 6 indicadores son fijos
    protected function getHeaderActions(): array
    {
        return [];
    }
}
