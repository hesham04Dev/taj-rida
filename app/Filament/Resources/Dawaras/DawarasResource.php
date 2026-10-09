<?php

namespace App\Filament\Resources\Dawaras;

use App\Filament\Resources\Dawaras\Pages\CreateDawaras;
use App\Filament\Resources\Dawaras\Pages\EditDawaras;
use App\Filament\Resources\Dawaras\Pages\ListDawaras;
use App\Filament\Resources\Dawaras\Schemas\DawarasForm;
use App\Filament\Resources\Dawaras\Tables\DawarasTable;
use App\Models\Dawara;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DawarasResource extends Resource
{
    protected static ?string $model = Dawara::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

      public static function getNavigationGroup(): ?string
    {
        return 'إعدادات';
    }
    public static function getModelLabel(): string
    {
        return 'دورة';
    }

    public static function getPluralModelLabel(): string
    {
        return 'الدورات';
    }

    protected static ?string $recordTitleAttribute = 'Dawaras';

    public static function form(Schema $schema): Schema
    {
        return DawarasForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DawarasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDawaras::route('/'),
            'create' => CreateDawaras::route('/create'),
            'edit' => EditDawaras::route('/{record}/edit'),
        ];
    }
}
