<?php

namespace App\Filament\Resources\Notifications;

use App\Filament\Resources\Notifications\Pages\CreateNotification;
use App\Filament\Resources\Notifications\Pages\ListNotifications;
use App\Models\Notification;
use App\Models\Student;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBell;

    public static function getModelLabel(): string
    {
        return 'إشعار';
    }

    public static function getPluralModelLabel(): string
    {
        return 'الإشعارات';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'التواصل';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('student_id')
                ->label('الطالب (اتركه فارغاً لإرسال لجميع أولياء الأمور)')
                ->options(
                    Student::query()
                        ->where('teacher_id', Auth::id())
                        ->pluck('name', 'id')
                )
                ->searchable()
                ->nullable()
                ->placeholder('إرسال لجميع أولياء أمور الطلاب'),

            TextInput::make('title')
                ->label('عنوان الإشعار')
                ->required()
                ->maxLength(255),

            Textarea::make('body')
                ->label('نص الإشعار')
                ->required()
                ->rows(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('student.name')
                    ->label('الطالب')
                    ->default('جميع الطلاب')
                    ->badge()
                    ->color('info'),

                TextColumn::make('reads_count')
                    ->label('عدد القراءات')
                    ->counts('reads')
                    ->badge()
                    ->color('success'),

                TextColumn::make('created_at')
                    ->label('أُرسل في')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotifications::route('/'),
            'create' => CreateNotification::route('/create'),
        ];
    }
}
