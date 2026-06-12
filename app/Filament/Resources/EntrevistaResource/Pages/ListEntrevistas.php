<?php

namespace App\Filament\Resources\EntrevistaResource\Pages;

use App\Filament\Resources\EntrevistaResource;
use Filament\Resources\Pages\ListRecords;

class ListEntrevistas extends ListRecords
{
    protected static string $resource = EntrevistaResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
