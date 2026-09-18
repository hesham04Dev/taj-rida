<?php

namespace App\Filament\Resources\Curriculum\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CurriculumTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('رقم الجزء')
                    ->sortable()
                    ->width(80),

                TextColumn::make('name')
                    ->label('اسم الجزء')
                    ->searchable(),

                TextColumn::make('children_count')
                    ->label('عدد العناصر')
                    ->getStateUsing(fn (mixed $record): int => count($record->children ?? []))
                    ->badge()
                    ->color('info'),
            ])
            ->defaultSort('number')
            ->recordActions([
                EditAction::make()->label('تعديل'),
            ]);
    }
}
