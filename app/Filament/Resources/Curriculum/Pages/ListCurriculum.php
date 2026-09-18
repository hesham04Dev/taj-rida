<?php

namespace App\Filament\Resources\Curriculum\Pages;

use App\Filament\Resources\Curriculum\CurriculumResource;
use Filament\Resources\Pages\ListRecords;

class ListCurriculum extends ListRecords
{
    protected static string $resource = CurriculumResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
