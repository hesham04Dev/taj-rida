<?php

namespace App\Filament\Resources\Notifications\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NotificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('teacher_id')
                    ->relationship('teacher', 'name')
                    ->required(),
                Select::make('student_id')
                    ->relationship('student', 'name')
                    ->default(null),
                TextInput::make('title')
                    ->required(),
                Textarea::make('body')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
