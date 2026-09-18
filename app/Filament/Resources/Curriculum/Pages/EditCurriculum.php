<?php

namespace App\Filament\Resources\Curriculum\Pages;

use App\Filament\Resources\Curriculum\CurriculumResource;
use Filament\Resources\Pages\EditRecord;

class EditCurriculum extends EditRecord
{
    protected static string $resource = CurriculumResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
