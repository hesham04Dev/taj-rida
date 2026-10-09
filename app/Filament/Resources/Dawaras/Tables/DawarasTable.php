<?php

namespace App\Filament\Resources\Dawaras\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn as ColumnsBadgeColumn;
use Filament\Tables\Columns\TextColumn as ColumnsTextColumn;
use Filament\Tables\Table;

class DawarasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                ColumnsTextColumn::make('name')
                    ->searchable(),

                ColumnsBadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'gray' => 'completed',
                    ]),
                ColumnsTextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                ColumnsTextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                ColumnsTextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
