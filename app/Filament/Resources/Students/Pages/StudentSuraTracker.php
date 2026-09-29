<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\Curriculum;
use App\Models\Memorization;
use App\Models\PageLog;
use App\Models\PointTransaction;
use App\Models\Setting;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date as FacadesDate;
use Illuminate\Support\HtmlString;

class StudentSuraTracker extends Page implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string $resource = StudentResource::class;

    protected string $view = 'filament.resources.students.pages.student-sura-tracker';

    public Student $record;

    /** @var array<int> Curriculum IDs selected for bulk action */
    public array $selectedJuz = [];

    public function mount(Student $record): void
    {
        $this->record = $record;
    }

    public function getTitle(): string
    {
        return 'متابعة الأجزاء: '.$this->record->name;
    }

    /**
     * Returns all 30 curriculum rows decorated with per-student memorization progress.
     */
    public function getCurriculumProperty(): Collection
    {
        $allJuz = Curriculum::orderBy('number')->get();

        $memorizations = Memorization::where('student_id', $this->record->id)
            ->get()
            ->keyBy('curriculum_id');

        return $allJuz->map(function (Curriculum $juz) use ($memorizations) {
            $mem = $memorizations->get($juz->id);
            $childrenCount = count($juz->children ?? []);

            $status = 'gray';
            $memorizedCount = 0;

            if ($mem) {
                // Count memorized children based on memorized_pages as an approximation
                $memorizedCount = (int) ($mem->memorized_pages ?? 0);

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

            $juz->status_color = $status;
            $juz->memorized_count = $memorizedCount;
            $juz->children_count = $childrenCount;
            $juz->memorization_percent = $childrenCount > 0
                ? min(100, round(($memorizedCount / $childrenCount) * 100))
                : 0;
            $juz->memorization_repetition = $mem?->memorization_repetition ?? 0;
            $juz->revision_repetition = $mem?->revision_repetition ?? 0;
            $juz->is_tested = $mem && ($mem->test_counts > 0 || ! empty($mem->test_grade));
            $juz->is_need_rememorisation = $mem?->is_need_rememorisation ?? false;
            $juz->is_need_revision = $mem?->is_need_revision ?? false;

            return $juz;
        });
    }

    // public function addLogAction(): Action
    // {
    //     return Action::make('addLog')
    //         ->label('تسجيل إنجاز')
    //         ->icon('heroicon-o-plus')
    //         ->modalHeading(fn (array $arguments) => 'تسجيل إنجاز - '.Curriculum::find($arguments['juz'] ?? 1)?->name)
    //         ->schema([
    //             Hidden::make('curriculum_id'),

    //             Toggle::make('is_need_rememorisation')
    //                 ->label('يحتاج لإعادة حفظ')
    //                 ->default(false)
    //                 ->live(),

    //             Toggle::make('is_need_revision')
    //                 ->label('يحتاج لمراجعة')
    //                 ->default(false)
    //                 ->live(),

    //             Toggle::make('is_no_points')
    //                 ->label('بدون نقاط')
    //                 ->default(false)
    //                 ->hidden(fn ($get) => $get('is_need_rememorisation') || $get('is_need_revision')),

    //             ToggleButtons::make('type')
    //                 ->label('النوع')
    //                 ->options([
    //                     'memorization' => 'تسميع جديد (حفظ)',
    //                     'revision' => 'مراجعة',
    //                     'test' => 'اختبار',
    //                 ])
    //                 ->inline()
    //                 ->required(fn ($get) => ! $get('is_need_rememorisation') && ! $get('is_need_revision'))
    //                 ->hidden(fn ($get) => $get('is_need_rememorisation') || $get('is_need_revision'))
    //                 ->live(),

    //             CheckboxList::make('selected_children')
    // ->label('اختر الصفحات / السور')
    // ->options(function ( \Filament\Schemas\Components\Utilities\Get  $get): array {
    //     // Retrieve the selected 'juz' value from the form state using $get
    //     $juzId = $get('juz');

    //     if (! $juzId) {
    //         return [];
    //     }

    //     $juz = Curriculum::find($juzId);
    //     if (! $juz) {
    //         return [];
    //     }

    //     return collect($juz->children)->pluck('label', 'label')->all();
    // })
    // ->columns(3)
    // ->required(fn ($get) => ! $get('is_need_rememorisation') && ! $get('is_need_revision'))
    // ->hidden(fn ($get) => $get('is_need_rememorisation') || $get('is_need_revision'))
    // ->bulkToggleable(),

    //             Grid::make(3)
    //                 ->hidden(fn ($get) => $get('is_need_rememorisation') || $get('is_need_revision'))
    //                 ->schema([
    //                     ToggleButtons::make('grade')
    //                         ->label('التقييم')
    //                         ->options([
    //                             'ممتاز' => 'ممتاز',
    //                             'جيد جدا' => 'جيد جدا',
    //                             'جيد' => 'جيد',
    //                             'مقبول' => 'مقبول',
    //                             'ضعيف' => 'ضعيف',
    //                         ])
    //                         ->inline()
    //                         ->required(fn ($get) => ! $get('is_need_rememorisation') && ! $get('is_need_revision')),
    //                 ]),

    //             Grid::make(3)
    //                 ->hidden(fn ($get) => $get('is_need_rememorisation') || $get('is_need_revision'))
    //                 ->schema([
    //                     TextInput::make('last_test_name')
    //                         ->hidden(fn ($get) => $get('type') != 'test')
    //                         ->label('اسم الاختبار')
    //                         ->nullable(),
    //                 ]),
    //         ])
    //         ->fillForm(function (array $arguments): array {
    //             return [
    //                 'type' => 'memorization',
    //                 'grade' => 'ممتاز',
    //                 'is_need_rememorisation' => false,
    //                 'is_need_revision' => false,
    //                 'selected_children' => [],
    //                 'curriculum_id' => $arguments['juz'] ?? null,
    //             ];
    //         })
    //         ->action(function (array $data, array $arguments) {
    //             $curriculumId = $arguments['juz'] ?? null;
    //             if (! $curriculumId) {
    //                 return;
    //             }

    //             $juz = Curriculum::find($curriculumId);
    //             if (! $juz) {
    //                 return;
    //             }

    //             $memorization = Memorization::firstOrNew([
    //                 'student_id' => $this->record->id,
    //                 'curriculum_id' => $curriculumId,
    //             ]);

    //             $isNeedRememorisation = $data['is_need_rememorisation'] ?? false;
    //             $isNeedRevision = $data['is_need_revision'] ?? false;

    //             $memorization->is_need_rememorisation = $isNeedRememorisation;
    //             $memorization->is_need_revision = $isNeedRevision;

    //             if ($isNeedRememorisation || $isNeedRevision) {
    //                 $memorization->save();

    //                 $label = $isNeedRememorisation ? 'إعادة الحفظ' : 'المراجعة';

    //                 Notification::make()
    //                     ->title("تم تحديد الجزء لـ{$label}")
    //                     ->warning()
    //                     ->send();

    //                 return;
    //             }

    //             // Build page range from selected children
    //             $selectedLabels = $data['selected_children'] ?? [];
    //             [$fromPage, $toPage, $selectedCount, $avgMultiplier] = $this->resolvePageRange($juz, $selectedLabels);

    //             if ($selectedCount === 0) {
    //                 Notification::make()
    //                     ->title('يرجى اختيار صفحة أو سورة واحدة على الأقل')
    //                     ->warning()
    //                     ->send();

    //                 return;
    //             }

    //             // Clear needs flags
    //             $memorization->need_from_page = null;
    //             $memorization->need_to_page = null;

    //             if ($data['type'] === 'memorization') {
    //                 $memorization->is_need_rememorisation = false;
    //                 $memorization->memorization_degree = $data['grade'];
    //                 $memorization->memorized_pages = ($memorization->memorized_pages ?? 0) + $selectedCount;
    //                 if ($memorization->memorized_pages >= count($juz->children)) {
    //                     $memorization->memorization_repetition = ($memorization->memorization_repetition ?? 0) + 1;
    //                 }
    //             } elseif ($data['type'] === 'revision') {
    //                 $memorization->is_need_revision = false;
    //                 $memorization->revision_degree = $data['grade'];
    //                 if ($selectedCount >= count($juz->children)) {
    //                     $memorization->revision_repetition = ($memorization->revision_repetition ?? 0) + 1;
    //                 }
    //             } elseif ($data['type'] === 'test') {
    //                 $memorization->test_grade = $data['grade'];
    //                 if ($selectedCount >= count($juz->children)) {
    //                     $memorization->test_counts = ($memorization->test_counts ?? 0) + 1;
    //                 }
    //             }

    //             if (! empty($data['last_test_name'])) {
    //                 $memorization->last_test_name = $data['last_test_name'];
    //             }

    //             $memorization->save();

    //             $logData = array_merge($data, [
    //                 'from_page' => $fromPage,
    //                 'to_page' => $toPage,
    //                 'avg_multiplier' => $avgMultiplier,
    //                 'selected_count' => $selectedCount,
    //             ]);

    //             $pageLog = $this->setPageLogs($logData, $memorization);
    //             $this->setPointTransation($memorization, $logData, $pageLog);
    //         });
    // }

    public function addLogAction(): Action
    {
        return Action::make('addLog')
            ->label('تسجيل إنجاز')
            ->icon('heroicon-o-plus')
            ->modalHeading(fn (array $arguments) => 'تسجيل إنجاز - '.Curriculum::find($arguments['juz'] ?? 1)?->name)
            ->schema([
                Hidden::make('curriculum_id'),

                Toggle::make('is_need_rememorisation')
                    ->label('يحتاج لإعادة حفظ')
                    ->default(false)
                    ->live(),

                Toggle::make('is_need_revision')
                    ->label('يحتاج لمراجعة')
                    ->default(false)
                    ->live(),

                Toggle::make('is_no_points')
                    ->label('بدون نقاط')
                    ->default(false)
                    ->hidden(fn ($get) => $get('is_need_rememorisation') || $get('is_need_revision')),

                ToggleButtons::make('type')
                    ->label('النوع')
                    ->options([
                        'memorization' => 'تسميع جديد (حفظ)',
                        'revision' => 'مراجعة',
                        'test' => 'اختبار',
                    ])
                    ->inline()
                    ->required(fn ($get) => ! $get('is_need_rememorisation') && ! $get('is_need_revision'))
                    ->hidden(fn ($get) => $get('is_need_rememorisation') || $get('is_need_revision'))
                    ->live(),

                ToggleButtons::make('selected_children')
                    ->label('اختر الصفحات / السور')
                    ->options(function (Get $get): array {
                        $curriculumId = $get('curriculum_id');

                        if (! $curriculumId) {
                            return [];
                        }

                        $juz = Curriculum::find($curriculumId);
                        if (! $juz || empty($juz->children)) {
                            return [];
                        }

                        $pageLogs = PageLog::where('student_id', $this->record->id)
                            ->where('curriculum_id', $curriculumId)
                            ->whereIn('type', ['recitation', 'revision'])
                            ->orderBy('date', 'desc')
                            ->orderBy('id', 'desc')
                            ->get();
                            // dd($pageLogs);
                        $options = [];
                        foreach ($juz->children as $child) {
                            $latestLog = null;
                            foreach ($pageLogs as $log) {
                                if (in_array($child['label'], $log->children ?? [])) {
                                    
                                    $latestLog = $log;
                                    break;
                                }
                            }

                            if ($latestLog) {
                                $colorClass = 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200';

                                if ($latestLog->type === 'revision') {
                                    if ($latestLog->grade === 'ممتاز') {
                                        $colorClass = '#00ff00';
                                    } elseif (in_array($latestLog->grade, ['جيد جدا', 'جيد'])) {
                                        $colorClass = '#80ef80';
                                    }
                                } else {
                                    if ($latestLog->grade === 'ممتاز') {
                                        $colorClass = '#38bdf8';
                                    } elseif (in_array($latestLog->grade, ['جيد جدا', 'جيد'])) {
                                        $colorClass = '#6366f1';
                                    } elseif ($latestLog->grade) {
                                        $colorClass = '#fbbf24';
                                    }
                                }

                                $gradeLabel = $latestLog->grade ?? 'تم';

                                // $options[$child['label']] = new HtmlString(
                                //     '<div class="'.$colorClass.'" style="padding: 0.25rem 0.5rem; border-radius: 0.375rem; width: 100%; height: 100%; font-weight: 500;">'.$child['label'].' <span class="text-[0.65rem] opacity-80 ml-1">('.$gradeLabel.')</span></div>'
                                // );
                                $options[$child['label']] = new HtmlString('
    <div class="flex items-center gap-2">
        
        <span class="dot" style=" display:inline-block;background-color: ' . e($colorClass) . ';"></span>
        <span>' . e($child['label']) . '</span>
    </div>
');

                                //  $options[$child['label']] = '<div>'.$child['label'].$colorClass.'</div>';
                            } else {
                                $options[$child['label']] = $child['label'] . "dd";
                            }
                        }

                        return $options;
                    })
                    ->multiple() // Allows selecting multiple items
                    ->inline()
                    ->required(),

                Grid::make(3)
                    ->hidden(fn ($get) => $get('is_need_rememorisation') || $get('is_need_revision'))
                    ->schema([
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
                            ->required(fn ($get) => ! $get('is_need_rememorisation') && ! $get('is_need_revision')),
                    ]),

                Grid::make(3)
                    ->hidden(fn ($get) => $get('is_need_rememorisation') || $get('is_need_revision'))
                    ->schema([
                        TextInput::make('last_test_name')
                            ->hidden(fn ($get) => $get('type') != 'test')
                            ->label('اسم الاختبار')
                            ->nullable(),
                    ]),
            ])
            ->fillForm(function (array $arguments): array {
                return [
                    'type' => 'memorization',
                    'grade' => 'ممتاز',
                    'is_need_rememorisation' => false,
                    'is_need_revision' => false,
                    'selected_children' => [],
                    'curriculum_id' => $arguments['juz'] ?? null,
                ];
            })
            ->action(function (array $data, array $arguments) {
                $curriculumId = $data['curriculum_id'] ?? $arguments['juz'] ?? null;
                if (! $curriculumId) {
                    return;
                }

                $juz = Curriculum::find($curriculumId);
                if (! $juz) {
                    return;
                }

                $memorization = Memorization::firstOrNew([
                    'student_id' => $this->record->id,
                    'curriculum_id' => $curriculumId,
                ]);

                $isNeedRememorisation = $data['is_need_rememorisation'] ?? false;
                $isNeedRevision = $data['is_need_revision'] ?? false;
                
                // Build page info from selected children
                $selectedLabels = $data['selected_children'] ?? [];
                [$selectedCount, $totalPagesEquivalent, $selectedLabels] = $this->resolvePageInfo($juz, $selectedLabels);

                if ($selectedCount === 0) {
                    Notification::make()
                        ->title('يرجى اختيار صفحة أو سورة واحدة على الأقل')
                        ->warning()
                        ->send();

                    return;
                }

                if ($isNeedRememorisation || $isNeedRevision) {
                    if ($isNeedRememorisation) {
                        $memorization->is_need_rememorisation = true;
                        $memorization->needs_rememorisation_children = array_values(array_unique(array_merge($memorization->needs_rememorisation_children ?? [], $selectedLabels)));
                    } else {
                        $memorization->is_need_revision = true;
                        $memorization->needs_revision_children = array_values(array_unique(array_merge($memorization->needs_revision_children ?? [], $selectedLabels)));
                    }

                    $memorization->save();

                    $label = $isNeedRememorisation ? 'إعادة الحفظ' : 'المراجعة';

                    Notification::make()
                        ->title("تم تحديد الصفحات لـ{$label}")
                        ->warning()
                        ->send();

                    return;
                }

                // Clear needs flags
                // (no longer needed to clear need_from_page / need_to_page)

                if ($data['type'] === 'memorization') {
                    $rememorizeChildren = $memorization->needs_rememorisation_children ?? [];
                    $rememorizeChildren = array_diff($rememorizeChildren, $selectedLabels);
                    $memorization->needs_rememorisation_children = array_values($rememorizeChildren);
                    if (empty($rememorizeChildren)) {
                        $memorization->is_need_rememorisation = false;
                    }
                    
                    $memorization->memorization_degree = $data['grade'];
                    $memorization->memorized_pages = ($memorization->memorized_pages ?? 0) + $selectedCount;
                    if ($memorization->memorized_pages >= count($juz->children ?? [])) {
                        $memorization->memorization_repetition = ($memorization->memorization_repetition ?? 0) + 1;
                    }
                } elseif ($data['type'] === 'revision') {
                    $revisionChildren = $memorization->needs_revision_children ?? [];
                    $revisionChildren = array_diff($revisionChildren, $selectedLabels);
                    $memorization->needs_revision_children = array_values($revisionChildren);
                    if (empty($revisionChildren)) {
                        $memorization->is_need_revision = false;
                    }
                    
                    $memorization->revision_degree = $data['grade'];
                    if ($selectedCount >= count($juz->children ?? [])) {
                        $memorization->revision_repetition = ($memorization->revision_repetition ?? 0) + 1;
                    }
                } elseif ($data['type'] === 'test') {
                    $memorization->test_grade = $data['grade'];
                    if ($selectedCount >= count($juz->children ?? [])) {
                        $memorization->test_counts = ($memorization->test_counts ?? 0) + 1;
                    }
                }

                if (! empty($data['last_test_name'])) {
                    $memorization->last_test_name = $data['last_test_name'];
                }

                $memorization->save();

                $logData = array_merge($data, [
                    'children' => $selectedLabels,
                    'total_pages_equivalent' => $totalPagesEquivalent,
                    'selected_count' => $selectedCount,
                ]);

                $pageLog = $this->setPageLogs($logData, $memorization);
                $this->setPointTransation($memorization, $logData, $pageLog);
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
                        ->default(false)
                        ->live(),

                    Toggle::make('is_need_revision')
                        ->label('يحتاج لمراجعة')
                        ->default(false)
                        ->live(),

                    Toggle::make('is_no_points')
                        ->label('بدون نقاط')
                        ->default(false)
                        ->hidden(fn ($get) => $get('is_need_rememorisation') || $get('is_need_revision')),

                    TextEntry::make('selected_juz_names')
                        ->label(function () {
                            $names = Curriculum::whereIn('id', $this->selectedJuz)
                                ->orderBy('number')
                                ->pluck('name')
                                ->join('، ');

                            return $names ?: 'لم يتم تحديد أي جزء';
                        }),

                    ToggleButtons::make('type')
                        ->label('النوع')
                        ->options([
                            'memorization' => 'تسميع جديد (حفظ)',
                            'revision' => 'مراجعة',
                            'test' => 'اختبار',
                        ])
                        ->inline()
                        ->required(fn ($get) => ! $get('is_need_rememorisation') && ! $get('is_need_revision'))
                        ->hidden(fn ($get) => $get('is_need_rememorisation') || $get('is_need_revision'))
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
                        ->required(fn ($get) => ! $get('is_need_rememorisation') && ! $get('is_need_revision'))
                        ->hidden(fn ($get) => $get('is_need_rememorisation') || $get('is_need_revision')),

                    Grid::make(3)
                        ->hidden(fn ($get) => $get('is_need_rememorisation') || $get('is_need_revision'))
                        ->schema([
                            TextInput::make('last_test_name')
                                ->hidden(fn ($get) => $get('type') != 'test')
                                ->label('اسم الاختبار')
                                ->nullable(),
                        ]),
                ])
                ->action(function (array $data) {
                    if (empty($this->selectedJuz)) {
                        Notification::make()
                            ->title('يرجى تحديد جزء واحد على الأقل')
                            ->warning()
                            ->send();

                        return;
                    }

                    $isNeedRememorisation = $data['is_need_rememorisation'] ?? false;
                    $isNeedRevision = $data['is_need_revision'] ?? false;
                    $isFlagOnly = $isNeedRememorisation || $isNeedRevision;

                    foreach ($this->selectedJuz as $curriculumId) {
                        $juz = Curriculum::find($curriculumId);
                        if (! $juz) {
                            continue;
                        }

                        $memorization = Memorization::firstOrNew([
                            'student_id' => $this->record->id,
                            'curriculum_id' => $curriculumId,
                        ]);

                        if ($isNeedRememorisation) {
                            $memorization->is_need_rememorisation = true;
                            $memorization->needs_rememorisation_children = collect($juz->children ?? [])->pluck('label')->toArray();
                        } else {
                            $memorization->is_need_revision = true;
                            $memorization->needs_revision_children = collect($juz->children ?? [])->pluck('label')->toArray();
                        }

                        if ($isFlagOnly) {
                            $memorization->save();

                            continue;
                        }

                        // Full juz: all children selected
                        $allChildren = $juz->children ?? [];
                        $childrenCount = count($allChildren);
                        $selectedLabels = collect($allChildren)->pluck('label')->toArray();
                        $totalPagesEquivalent = collect($allChildren)->sum('pages_equivalent');

                        $memorization->memorized_pages = $childrenCount;

                        if ($data['type'] === 'memorization') {
                            $memorization->needs_rememorisation_children = [];
                            $memorization->is_need_rememorisation = false;
                            $memorization->memorization_degree = $data['grade'];
                            $memorization->memorization_repetition = ($memorization->memorization_repetition ?? 0) + 1;
                        } elseif ($data['type'] === 'revision') {
                            $memorization->needs_revision_children = [];
                            $memorization->is_need_revision = false;
                            $memorization->revision_degree = $data['grade'];
                            $memorization->revision_repetition = ($memorization->revision_repetition ?? 0) + 1;
                        } elseif ($data['type'] === 'test') {
                            $memorization->test_grade = $data['grade'];
                            $memorization->test_counts = ($memorization->test_counts ?? 0) + 1;
                        }

                        if (! empty($data['last_test_name'])) {
                            $memorization->last_test_name = $data['last_test_name'];
                        }

                        $memorization->save();

                        $logData = array_merge($data, [
                            'children' => $selectedLabels,
                            'total_pages_equivalent' => $totalPagesEquivalent,
                            'selected_count' => $childrenCount,
                        ]);

                        $pageLog = $this->setPageLogs($logData, $memorization);
                        $this->setPointTransation($memorization, $logData, $pageLog);
                    }

                    $this->selectedJuz = [];

                    $label = $isNeedRememorisation ? 'إعادة الحفظ' : ($isNeedRevision ? 'المراجعة' : null);

                    Notification::make()
                        ->title($label ? "تم تحديد الأجزاء لـ{$label}" : 'تم تسجيل الإنجاز بنجاح')
                        ->when($isFlagOnly, fn ($n) => $n->warning())
                        ->when(! $isFlagOnly, fn ($n) => $n->success())
                        ->send();
                }),
        ];
    }

    /**
     * Resolves the page info and count from a list of selected child labels.
     *
     * @param  array<string>  $selectedLabels
     * @return array{int, float, array<string>} [selectedCount, totalPagesEquivalent, selectedLabels]
     */
    protected function resolvePageInfo(Curriculum $juz, array $selectedLabels): array
    {
        if (empty($selectedLabels)) {
            return [0, 0.0, []];
        }

        $selected = collect($juz->children)->whereIn('label', $selectedLabels);

        if ($selected->isEmpty()) {
            return [0, 0.0, []];
        }

        $selectedCount = $selected->count();
        $totalPagesEquivalent = (float) $selected->sum('pages_equivalent');

        return [$selectedCount, $totalPagesEquivalent, $selectedLabels];
    }

    protected function setPointTransation(Memorization $memorization, array $data, ?PageLog $pageLog = null): void
    {
        $isNoPoints = $data['is_no_points'] ?? false;

        $type = $data['type'] === 'memorization' ? 'recitation' : $data['type'];
        $settingKey = $type.'_points_per_page';
        $setting = Setting::where('key', $settingKey)->first();
        $pointsPerPage = $setting ? (int) $setting->value : 0;

        $grade = $data['grade'] ?? 'ممتاز';
        $gradeSettingKey = match ($grade) {
            'ممتاز' => 'grade_excellent_percent',
            'جيد جدا' => 'grade_very_good_percent',
            'جيد' => 'grade_good_percent',
            'مقبول' => 'grade_acceptable_percent',
            'ضعيف' => 'grade_weak_percent',
            default => 'grade_excellent_percent',
        };
        $gradePercentSetting = Setting::where('key', $gradeSettingKey)->first();
        $gradePercent = $gradePercentSetting ? (int) $gradePercentSetting->value : match ($grade) {
            'ممتاز' => 100,
            'جيد جدا' => 75,
            'جيد' => 50,
            'مقبول' => 25,
            'ضعيف' => 0,
            default => 100,
        };

        // Repetition check
        $isRepetition = false;
        $repetitionPercent = 100;
        if ($data['type'] === 'memorization' && (($memorization->memorization_repetition ?? 0) > 1)) {
            $isRepetition = true;
            $rePercentSetting = Setting::where('key', 're_recitation_percent')->first();
            $repetitionPercent = $rePercentSetting ? (int) $rePercentSetting->value : 50;
        } elseif ($data['type'] === 'revision' && (($memorization->revision_repetition ?? 0) > 1)) {
            $isRepetition = true;
            $rePercentSetting = Setting::where('key', 're_revision_percent')->first();
            $repetitionPercent = $rePercentSetting ? (int) $rePercentSetting->value : 50;
        }

        // Per-child pages_equivalent sum
        $totalPagesEquivalent = (float) ($data['total_pages_equivalent'] ?? 0.0);

        // Student personal multiplier
        $studentMultiplier = $memorization->student->points_multiplier ?? 1.0;

        $selectedCount = (int) ($data['selected_count'] ?? 0);
        $totalPoints = 0;

        $juz = $memorization->curriculum;
        $reason = __($data['type']).' '.$juz->name;

        $children = $data['children'] ?? [];
        if (!empty($children)) {
            $juzChildrenCount = count($juz->children ?? []);
            if (count($children) < $juzChildrenCount) {
                $reason .= ' ('.implode('، ', $children).')';
            }
        }

        if ($isRepetition) {
            $reason .= ' (إعادة)';
        }
        if ($data['type'] === 'test' && ! empty($memorization->last_test_name)) {
            $reason .= ' ('.$memorization->last_test_name.')';
        }

        if (! $isNoPoints && $selectedCount > 0) {
            $basePoints = $totalPagesEquivalent * $pointsPerPage * $studentMultiplier;
            $gradeScaled = $basePoints * ($gradePercent / 100.0);
            $totalPoints = (int) round($gradeScaled * ($repetitionPercent / 100.0));
        } elseif ($isNoPoints) {
            $reason .= ' (بدون نقاط)';
        }

        PointTransaction::create([
            'student_id' => $memorization->student_id,
            'teacher_id' => auth()->id() ?? 1,
            'amount' => $totalPoints,
            'reason' => $reason,
            'curriculum_id' => $memorization->curriculum_id,
            'page_log_id' => $pageLog?->id,
        ]);
    }

    protected function setPageLogs(array $data, Memorization $memorization): PageLog
    {
        $pageLog = new PageLog([
            'student_id' => $memorization->student_id,
            'curriculum_id' => $memorization->curriculum_id,
            'type' => $data['type'] === 'memorization' ? 'recitation' : $data['type'],
            'grade' => $data['grade'] ?? null,
            'children' => $data['children'] ?? [],
            'count' => $data['selected_count'] ?? count($data['children'] ?? []),
            'date' => FacadesDate::now()->format('Y-m-d'),
        ]);

        $pageLog->saveQuietly();

        return $pageLog;
    }
}
