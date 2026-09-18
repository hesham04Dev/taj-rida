<?php

namespace App\Filament\Resources\Curriculum\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CurriculumForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('اسم الجزء')
                ->required()

                ->maxLength(255),

            Repeater::make('children')
                ->columnSpanFull()
                ->label('المحتوى (صفحات / سور)')
                ->schema([
                    TextInput::make('label')
                        ->label('الاسم')
                        ->required()
                        ->placeholder('صفحة 10 أو النبأ'),

                    TextInput::make('from_page')
                        ->label('من صفحة')
                        ->numeric()
                        ->integer()
                        ->required(),

                    TextInput::make('to_page')
                        ->label('إلى صفحة')
                        ->numeric()
                        ->integer()
                        ->required(),

                    TextInput::make('points_multiplier')
                        ->label('مضاعف النقاط')
                        ->numeric()
                        ->default(1.0)
                        ->minValue(0)
                        ->maxValue(10)
                        ->required()
                        ->helperText('1.0 = نقاط عادية, 0.5 = نصف النقاط'),
                ])
                ->columns(4)
                ->addActionLabel('إضافة عنصر')
                ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                ->defaultItems(0)
                ->reorderable()
                ->collapsible(),
        ]);
    }
}
