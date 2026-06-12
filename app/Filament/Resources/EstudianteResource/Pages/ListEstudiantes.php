<?php

namespace App\Filament\Resources\EstudianteResource\Pages;

use App\Filament\Resources\EstudianteResource;
use Filament\Resources\Pages\ListRecords;

class ListEstudiantes extends ListRecords
{
    protected static string $resource = EstudianteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
