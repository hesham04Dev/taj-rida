<?php

namespace App\Filament\Resources\Students\RelationManagers;

use App\Models\Curriculum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MemorizationsRelationManager extends RelationManager
{
    protected static string $relationship = 'memorizations';

    protected static ?string $title = 'الأجزاء المحفوظة';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('is_need_rememorisation')
                    ->label('يحتاج لإعادة حفظ')
                    ->default(false),

                Select::make('curriculum_id')
                    ->label('الجزء')
                    ->options(
                        Curriculum::orderBy('number')
                            ->get()
                            ->mapWithKeys(fn ($j) => [$j->id => $j->name])
                    )
                    ->required()
                    ->searchable(),

                Grid::make(3)->schema([
                    TextInput::make('memorized_pages')
                        ->label('عدد العناصر المحفوظة')
                        ->numeric()
                        ->default(0)
                        ->required(),

                    TextInput::make('memorization_repetition')
                        ->label('عدد مرات الحفظ')
                        ->numeric()
                        ->default(1)
                        ->required(),

                    TextInput::make('revision_repetition')
                        ->label('عدد مرات المراجعة')
                        ->numeric()
                        ->default(0),
                ]),

                Grid::make(2)->schema([
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
                ]),

                Grid::make(3)->schema([
                    TextInput::make('test_grade')
                        ->label('درجة الاختبار')
                        ->nullable(),

                    TextInput::make('test_counts')
                        ->label('مرات الاختبار')
                        ->numeric()
                        ->default(0),

                    TextInput::make('last_test_name')
                        ->label('اسم آخر اختبار')
                        ->nullable(),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('curriculum.name')
            ->contentGrid([
                'sm' => 1,
                'md' => 2,
                'xl' => 2,
            ])
            ->columns([
                Stack::make([
                    Split::make([
                        Stack::make([
                            TextColumn::make('curriculum.name')
                                ->weight('bold')
                                ->size('lg')
                                ->icon('heroicon-m-book-open'),

                            TextColumn::make('memorized_count')
                                ->getStateUsing(fn ($record) => $record->memorized_pages
                                    ? "محفوظ {$record->memorized_pages} عنصر"
                                    : 'لم يبدأ')
                                ->color('primary')
                                ->size('sm')
                                ->icon('heroicon-m-document-text'),

                            TextColumn::make('updated_at')
                                ->date('Y-m-d')
                                ->color('gray')
                                ->size('xs'),
                        ]),

                        TextColumn::make('memorization_percent')
                            ->getStateUsing(fn ($record) => $record->curriculum
                                ? (count($record->curriculum->children) > 0
                                    ? round(($record->memorized_pages / count($record->curriculum->children)) * 100).'%'
                                    : '0%')
                                : '0%')
                            ->badge()
                            ->size('xl')
                            ->color(fn ($record) => ($record->memorized_pages ?? 0) >= count($record->curriculum?->children ?? []) ? 'success' : 'warning')
                            ->grow(false),
                    ])->extraAttributes(['class' => 'mb-3']),

                    Split::make([
                        Panel::make([
                            Stack::make([
                                TextColumn::make('label_mem')
                                    ->default('بيانات الحفظ')
                                    ->weight('bold')
                                    ->size('sm')
                                    ->color('info'),
                                TextColumn::make('memorization_degree')
                                    ->formatStateUsing(fn ($state) => "الدرجة: $state")
                                    ->icon('heroicon-m-star'),
                                TextColumn::make('memorization_repetition')
                                    ->formatStateUsing(fn ($state) => "التكرار: $state")
                                    ->icon('heroicon-m-arrow-path'),
                            ])->space(1),
                        ])->collapsible(),

                        Panel::make([
                            Stack::make([
                                TextColumn::make('label_rev')
                                    ->default('بيانات المراجعة')
                                    ->weight('bold')
                                    ->size('sm')
                                    ->color('success'),
                                TextColumn::make('revision_degree')
                                    ->formatStateUsing(fn ($state) => "الدرجة: $state")
                                    ->icon('heroicon-m-check-badge'),
                                TextColumn::make('revision_repetition')
                                    ->formatStateUsing(fn ($state) => "التكرار: $state")
                                    ->icon('heroicon-m-arrow-path-rounded-square'),
                            ])->space(1),
                        ]),
                    ]),

                    Panel::make([
                        Split::make([
                            Split::make([
                                TextColumn::make('label_test')->default('آخر اختبار')->size('xs text-gray-500')->grow(false),
                                TextColumn::make('last_test_name')->weight('bold')->default('-'),
                            ]),
                            Split::make([
                                Split::make([
                                    TextColumn::make('label_grade')->default('النتيجة')->size('xs text-gray-500')->grow(false),
                                    TextColumn::make('test_grade')->badge()->color('primary'),
                                ]),
                                Split::make([
                                    TextColumn::make('label_count')->default('المرات')->size('xs text-gray-500')->grow(false),
                                    TextColumn::make('test_counts')->icon('heroicon-m-hashtag'),
                                ]),
                            ]),
                        ]),
                    ]),

                    TextColumn::make('is_need_rememorisation')
                        ->visible(fn ($state) => $state)
                        ->formatStateUsing(fn () => '⚠️ يحتاج الطالب لإعادة تركيز وحفظ')
                        ->color('danger')
                        ->weight('bold')
                        ->alignCenter(),

                ])->space(3),
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
            ->defaultSort('curriculum_id');
    }
}
