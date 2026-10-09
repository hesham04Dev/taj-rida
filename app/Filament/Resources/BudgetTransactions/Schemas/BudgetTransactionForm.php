<?php

namespace App\Filament\Resources\BudgetTransactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;

class BudgetTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ToggleButtons::make('type')
                    ->label('نوع الحركة')
                    ->options([
                        'credit' => 'سلف',
                        'return' => 'سداد سلف',
                        'gift' => 'تبرع (صدقة)',
                        'expense' => 'مصروفات',
                    ])
                    ->inline()
                    ->required()
                    ->live(),
                Select::make('creditor_id')
                    ->label('الداعم / الدائن')
                    ->relationship('creditor', 'name')
                    ->searchable()
                    ->preload()
                    ->required(fn ($get): bool => in_array($get('type'), ['credit', 'return']))
                    ->visible(fn ($get): bool => in_array($get('type'), ['credit', 'return']))
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->maxLength(255),
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),
                TextInput::make('amount')
                    ->label('المبلغ')
                    ->numeric()
                    ->required(),
                DatePicker::make('date')
                    ->label('التاريخ')
                    ->required()
                    ->default(now()),
                Textarea::make('description')
                    ->label('الوصف')
                    ->columnSpanFull(),
            ]);
    }
}
