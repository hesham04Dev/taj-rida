<?php

namespace App\Filament\Resources\Students\RelationManagers;

use App\Models\Sura;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MemorizationsRelationManager extends RelationManager
{
    protected static string $relationship = 'memorizations';

    protected static ?string $title = 'السور المحفوظة';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('sura_id')
                    ->label('السورة')
                    ->options(Sura::orderBy('id')->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Get $get, callable $set, $state) {
                        $sura = Sura::find($state);
                        if ($sura) {
                            $set('memorized_ayas', $sura->ayas_count);
                        }
                    }),
                Grid::make(2)->schema([
                    TextInput::make('memorized_ayas')
                        ->label('عدد الآيات المحفوظة')
                        ->numeric()
                        ->default(0)
                        ->required(),
                    TextInput::make('memorization_repetition')
                        ->label('عدد مرات الحفظ')
                        ->numeric()
                        ->default(1)
                        ->required(),
                ]),
                ToggleButtons::make('memorization_degree')
                    ->label('درجة الحفظ')
                    ->options([
                        'ممتاز' => 'ممتاز',
                        'جيد جدا' => 'جيد جدا',
                        'جيد' => 'جيد',
                        'مقبول' => 'مقبول',
                        'ضعيف' => 'ضعيف',
                    ])
                    ->inline()
                    ->nullable(),
                Grid::make(2)->schema([
                    TextInput::make('revision_repetition')
                        ->label('عدد مرات المراجعة')
                        ->numeric()
                        ->default(0),
                ]),
                ToggleButtons::make('revision_degree')
                    ->label('درجة المراجعة')
                    ->options([
                        'ممتاز' => 'ممتاز',
                        'جيد جدا' => 'جيد جدا',
                        'جيد' => 'جيد',
                        'مقبول' => 'مقبول',
                        'ضعيف' => 'ضعيف',
                    ])
                    ->inline()
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sura.name')
            ->columns([
                TextColumn::make('sura.name')
                    ->label('السورة')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('memorization_percent')
                    ->label('نسبة الحفظ')
                    ->getStateUsing(fn ($record) => $record->sura
                        ? round(($record->memorized_ayas / $record->sura->ayas_count) * 100).'%'
                        : '-'
                    )
                    ->badge()
                    ->color(fn ($record) => $record->sura && $record->memorized_ayas >= $record->sura->ayas_count ? 'success' : 'warning'),
                TextColumn::make('memorization_degree')->label('درجة الحفظ')->badge()->color('info'),
                TextColumn::make('memorization_repetition')->label('حفظ (مرات)')->badge()->color('gray'),
                TextColumn::make('revision_degree')->label('درجة المراجعة')->badge()->color('success'),
                TextColumn::make('revision_repetition')->label('مراجعة (مرات)')->badge()->color('gray'),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make()->label('إضافة سورة'),
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
            ->defaultSort('sura_id');
    }
}
