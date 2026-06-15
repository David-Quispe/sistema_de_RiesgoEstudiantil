<?php

namespace App\Filament\Resources\PeriodoResource\Pages;

use App\Filament\Resources\PeriodoResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreatePeriodo extends CreateRecord
{
    protected static string $resource = PeriodoResource::class;

    // Al crear, forzar institucion_id = 1 y quitar campos virtuales (anio, semestre)
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['anio'], $data['semestre']);
        $data['institucion_id'] = 1;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
