<?php

namespace App\Filament\Resources\Creditors;

use App\Filament\Resources\Creditors\Pages\CreateCreditor;
use App\Filament\Resources\Creditors\Pages\EditCreditor;
use App\Filament\Resources\Creditors\Pages\ListCreditors;
use App\Filament\Resources\Creditors\RelationManagers\BudgetTransactionsRelationManager;
use App\Filament\Resources\Creditors\Schemas\CreditorForm;
use App\Filament\Resources\Creditors\Tables\CreditorsTable;
use App\Models\Creditor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CreditorResource extends Resource
{
    protected static ?string $model = Creditor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getModelLabel(): string
    {
        return 'داعم / دائن';
    }

    public static function getPluralModelLabel(): string
    {
        return 'الداعمين والدائنين';
    }

    public static function form(Schema $schema): Schema
    {
        return CreditorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CreditorsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            BudgetTransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCreditors::route('/'),
            'create' => CreateCreditor::route('/create'),
            'edit' => EditCreditor::route('/{record}/edit'),
        ];
    }

    // public static function canViewAny(): bool
    // {
    //     return auth()->user()->role === 'admin';
    // }
}
