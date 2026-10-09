<?php

namespace App\Filament\Resources\BudgetTransactions;

use App\Filament\Resources\BudgetTransactions\Pages\CreateBudgetTransaction;
use App\Filament\Resources\BudgetTransactions\Pages\EditBudgetTransaction;
use App\Filament\Resources\BudgetTransactions\Pages\ListBudgetTransactions;
use App\Filament\Resources\BudgetTransactions\Schemas\BudgetTransactionForm;
use App\Filament\Resources\BudgetTransactions\Tables\BudgetTransactionsTable;
use App\Filament\Resources\BudgetTransactions\Widgets\BudgetStats;
use App\Filament\Widgets\GiftsAndExchangeRatesWidget;
use App\Models\BudgetTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BudgetTransactionResource extends Resource
{
    protected static ?string $model = BudgetTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    public static function getModelLabel(): string
    {
        return 'حركة ميزانية';
    }

    public static function getPluralModelLabel(): string
    {
        return 'حركات الميزانية';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'الميزانية';
    }

    public static function form(Schema $schema): Schema
    {
        return BudgetTransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BudgetTransactionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBudgetTransactions::route('/'),
            'create' => CreateBudgetTransaction::route('/create'),
            'edit' => EditBudgetTransaction::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            BudgetStats::class,
            GiftsAndExchangeRatesWidget::class,
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->role === 'admin';
    }
}
