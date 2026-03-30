<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('tracker')
                ->label('متابعة السور')
                ->icon('heroicon-o-book-open')
                ->color('info')
                ->url(fn () => \App\Filament\Resources\Students\Pages\StudentSuraTracker::getUrl(['record' => $this->record])),
            // ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
