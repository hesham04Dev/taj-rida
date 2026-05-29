<?php

namespace App\Filament\Resources\Guardians;

use App\Filament\Resources\Guardians\Pages\CreateGuardian;
use App\Filament\Resources\Guardians\Pages\EditGuardian;
use App\Filament\Resources\Guardians\Pages\ListGuardians;
use App\Filament\Resources\Guardians\Schemas\GuardianForm;
use App\Filament\Resources\Guardians\Tables\GuardiansTable;
use App\Models\Guardian;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GuardianResource extends Resource
{
    protected static ?string $model = Guardian::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    public static function getModelLabel(): string
    {
        return 'ولي أمر';
    }

    public static function getPluralModelLabel(): string
    {
        return 'أولياء الأمور';
    }

    public static function form(Schema $schema): Schema
    {
        return GuardianForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GuardiansTable::configure($table);
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
            'index' => ListGuardians::route('/'),
            'create' => CreateGuardian::route('/create'),
            'edit' => EditGuardian::route('/{record}/edit'),
        ];
    }
}
