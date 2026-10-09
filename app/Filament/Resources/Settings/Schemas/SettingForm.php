<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')->label('المفتاح')->required()->unique(ignoreRecord: true),
                TextInput::make('value')
                    ->label('القيمة')
                    ->required()
                    ->rules([
                        fn ($get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                            if ($get('key') === 'expected_exchange_rate') {
                                if (! is_numeric($value) || (float) $value <= 0) {
                                    $fail('يجب أن يكون سعر الصرف المتوقع رقماً موجباً.');
                                }
                            }

                            if ($get('key') === 'gifts_balance') {
                                if (! is_numeric($value) || (float) $value < 0) {
                                    $fail('يجب أن يكون رصيد الهدايا رقماً لا يقل عن الصفر.');
                                }
                            }
                        },
                    ]),
            ]);
    }
}
