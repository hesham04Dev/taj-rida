<?php
namespace App\Filament\Resources\Settings\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')->label('المفتاح')->required()->unique(ignoreRecord: true),
                TextInput::make('value')->label('القيمة')->required(),
            ]);
    }
}
