<?php

namespace App\Filament\Resources\Creditors\Pages;

use App\Filament\Resources\Creditors\CreditorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCreditors extends ListRecords
{
    protected static string $resource = CreditorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
