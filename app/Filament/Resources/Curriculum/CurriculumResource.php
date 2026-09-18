<?php

namespace App\Filament\Resources\Curriculum;

use App\Filament\Resources\Curriculum\Pages\EditCurriculum;
use App\Filament\Resources\Curriculum\Pages\ListCurriculum;
use App\Filament\Resources\Curriculum\Schemas\CurriculumForm;
use App\Filament\Resources\Curriculum\Tables\CurriculumTable;
use App\Models\Curriculum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CurriculumResource extends Resource
{
    protected static ?string $model = Curriculum::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return 'جزء';
    }

    public static function getPluralModelLabel(): string
    {
        return 'المنهج (الأجزاء)';
    }

    public static function getNavigationLabel(): string
    {
        return 'منهج القرآن';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'إعدادات';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function form(Schema $schema): Schema
    {
        return CurriculumForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CurriculumTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCurriculum::route('/'),
            'edit' => EditCurriculum::route('/{record}/edit'),
        ];
    }
}
