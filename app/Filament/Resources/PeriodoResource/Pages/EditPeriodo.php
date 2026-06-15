<?php

namespace App\Filament\Resources\PeriodoResource\Pages;

use App\Filament\Resources\PeriodoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPeriodo extends EditRecord
{
    protected static string $resource = PeriodoResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['anio'], $data['semestre']);
        $data['institucion_id'] = 1;
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
