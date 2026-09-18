<?php

namespace App\Filament\Resources\Creditors\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BudgetTransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'budgetTransactions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label('Transaction Type')
                    ->options([
                        'credit' => 'Credit In (Qard Hasan)',
                        'return' => 'Credit Return (Loan Repayment)',
                        'gift' => 'Gift (Sadaqah)',
                        'expense' => 'Expense',
                    ])
                    ->required(),
                TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->required(),
                DatePicker::make('date')
                    ->label('Date')
                    ->required()
                    ->default(now()),
                Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('date')
            ->columns([
                TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'credit' => 'Credit In',
                        'return' => 'Return',
                        'gift' => 'Gift',
                        'expense' => 'Expense',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'credit' => 'warning',
                        'return' => 'danger',
                        'gift' => 'success',
                        'expense' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->numeric(2)
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(30),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
