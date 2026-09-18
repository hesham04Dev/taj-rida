<?php

namespace App\Filament\Resources\BudgetTransactions\Pages;

use App\Filament\Resources\BudgetTransactions\BudgetTransactionResource;
use App\Filament\Resources\BudgetTransactions\Widgets\BudgetStats;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBudgetTransactions extends ListRecords
{
    protected static string $resource = BudgetTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BudgetStats::class,
        ];
    }
}
