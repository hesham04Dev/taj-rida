<?php

namespace App\Filament\Resources\Students\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
class PointTransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'pointTransactions';
    protected static ?string $title = 'سجل النقاط';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('amount')
                    ->label('القيمة')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('reason')
                    ->label('السبب')
                    ->required(),
                Forms\Components\Select::make('teacher_id')
                    ->relationship('teacher', 'name')
                    ->label('الأستاذ')
                    ->default(auth()->id())
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('amount')->label('القيمة')->badge()->color(fn ($state) => $state > 0 ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('reason')->label('السبب'),
                Tables\Columns\TextColumn::make('teacher.name')->label('الأستاذ'),
                Tables\Columns\TextColumn::make('created_at')->label('تاريخ العملية')->dateTime(),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make()->label('إضافة نقاط'),
            ])
            ->recordActions([
                EditAction::make()->label('تعديل'),
                DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('حذف'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
