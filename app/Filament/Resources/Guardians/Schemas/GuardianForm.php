<?php

namespace App\Filament\Resources\Guardians\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GuardianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('الاسم')
                    ->required(),
                TextInput::make('phone')
                    ->label('رقم الهاتف')
                    ->tel()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label('كلمة المرور')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),
            ]);
    }
}
