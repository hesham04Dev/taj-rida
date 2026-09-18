<?php

namespace App\Filament\Resources\BudgetTransactions\Pages;

use App\Filament\Resources\BudgetTransactions\BudgetTransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBudgetTransaction extends EditRecord
{
    protected static string $resource = BudgetTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
