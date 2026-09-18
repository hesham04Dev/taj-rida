<?php

namespace App\Filament\Resources\BudgetTransactions\Widgets;

use App\Models\BudgetTransaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BudgetStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalCredits = BudgetTransaction::where('type', 'credit')->sum('amount');
        $totalReturns = BudgetTransaction::where('type', 'return')->sum('amount');
        $totalGifts = BudgetTransaction::where('type', 'gift')->sum('amount');
        $totalExpenses = BudgetTransaction::where('type', 'expense')->sum('amount');

        $currentDebt = max(0, $totalCredits - $totalReturns);
        $currentBalance = ($totalCredits + $totalGifts) - ($totalReturns + $totalExpenses);

        return [
            Stat::make('الرصيد الحالي (Total Balance)', number_format($currentBalance, 2))
                ->color($currentBalance >= 0 ? 'success' : 'danger'),
            Stat::make('إجمالي التبرعات', number_format($totalGifts, 2))
                ->color('success'),
            Stat::make('الديون الحالية (قرض حسن)', number_format($currentDebt, 2))
                ->color('danger'),
            Stat::make('إجمالي المصروفات', number_format($totalExpenses, 2))
                ->color('warning'),
            Stat::make('إجمالي السلف الواردة', number_format($totalCredits, 2)),
            Stat::make('إجمالي السلف المسددة', number_format($totalReturns, 2)),
        ];
    }
}
