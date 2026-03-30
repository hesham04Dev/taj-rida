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
class AttendancesRelationManager extends RelationManager
{
    protected static string $relationship = 'attendances';
    protected static ?string $title = 'سجل الحضور';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\DatePicker::make('date')
                    ->label('التاريخ')
                    ->default(now())
                    ->required(),
                Forms\Components\Toggle::make('is_present')
                    ->label('حاضر؟')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('date')->label('التاريخ')->date(),
                Tables\Columns\IconColumn::make('is_present')->label('حاضر؟')->boolean(),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make()->label('إضافة حضور'),
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
            ->defaultSort('date', 'desc');
    }
}
