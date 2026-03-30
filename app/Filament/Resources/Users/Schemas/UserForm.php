<?php
namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('الاسم')->required(),
                TextInput::make('email')->label('البريد الإلكتروني')->email()->required(),
                TextInput::make('phone')->label('رقم الهاتف'),
                Select::make('role')->label('الصلاحية')->options([
                    'admin' => 'مدير (Admin)',
                    'teacher' => 'أستاذ (Teacher)',
                ])->required(),
                TextInput::make('password')->label('كلمة المرور')->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),
            ]);
    }
}
