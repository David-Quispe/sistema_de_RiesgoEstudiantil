<?php

namespace App\Filament\Resources\DerivacionResource\Pages;

use App\Filament\Resources\DerivacionResource;
use Filament\Resources\Pages\ListRecords;

class ListDerivaciones extends ListRecords
{
    protected static string $resource = DerivacionResource::class;
    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
