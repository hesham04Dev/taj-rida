<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\Student;
use App\Models\Sura;
use App\Models\Recitation;
use App\Models\Revision;
use Filament\Resources\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Select;
// use Filament\Forms\Components\Grid;
use Filament\Schemas\Components\Grid;
use Carbon\Carbon;

class StudentSuraTracker extends Page implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected static string $resource = StudentResource::class;

    protected  string $view = 'filament.resources.students.pages.student-sura-tracker';

    public Student $record;

    public function mount(Student $record): void
    {
        $this->record = $record;
    }

    public function getTitle(): string
    {
        return 'متابعة السور: ' . $this->record->name;
    }

    public function getSurasProperty()
    {
        $suras = Sura::orderBy('id', 'asc')->get();
        
        $recitations = Recitation::where('student_id', $this->record->id)->get()->groupBy('sura_id');
        $revisions = Revision::where('student_id', $this->record->id)->get()->groupBy('sura_id');

        return $suras->map(function($sura) use ($recitations, $revisions) {
            $suraRecitations = $recitations->get($sura->id, collect());
            $suraRevisions = $revisions->get($sura->id, collect());

            $latestRecitation = $suraRecitations->sortByDesc('date')->first();
            $latestRevision = $suraRevisions->sortByDesc('date')->first();

            $status = 'gray'; // not memorized
            
            if ($latestRecitation) {
                if ($latestRecitation->grade === 'ممتاز') {
                    $status = 'light_blue';
                } elseif ($latestRecitation->grade === 'جيد جدا' || $latestRecitation->grade === 'جيد') {
                    $status = 'blue';
                } else {
                    $status = 'yellow';
                }
            }

            if ($latestRevision) {
                if ($latestRevision->grade === 'ممتاز') {
                    $status = 'lime_green'; // overrides recitation!
                } elseif ($latestRevision->grade === 'جيد جدا' || $latestRevision->grade === 'جيد') {
                    $status = 'dark_green';
                }
            }

            $sura->status_color = $status;
            return $sura;
        });
    }

    public function addLogAction(): Action
    {
        return Action::make('addLog')
            ->label('تحديث الإنجاز')
            ->icon('heroicon-o-plus')
            ->modalHeading(fn (array $arguments) => 'تسجيل إنجاز - سورة ' . Sura::find($arguments['sura'] ?? 1)->name)
            ->schema([
               ToggleButtons::make('type')
                    ->label('النوع')
                    ->options([
                        'recitation' => 'تسميع جديد (حفظ)',
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
                            // ->columnSpan(1)
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
            ])->fillForm(function (array $arguments): array {
            $sura = Sura::find($arguments['sura'] ?? 1);
            
            return [
                'from_aya' => 1,
                'to_aya' => $sura?->ayas_count ?? 1,
                'type' => 'recitation', 
                'grade' => 'ممتاز'
            ];
        })
            ->action(function (array $data, array $arguments) {
                $suraId = $arguments['sura'];
                if ($data['type'] === 'recitation') {
                    Recitation::create([
                        'student_id' => $this->record->id,
                        'sura_id' => $suraId,
                        'from_aya' => $data['from_aya'],
                        'to_aya' => $data['to_aya'],
                        'grade' => $data['grade'],
                        'date' => Carbon::now(),
                    ]);
                } else {
                    Revision::create([
                        'student_id' => $this->record->id,
                        'sura_id' => $suraId,
                        'from_aya' => $data['from_aya'],
                        'to_aya' => $data['to_aya'],
                        'grade' => $data['grade'],
                        'date' => Carbon::now(),
                    ]);
                   
                }
            });
    }
}
