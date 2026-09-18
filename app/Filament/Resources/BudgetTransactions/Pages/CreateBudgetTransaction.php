<?php

namespace App\Filament\Resources\BudgetTransactions\Pages;

use App\Filament\Resources\BudgetTransactions\BudgetTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBudgetTransaction extends CreateRecord
{
    protected static string $resource = BudgetTransactionResource::class;
}
