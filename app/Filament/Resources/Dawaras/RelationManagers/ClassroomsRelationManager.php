<?php

namespace App\Filament\Resources\Dawaras\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClassroomsRelationManager extends RelationManager
{
    protected static string $relationship = 'classrooms';

    protected static ?string $title = 'الحلقات';

    protected static ?string $modelLabel = 'حلقة';

    protected static ?string $pluralModelLabel = 'الحلقات';

    public function form(Schema $schema): Schema
    {
        // return $schema
        //     ->components([
        //         TextInput::make('name')
        //             ->required()
        //             ->maxLength(255),
        //     ]);

        return $schema->components([
            TextInput::make('name')->label('اسم الحلقة')->required(),
            Select::make('teachers')
                ->label('الأساتذة')
                ->relationship('users', 'name', fn ($query) => $query->where('role', 'teacher'))
                ->multiple()->preload()->searchable(),
        ]);
    }

    public function table(Table $table): Table
    {
        // return $table
        //     ->recordTitleAttribute('name')
        //     ->columns([
        //         TextColumn::make('name')
        //             ->searchable(),
        //     ])
        //     ->filters([
        //         //
        //     ])

        //     ]);

        return $table
            ->recordTitleAttribute('name')
            ->modifyQueryUsing(function ($query) {
                $user = auth()->user();
                if ($user?->role === 'teacher') {
                    $query->whereHas('teachers', fn ($q) => $q->where('users.id', $user->id));
                }
            })
            ->columns([
                TextColumn::make('name')->label('اسم الحلقة')->searchable(),
                TextColumn::make('teachers.name')->label('الأساتذة')->badge(),
                TextColumn::make('students_count')->counts('students')->label('الطلاب'),
            ])
        // ->headerActions([
        //     CreateAction::make()
        //         ->visible(fn () => auth()->user()?->role !== 'teacher'),
        // ])
        // ->recordActions([
        //     EditAction::make()
        //         ->visible(fn () => auth()->user()?->role !== 'teacher'),
        //     DeleteAction::make()
        //         ->visible(fn () => auth()->user()?->role !== 'teacher'),
        // ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
