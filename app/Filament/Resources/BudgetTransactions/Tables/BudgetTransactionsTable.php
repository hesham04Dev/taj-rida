<?php

namespace App\Filament\Resources\BudgetTransactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BudgetTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('التاريخ')
                    ->date()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'credit' => 'سلف وارد',
                        'return' => 'سداد سلف',
                        'gift' => 'تبرع',
                        'expense' => 'مصروف',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'credit' => 'warning',
                        'return' => 'danger',
                        'gift' => 'success',
                        'expense' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('creditor.name')
                    ->label('الداعم / الدائن')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('المبلغ')
                    ->numeric(2)
                    ->sortable(),
                TextColumn::make('description')
                    ->label('الوصف')
                    ->limit(30)
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('النوع')
                    ->options([
                        'credit' => 'سلف وارد',
                        'return' => 'سداد سلف',
                        'gift' => 'تبرع',
                        'expense' => 'مصروف',
                    ]),
                SelectFilter::make('creditor_id')
                    ->relationship('creditor', 'name')
                    ->label('الداعم / الدائن'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
