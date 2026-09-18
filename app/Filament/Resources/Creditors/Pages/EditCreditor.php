<?php

namespace App\Filament\Resources\Creditors\Pages;

use App\Filament\Resources\Creditors\CreditorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCreditor extends EditRecord
{
    protected static string $resource = CreditorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
