<?php

namespace App\Filament\Resources\Creditors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CreditorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable(),
                TextColumn::make('total_credit')
                    ->label('إجمالي القروض المقدمة')
                    ->numeric(2),
                TextColumn::make('total_returned')
                    ->label('إجمالي المسدد')
                    ->numeric(2),
                TextColumn::make('remaining_balance')
                    ->label('الرصيد المتبقي (الدين)')
                    ->numeric(2)
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'success'),
            ])
            ->filters([
                //
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
