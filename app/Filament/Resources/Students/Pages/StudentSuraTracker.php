<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\Memorization;
use App\Models\Student;
use App\Models\Sura;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Grid;

class StudentSuraTracker extends Page implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string $resource = StudentResource::class;

    protected string $view = 'filament.resources.students.pages.student-sura-tracker';

    public Student $record;

    public array $selectedSuras = [];

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
            $sura->is_tested = $mem && ($mem->test_counts > 0 || !empty($mem->test_grade));

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
                Toggle::make('is_need_rememorisation')
                        ->label('يحتاج لإعادة حفظ')
                        ->default(false),
                ToggleButtons::make('type')
                    ->label('النوع')
                    ->options([
                        'memorization' => 'تسميع جديد (حفظ)',
                        'revision' => 'مراجعة',
                        'test' => 'اختبار',
                    ])
                    ->inline()
                    ->required()->live(),
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
                Grid::make(3)->schema([
                    TextInput::make('last_test_name')->hidden(fn ($get)=> $get("type") != "test")->label('اسم الاختبار')->nullable(),
                ]),
                
                   
                
            ])
            ->fillForm(function (array $arguments): array {
                return [
                    'type' => 'memorization',
                    'grade' => 'ممتاز',
                    'is_need_rememorisation' => false,
                    'update_date' => now(),
                ];
            })
            ->action(function (array $data, array $arguments) {
                $suraId = $arguments['sura'] ?? null;
                if (! $suraId) {
                    return;
                }

                $sura = Sura::find($suraId);
                if (! $sura) {
                    return;
                }

                $memorization = Memorization::firstOrNew([
                    'student_id' => $this->record->id,
                    'sura_id' => $suraId,
                ]);

                $memorization->memorized_ayas = $sura->ayas_count;

                if ($data['type'] === 'memorization') {
                    $memorization->memorization_degree = $data['grade'];
                    $memorization->memorization_repetition = ($memorization->memorization_repetition ?? 0) + 1;
                } elseif ($data['type'] === 'revision') {
                    $memorization->revision_degree = $data['grade'];
                    $memorization->revision_repetition = ($memorization->revision_repetition ?? 0) + 1;
                } elseif ($data['type'] === 'test') {
                    $memorization->test_grade = $data['grade'];
                    $memorization->test_counts = ($memorization->test_counts ?? 0) + 1;
                }

                if (! empty($data['last_test_name'])) {
                    $memorization->last_test_name = $data['last_test_name'];
                }
                if (! empty($data['update_date'])) {
                    $memorization->update_date = $data['update_date'];
                }

                $memorization->is_need_rememorisation = $data['is_need_rememorisation'] ?? false;

                $memorization->save();
            });
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('return_to_student_page')
                ->label('العودة لصفحة الطالب')
                ->icon('heroicon-o-arrow-left')
                ->url(route('filament.admin.resources.students.edit', $this->record->id)),
            Action::make('bulkAddLog')
                ->label('تسجيل إنجاز متعدد (للمحدد)')
                
                ->form([
                    Toggle::make('is_need_rememorisation')
                            ->label('يحتاج لإعادة حفظ')
                            ->default(false),
                    TextEntry::make('selected_suras_names')
                        ->label('السور المحددة'),
                    TextEntry::make("selected_suras_names_content")
                        ->label(function () {
                            $names = Sura::whereIn('id', $this->selectedSuras)->pluck('name')->join('، ');
                            return $names ?: 'لم يتم تحديد أي سورة';
                        }),
                    ToggleButtons::make('type')
                        ->label('النوع')
                        ->options([
                            'memorization' => 'تسميع جديد (حفظ)',
                            'revision' => 'مراجعة',
                            'test' => 'اختبار',
                        ])
                        ->inline()
                        ->required()
                        ->live(),
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
                    Grid::make(3)->schema([
                        TextInput::make('last_test_name')->hidden(fn ($get)=> $get("type") != "test")->label('اسم الاختبار')->nullable(),
                    ]),
                    
                        
                    
                ])
                ->action(function (array $data) {
                    if (empty($this->selectedSuras)) {
                        Notification::make()
                            ->title('يرجى تحديد سورة واحدة على الأقل')
                            ->warning()
                            ->send();

                        return;
                    }

                    foreach ($this->selectedSuras as $suraId) {
                        $sura = Sura::find($suraId);
                        if ($sura) {
                            $memorization = Memorization::firstOrNew([
                                'student_id' => $this->record->id,
                                'sura_id' => $suraId,
                            ]);

                            $memorization->memorized_ayas = $sura->ayas_count; // always full

                            if ($data['type'] === 'memorization') {
                                $memorization->memorization_degree = $data['grade'];
                                $memorization->memorization_repetition = ($memorization->memorization_repetition ?? 0) + 1;
                            } elseif ($data['type'] === 'revision') {
                                $memorization->revision_degree = $data['grade'];
                                $memorization->revision_repetition = ($memorization->revision_repetition ?? 0) + 1;
                            } elseif ($data['type'] === 'test') {
                                $memorization->test_grade = $data['grade'];
                                $memorization->test_counts = ($memorization->test_counts ?? 0) + 1;
                            }

                            if (! empty($data['last_test_name'])) {
                                $memorization->last_test_name = $data['last_test_name'];
                            }
                            if (! empty($data['update_date'])) {
                                $memorization->update_date = $data['update_date'];
                            }

                            $memorization->is_need_rememorisation = $data['is_need_rememorisation'] ?? false;

                            $memorization->save();
                        }
                    }

                    $this->selectedSuras = [];

                    Notification::make()
                        ->title('تم تسجيل الإنجاز بنجاح')
                        ->success()
                        ->send();
                }),
        ];
    }
}
