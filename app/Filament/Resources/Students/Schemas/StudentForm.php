<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
// use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('اسم الطالب')->required(),
                // Select::make('teacher_id')
                //     ->relationship('teacher', 'name', fn ($query) => $query->where('role', 'teacher'))
                //     ->label('الأستاذ المكلّف')
                //     ->required()
                //     ->default(auth()->check() && auth()->user()->role === 'teacher' ? auth()->id() : null)
                //     ->disabled(fn () => auth()->check() && auth()->user()->role === 'teacher')
                //     ->dehydrated(),
                Select::make('classroom_id')
                    ->label('الحلقة')
                    ->relationship('classroom', 'name', function ($query, $record) {
                        // Show classrooms that are active, plus the student's current classroom
                        // so the dropdown never appears blank on edit.
                        $query->where(function ($q) use ($record) {
                            $q->active()->orWhere('classrooms.id', $record?->classroom_id);
                        });

                        if (auth()->user()?->role === 'teacher') {
                            $query->whereHas('teachers', fn ($q) => $q->where('users.id', auth()->id()));
                        }
                    })
                    ->default(fn () => auth()->user()?->role === 'teacher'
                        ? auth()->user()->activeClassroom()?->id
                        : null)
                    ->searchable()->preload()
                    ->required(),
                DatePicker::make('birthdate')->label('تاريخ الميلاد'),
                TextInput::make('points_multiplier')->label('مضاعف النقاط')->numeric()->default(1.0)->required(),
                TextInput::make('father_name')->label('اسم ولي الأمر'),
                TextInput::make('father_phone')->label('رقم هاتف ولي الأمر'),
                Textarea::make('more_details')->label('تفاصيل أخرى'),
                Textarea::make('notes')->label('ملاحظات الأستاذ'),

                Section::make('أرقام هواتف أولياء الأمور للتواصل وبوابة المتابعة')
                    ->description('سجل هنا أرقام هواتف الآباء والأمهات أو الإخوة لربطهم بحساباتهم وتمكينهم من تسجيل الدخول والمتابعة.')
                    ->schema([
                        Repeater::make('guardians')
                            ->relationship('guardians')
                            ->schema([
                                TextInput::make('name')
                                    ->label('الاسم')
                                    ->required(),
                                TextInput::make('phone')
                                    ->label('رقم الهاتف')
                                    ->tel()
                                    ->required(),
                                Select::make('relationship')
                                    ->label('صلة القرابة')
                                    ->options([
                                        'father' => 'الأب',
                                        'mother' => 'الأم',
                                        'sister' => 'الأخت',
                                        'brother' => 'الأخ',
                                        'other' => 'غير ذلك',
                                    ])
                                    ->required(),
                            ])
                            ->columns(3)
                            ->label('قائمة أولياء الأمور')
                            ->defaultItems(0),
                    ]),
            ]);
    }
}
