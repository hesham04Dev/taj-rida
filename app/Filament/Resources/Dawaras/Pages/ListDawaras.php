<?php

namespace App\Filament\Resources\Dawaras\Pages;

use App\Filament\Resources\Dawaras\DawarasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDawaras extends ListRecords
{
    protected static string $resource = DawarasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
