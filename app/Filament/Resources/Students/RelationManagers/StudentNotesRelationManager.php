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
class StudentNotesRelationManager extends RelationManager
{
    protected static string $relationship = 'studentNotes';
    protected static ?string $title = 'الملاحظات والتقييمات';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Textarea::make('description')
                    ->label('الملاحظة')
                    ->required(),
                Forms\Components\TextInput::make('rating')
                    ->label('التقييم العام (من 10)')
                    ->numeric()
                    ->maxValue(10)
                    ->minValue(1),
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
                Tables\Columns\TextColumn::make('description')->label('الملاحظة')->limit(50),
                Tables\Columns\TextColumn::make('rating')->label('التقييم')->badge()->color(fn ($state) => $state >= 8 ? 'success' : ($state >= 5 ? 'warning' : 'danger')),
                Tables\Columns\TextColumn::make('date')->label('التاريخ')->date(),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make()->label('إضافة ملاحظة'),
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
