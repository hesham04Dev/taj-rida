<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\Memorization;
use App\Models\Student;
use App\Models\Sura;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Grid;

class StudentSuraTracker extends Page implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string $resource = StudentResource::class;

    protected string $view = 'filament.resources.students.pages.student-sura-tracker';

    public Student $record;

    public function mount(Student $record): void
    {
        $this->record = $record;
    }

    public function getTitle(): string
    {
        return 'متابعة السور: '.$this->record->name;
    }

    public function getSurasProperty()
    {
        $suras = Sura::orderBy('id', 'asc')->get();

        $memorizations = Memorization::where('student_id', $this->record->id)
            ->get()
            ->keyBy('sura_id');

        return $suras->map(function ($sura) use ($memorizations) {
            $mem = $memorizations->get($sura->id);

            $status = 'gray';
            $percent = 0;

            if ($mem) {
                $percent = $sura->ayas_count > 0
                    ? round(($mem->memorized_ayas / $sura->ayas_count) * 100)
                    : 0;

                if ($mem->revision_degree === 'ممتاز') {
                    $status = 'lime_green';
                } elseif (in_array($mem->revision_degree, ['جيد جدا', 'جيد'])) {
                    $status = 'dark_green';
                } elseif ($mem->memorization_degree === 'ممتاز') {
                    $status = 'light_blue';
                } elseif (in_array($mem->memorization_degree, ['جيد جدا', 'جيد'])) {
                    $status = 'blue';
                } elseif ($mem->memorization_degree) {
                    $status = 'yellow';
                }
            }

            $sura->status_color = $status;
            $sura->memorization_percent = $percent;
            $sura->memorization_repetition = $mem?->memorization_repetition ?? 0;
            $sura->revision_repetition = $mem?->revision_repetition ?? 0;

            return $sura;
        });
    }

    public function addLogAction(): Action
    {
        return Action::make('addLog')
            ->label('تحديث الإنجاز')
            ->icon('heroicon-o-plus')
            ->modalHeading(fn (array $arguments) => 'تسجيل إنجاز - سورة '.Sura::find($arguments['sura'] ?? 1)?->name)
            ->schema([
                ToggleButtons::make('type')
                    ->label('النوع')
                    ->options([
                        'memorization' => 'تسميع جديد (حفظ)',
                        'revision' => 'مراجعة',
                    ])
                    ->inline()
                    ->required(),
                Grid::make(2)
                    ->schema([
                        TextInput::make('from_aya')
                            ->label('من آية')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        TextInput::make('to_aya')
                            ->label('إلى آية')
                            ->numeric()
                            ->required(),
                    ]),
                ToggleButtons::make('grade')
                    ->label('التقييم')
                    ->options([
                        'ممتاز' => 'ممتاز',
                        'جيد جدا' => 'جيد جدا',
                        'جيد' => 'جيد',
                        'مقبول' => 'مقبول',
                        'ضعيف' => 'ضعيف',
                    ])
                    ->inline()
                    ->required(),
            ])
            ->fillForm(function (array $arguments): array {
                $sura = Sura::find($arguments['sura'] ?? 1);

                return [
                    'from_aya' => 1,
                    'to_aya' => $sura?->ayas_count ?? 1,
                    'type' => 'memorization',
                    'grade' => 'ممتاز',
                ];
            })
            ->action(function (array $data, array $arguments) {
                $suraId = $arguments['sura'];

                $memorization = Memorization::firstOrNew([
                    'student_id' => $this->record->id,
                    'sura_id' => $suraId,
                ]);

                if ($data['type'] === 'memorization') {
                    $memorization->memorized_ayas = $data['to_aya'];
                    $memorization->memorization_degree = $data['grade'];
                    $memorization->memorization_repetition = ($memorization->memorization_repetition ?? 0) + 1;
                } else {
                    $memorization->revision_degree = $data['grade'];
                    $memorization->revision_repetition = ($memorization->revision_repetition ?? 0) + 1;
                }

                $memorization->save();
            });
    }
}
