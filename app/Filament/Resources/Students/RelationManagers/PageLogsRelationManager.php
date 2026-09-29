<?php

namespace App\Filament\Resources\Students\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PageLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'pageLogs';

    protected static ?string $title = 'سجل الصفحات';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('curriculum_id')
                    ->label('الجزء')
                    ->relationship('curriculum', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label('النوع')
                    ->options([
                        'recitation' => 'تسميع',
                        'revision' => 'مراجعة',
                        'test' => 'اختبار',
                    ])
                    ->required(),
                Forms\Components\Select::make('grade')
                    ->label('التقييم')
                    ->options([
                        'ممتاز' => 'ممتاز',
                        'جيد جدا' => 'جيد جدا',
                        'جيد' => 'جيد',
                        'مقبول' => 'مقبول',
                        'ضعيف' => 'ضعيف',
                    ])
                    ->nullable(),
                Forms\Components\TagsInput::make('children')
                    ->label('الصفحات/السور')
                    ->placeholder('أضف صفحة/سورة'),
                Forms\Components\TextInput::make('count')
                    ->label('عدد الصفحات/السور')
                    ->numeric()
                    ->required(),
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
                Tables\Columns\TextColumn::make('curriculum.name')->label('الجزء'),
                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'recitation' => 'تسميع',
                        'revision' => 'مراجعة',
                        'test' => 'اختبار',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'recitation' => 'success',
                        'revision' => 'info',
                        'test' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('grade')->label('التقييم'),
                Tables\Columns\TextColumn::make('children')
                    ->label('الصفحات/السور')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode('، ', $state) : $state)
                    ->wrap(),
                Tables\Columns\TextColumn::make('count')->label('العدد'),
                Tables\Columns\TextColumn::make('date')->label('التاريخ')->date(),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make()->label('إضافة سجل'),
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
