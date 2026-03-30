<?php
namespace App\Filament\Resources\Students\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('اسم الطالب')->required(),
                Select::make('teacher_id')
                    ->relationship('teacher', 'name', fn($query) => $query->where('role', 'teacher'))
                    ->label('الأستاذ المكلّف')
                    ->required()
                    ->default(auth()->check() && auth()->user()->role === 'teacher' ? auth()->id() : null)
                    ->disabled(fn () => auth()->check() && auth()->user()->role === 'teacher')
                    ->dehydrated(),
                DatePicker::make('birthdate')->label('تاريخ الميلاد'),
                TextInput::make('points_multiplier')->label('مضاعف النقاط')->numeric()->default(1.0)->required(),
                TextInput::make('father_name')->label('اسم ولي الأمر'),
                TextInput::make('father_phone')->label('رقم هاتف ولي الأمر'),
                Textarea::make('more_details')->label('تفاصيل أخرى'),
                Textarea::make('notes')->label('ملاحظات الأستاذ'),
            ]);
    }
}
