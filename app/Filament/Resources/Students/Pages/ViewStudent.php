<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStudent extends ViewRecord
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
            EditAction::make(),
        ];
    }
}
