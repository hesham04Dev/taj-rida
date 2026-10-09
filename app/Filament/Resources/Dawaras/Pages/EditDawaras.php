<?php

namespace App\Filament\Resources\Dawaras\Pages;

use App\Filament\Resources\Dawaras\DawarasResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDawaras extends EditRecord
{
    protected static string $resource = DawarasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
