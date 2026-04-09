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
class RevisionsRelationManager extends RelationManager
{
    protected static string $relationship = 'revisions';
    protected static ?string $title = 'جلسات المراجعة';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('sura_id')
                    ->relationship('sura', 'name')
                    ->label('السورة')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('from_page')
                    ->label('من آية')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('to_page')
                    ->label('إلى آية')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('grade')
                    ->label('التقييم')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('date')
                    ->label('التاريخ')
                    ->default(now())
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('sura.name')->label('السورة'),
                Tables\Columns\TextColumn::make('from_page')->label('من صفحة'),
                Tables\Columns\TextColumn::make('to_page')->label('إلى صفحة'),
                Tables\Columns\TextColumn::make('grade')->label('التقييم'),
                Tables\Columns\TextColumn::make('date')->label('التاريخ')->date(),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make()->label('إضافة مراجعة'),
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
