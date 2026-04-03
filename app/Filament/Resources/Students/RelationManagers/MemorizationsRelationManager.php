<?php

namespace App\Filament\Resources\Students\RelationManagers;

use App\Models\Sura;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;

use Filament\Tables\Columns\Layout\Panel;


class MemorizationsRelationManager extends RelationManager
{
    protected static string $relationship = 'memorizations';

    protected static ?string $title = 'السور المحفوظة';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('is_need_rememorisation')
                        ->label('يحتاج لإعادة حفظ')
                        ->default(false),
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
                Grid::make(3)->schema([
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
            ->recordTitleAttribute('sura.name')
            // ->columns([
            //     TextColumn::make('sura.name')
            //         ->label('السورة')
            //         ->sortable()
            //         ->searchable(),
            //     TextColumn::make('memorization_percent')
            //         ->label('نسبة الحفظ')
            //         ->getStateUsing(fn ($record) => $record->sura
            //             ? round(($record->memorized_ayas / $record->sura->ayas_count) * 100).'%'
            //             : '-'
            //         )
            //         ->badge()
            //         ->color(fn ($record) => $record->sura && $record->memorized_ayas >= $record->sura->ayas_count ? 'success' : 'warning'),
            //     TextColumn::make('memorization_degree')->label('درجة الحفظ')->badge()->color('info'),
            //     TextColumn::make('memorization_repetition')->label('حفظ (مرات)')->badge()->color('gray'),
            //     TextColumn::make('revision_degree')->label('درجة المراجعة')->badge()->color('success'),
            //     TextColumn::make('revision_repetition')->label('مراجعة (مرات)')->badge()->color('gray'),
            
            //     TextColumn::make('test_grade')->label('درجة الاختبار')->badge()->color('primary'),
            //     TextColumn::make('test_counts')->label('اختبار (مرات)')->badge()->color('gray'),
            //     TextColumn::make("last_test_name")->label("اسم آخر اختبار"),
            //     TextColumn::make('is_need_rememorisation')
            //         ->label('إعادة حفظ')
            //         ->badge()
            //         ->color(fn ($state) => $state ? 'danger' : 'gray')
            //         ->formatStateUsing(fn ($state) => $state ? 'نعم' : 'لا'),
            //     TextColumn::make('updated_at')->label('تحديث')->date('Y-m-d'),
            // ])
            
       ->contentGrid([
    'sm' => 1,
    'md' => 2,
    'xl' => 2,
])
->columns([
    Stack::make([
        // --- الرأس: اسم السورة وحالة الإتقان ---
        Split::make([
            Stack::make([
                TextColumn::make('sura.name')
                    ->weight('bold')
                    ->size('lg')
                    ->icon('heroicon-m-book-open'),
                TextColumn::make('updated_at')
                    ->date('Y-m-d')
                    ->color('gray')
                    ->size('xs'),
            ]),
            
            TextColumn::make('memorization_percent')
                ->getStateUsing(fn ($record) => $record->sura 
                    ? round(($record->memorized_ayas / $record->sura->ayas_count) * 100).'%' 
                    : '0%')
                ->badge()
                ->size('xl')
                ->color(fn ($record) => $record->memorized_ayas >= ($record->sura->ayas_count ?? 0) ? 'success' : 'warning')
                ->grow(false),
        ])->extraAttributes(['class' => 'mb-3']),

        // --- شبكة البيانات المقسمة بإطارات (Borders) ---
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

                // 2. قسم المراجعة (داخل Panel لعمل بوردر)
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
                

            // ]),

        // 3. قسم الاختبار (بوردر عريض أسفل الكارت)
        Panel::make([
            // Grid::make(3)
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
                        TextColumn::make('label_count')->default('المرات')->size('xs text-gray-500')->grow(false)   ,
                        TextColumn::make('test_counts')->icon('heroicon-m-hashtag'),
                    ]),])
            ]),
                // ->schema([
                    
                // ]),
        ]),

        // 4. تنبيه إعادة الحفظ (يظهر فقط إذا كانت القيمة نعم)
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
            ->defaultSort('sura_id');
    }
}
