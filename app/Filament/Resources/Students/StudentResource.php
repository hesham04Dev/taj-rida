<?php

namespace App\Filament\Resources\Students;

use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Filament\Resources\Students\Pages\ViewStudent;
use App\Filament\Resources\Students\Schemas\StudentForm;
use App\Filament\Resources\Students\Schemas\StudentInfolist;
use App\Filament\Resources\Students\Tables\StudentsTable;
use App\Models\Student;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use  App\Filament\Resources\Students\RelationManagers\RecitationsRelationManager;
use  App\Filament\Resources\Students\RelationManagers\RevisionsRelationManager;
use  App\Filament\Resources\Students\RelationManagers\PageLogsRelationManager;
use  App\Filament\Resources\Students\RelationManagers\AttendancesRelationManager;
use  App\Filament\Resources\Students\RelationManagers\PointTransactionsRelationManager;
use  App\Filament\Resources\Students\RelationManagers\StudentNotesRelationManager;


class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    public static function getModelLabel(): string
    {
        return "تلميذ";
    }

    public static function getPluralModelLabel(): string
    {
        return "التلاميذ";
    }
    public static function form(Schema $schema): Schema
    {
        return StudentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RecitationsRelationManager::class,
            RevisionsRelationManager::class,
            PageLogsRelationManager::class,
            AttendancesRelationManager::class,
            PointTransactionsRelationManager::class,
            StudentNotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'view' => ViewStudent::route('/{record}'),
            'edit' => EditStudent::route('/{record}/edit'),
            'tracker' => \App\Filament\Resources\Students\Pages\StudentSuraTracker::route('/{record}/tracker'),
        ];
    }
}
