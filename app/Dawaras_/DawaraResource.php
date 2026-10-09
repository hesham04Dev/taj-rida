<?php

namespace App\Filament\Resources\Dawaras;

use App\Actions\EndDawaraAction;
use App\Filament\Resources\Dawaras\Pages\ManageDawaras;
use App\Models\Dawara;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class DawaraResource extends Resource
{
    protected static ?string $model = Dawara::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Dawaras (Terms)';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'completed' => 'Completed',
                    ])
                    ->required()
                    ->default('active'),
                Forms\Components\DatePicker::make('start_date'),
                Forms\Components\DatePicker::make('end_date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'gray' => 'completed',
                    ]),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('end_current_dawara')
                    ->label('End Current Dawara & Start New')
                    ->color('danger')
                    ->icon('heroicon-o-power')
                    ->requiresConfirmation()
                    ->modalHeading('End Current Dawara')
                    ->modalDescription('Are you sure you want to end the current Dawara? This will tally all points, mark it as completed, and immediately start a new Dawara. This cannot be easily undone.')
                    ->modalSubmitActionLabel('Yes, End Term')
                    ->action(function (EndDawaraAction $action) {
                        try {
                            $newDawara = $action->execute();
                            Notification::make()
                                ->title('Success')
                                ->body("The term has been ended. The new term {$newDawara->name} is now active.")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDawaras::route('/'),
        ];
    }
}
