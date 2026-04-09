<?php

// namespace App\Filament\Resources\Students\RelationManagers;

// use Filament\Forms;
// use Filament\Forms\Form;
// use Filament\Resources\RelationManagers\RelationManager;
// use Filament\Tables;
// use Filament\Tables\Table;
// use Filament\Schemas\Schema;
// use Filament\Actions\CreateAction;
// use Filament\Actions\EditAction;
// use Filament\Actions\DeleteAction;
// use Filament\Actions\BulkActionGroup;
// use Filament\Actions\DeleteBulkAction;
// class PageLogsRelationManager extends RelationManager
// {
//     protected static string $relationship = 'pageLogs';
//     protected static ?string $title = 'سجل الصفحات';

//     public function form(Schema $schema): Schema
//     {
//         return $schema
//             ->components([
//                 Forms\Components\Select::make('type')
//                     ->label('النوع')
//                     ->options([
//                         'recitation' => 'تسميع',
//                         'revision' => 'مراجعة',
//                     ])
//                     ->required(),
//                 Forms\Components\TextInput::make('count')
//                     ->label('عدد الصفحات')
//                     ->numeric()
//                     ->step('0.5')
//                     ->required(),
//                 Forms\Components\DatePicker::make('date')
//                     ->label('التاريخ')
//                     ->default(now())
//                     ->required(),
//             ]);
//     }

//     public function table(Table $table): Table
//     {
//         return $table
//             ->recordTitleAttribute('id')
//             ->columns([
//                 Tables\Columns\TextColumn::make('type')->label('النوع')->formatStateUsing(fn ($state) => $state === 'recitation' ? 'تسميع' : 'مراجعة')->badge()->color(fn ($state) => $state === 'recitation' ? 'success' : 'info'),
//                 Tables\Columns\TextColumn::make('count')->label('عدد الصفحات'),
//                 Tables\Columns\TextColumn::make('date')->label('التاريخ')->date(),
//             ])
//             ->filters([])
//             ->headerActions([
//                 CreateAction::make()->label('إضافة سجل'),
//             ])
//             ->recordActions([
//                 EditAction::make()->label('تعديل'),
//                 DeleteAction::make()->label('حذف'),
//             ])
//             ->bulkActions([
//                 BulkActionGroup::make([
//                     DeleteBulkAction::make()->label('حذف'),
//                 ]),
//             ])
//             ->defaultSort('date', 'desc');
//     }
// }
