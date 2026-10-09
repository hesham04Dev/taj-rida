<?php

namespace App\Filament\Resources\Dawaras\Pages;

use App\Filament\Resources\Dawaras\DawaraResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDawaras extends ManageRecords
{
    protected static string $resource = DawaraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
