<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\Memorization;
use App\Models\PageLog;
use App\Models\PointTransaction;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Sura;
use Date;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
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
use Filament\Schemas\Components\Utilities\Set;

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
                $percent = $sura->pages_count > 0
                    ? round(($mem->memorized_pages / $sura->pages_count) * 100)
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
            $sura->is_tested = $mem && ($mem->test_counts > 0 || ! empty($mem->test_grade));
            $sura->is_need_rememorisation = $mem?->is_need_rememorisation ?? false;
            $sura->is_need_revision = $mem?->is_need_revision ?? false;
            $sura->need_from_page = $mem?->need_from_page;
            $sura->need_to_page = $mem?->need_to_page;

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
                Hidden::make('sura_id'),
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
                Grid::make(2)
                    ->schema([
                        TextInput::make('from_page')
                            ->label('من صفحة')
                            ->numeric()
                            ->required(),
                        TextInput::make('to_page')
                            ->label('إلى صفحة')
                            ->numeric()
                            ->required(),
                    ]),
                Grid::make(3)
                    ->schema([

                        Action::make('add_page')
                            ->label('+ 1 صفحة')
                            ->action(function (Get $get, Set $set) {
                                $sura = Sura::find($get('sura_id'));
                                $newToPage = $get('to_page') + 1;

                                // If the new value exceeds the Sura's end page, cycle back to the starting point
                                if ($sura && $newToPage > $sura->to_page) {
                                    $set('to_page', $sura->to_page);
                                } else {
                                    $set('to_page', $newToPage);
                                }
                            }),

                        Action::make('add_h_page')
                            ->label('+ 0.5 صفحة')
                            ->action(function (Get $get, Set $set) {
                                $sura = Sura::find($get('sura_id'));
                                $newToPage = $get('to_page') + 0.5;

                                // If the new value exceeds the Sura's end page, cycle back to the starting point
                                if ($sura && $newToPage > $sura->to_page) {
                                    $set('to_page', $sura->to_page);
                                } else {
                                    $set('to_page', $newToPage);
                                }
                            }),
                        Action::make('full_sura')
                            ->label('كامل السورة')
                            ->action(function (Get $get, Set $set) {
                                // Read directly from the hidden form field
                                $suraId = $get('sura_id');

                                if (! $suraId) {
                                    return;
                                }

                                $sura = Sura::find($suraId);

                                if ($sura) {
                                    $set('to_page', $sura->to_page);
                                }
                            }),

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
                    ->required(fn ($get) => ! $get('is_need_rememorisation') && ! $get('is_need_revision'))
                    ->hidden(fn ($get) => $get('is_need_rememorisation') || $get('is_need_revision')),
                Grid::make(3)
                    ->hidden(fn ($get) => $get('is_need_rememorisation') || $get('is_need_revision'))
                    ->schema([
                        TextInput::make('last_test_name')->hidden(fn ($get) => $get('type') != 'test')->label('اسم الاختبار')->nullable(),
                    ]),
            ])
            ->fillForm(function (array $arguments): array {
                $sura = Sura::find($arguments['sura'] ?? 1);

                $from_page = round($sura?->from_page + $sura?->memorizations?->last()?->memorized_pages ?? 0, 1);
                if ($from_page == $sura?->to_page) {
                    $from_page = $sura?->from_page;
                }

                return [
                    'type' => 'memorization',
                    'grade' => 'ممتاز',
                    'is_need_rememorisation' => false,
                    'is_need_revision' => false,
                    'from_page' => $from_page,
                    'to_page' => $from_page,
                    'sura_id' => $arguments['sura'] ?? 1,
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

                $isNeedRememorisation = $data['is_need_rememorisation'] ?? false;
                $isNeedRevision = $data['is_need_revision'] ?? false;

                $memorization->is_need_rememorisation = $isNeedRememorisation;
                $memorization->is_need_revision = $isNeedRevision;

                // When flagging for needs mode: save page range, skip grading/points.
                if ($isNeedRememorisation || $isNeedRevision) {
                    // dd([$data['from_page'], $sura['from_page'], $data['to_page'], $sura['to_page']]);
                    if (
                        $data['from_page'] != $sura['from_page'] || $data['to_page'] != $sura['to_page']
                    ) {

                        $memorization->need_from_page = $data['from_page'];
                        $memorization->need_to_page = $data['to_page'];
                    }

                    $memorization->save();

                    $label = $isNeedRememorisation ? 'إعادة الحفظ' : 'المراجعة';

                    Notification::make()
                        ->title("تم تحديد السورة لـ{$label} (ص {$data['from_page']} → {$data['to_page']})")
                        ->warning()
                        ->send();

                    return;
                }

                // Normal grading flow — clear needs flags and page range.
                $memorization->need_from_page = null;
                $memorization->need_to_page = null;

                // Auto-clear the matching needs flag when a successful log is saved.
                if ($data['type'] === 'memorization') {
                    $memorization->is_need_rememorisation = false;
                    $memorization->memorization_degree = $data['grade'];
                    if ($data['to_page'] >= $sura->pages_count + $sura->from_page) {
                        $memorization->memorization_repetition = ($memorization->memorization_repetition ?? 0) + 1;
                    }
                } elseif ($data['type'] === 'revision') {
                    $memorization->is_need_revision = false;
                    $memorization->revision_degree = $data['grade'];
                    if ($data['to_page'] >= $sura->pages_count + $sura->from_page) {
                        $memorization->revision_repetition = ($memorization->revision_repetition ?? 0) + 1;
                    }
                } elseif ($data['type'] === 'test') {
                    $memorization->test_grade = $data['grade'];
                    if ($data['to_page'] >= $sura->pages_count + $sura->from_page) {
                        $memorization->test_counts = ($memorization->test_counts ?? 0) + 1;
                    }
                }

                if (! empty($data['last_test_name'])) {
                    $memorization->last_test_name = $data['last_test_name'];
                }
                if (! empty($data['update_date'])) {
                    $memorization->update_date = $data['update_date'];
                }

                $memorization->memorized_pages = $data['to_page'] - $sura->from_page;

                $memorization->save();
                $pageLog = $this->setPageLogs($data, $memorization);
                $this->setPointTransation($memorization, $data, $pageLog);
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
                    TextEntry::make('selected_suras_names')
                        ->label('السور المحددة'),
                    TextEntry::make('selected_suras_names_content')
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
                            TextInput::make('last_test_name')->hidden(fn ($get) => $get('type') != 'test')->label('اسم الاختبار')->nullable(),
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

                    $isNeedRememorisation = $data['is_need_rememorisation'] ?? false;
                    $isNeedRevision = $data['is_need_revision'] ?? false;
                    $isFlagOnly = $isNeedRememorisation || $isNeedRevision;

                    foreach ($this->selectedSuras as $suraId) {
                        $sura = Sura::find($suraId);
                        if (! $sura) {
                            continue;
                        }

                        $memorization = Memorization::firstOrNew([
                            'student_id' => $this->record->id,
                            'sura_id' => $suraId,
                        ]);

                        $memorization->is_need_rememorisation = $isNeedRememorisation;
                        $memorization->is_need_revision = $isNeedRevision;

                        // In bulk flag mode: store full sura page range, skip grading.
                        if ($isFlagOnly) {
                            // $memorization->need_from_page = $sura->from_page;
                            // $memorization->need_to_page = $sura->from_page + $sura->pages_count;
                            $memorization->save();

                            continue;
                        }

                        // Normal bulk grading flow — clear needs state.
                        $memorization->need_from_page = null;
                        $memorization->need_to_page = null;

                        $fromPage = $sura->from_page;
                        $toPage = $fromPage + $sura->pages_count;

                        $memorization->memorized_pages = $sura->pages_count;

                        if ($data['type'] === 'memorization') {
                            $memorization->is_need_rememorisation = false;
                            $memorization->memorization_degree = $data['grade'];
                            $memorization->memorization_repetition = ($memorization->memorization_repetition ?? 0) + 1;
                        } elseif ($data['type'] === 'revision') {
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

                        $logData = array_merge($data, ['from_page' => $fromPage, 'to_page' => $toPage]);
                        $pageLog = $this->setPageLogs($logData, $memorization);
                        $this->setPointTransation($memorization, $logData, $pageLog);
                    }

                    $this->selectedSuras = [];

                    $label = $isNeedRememorisation ? 'إعادة الحفظ' : ($isNeedRevision ? 'المراجعة' : null);

                    Notification::make()
                        ->title($label ? "تم تحديد السور لـ{$label}" : 'تم تسجيل الإنجاز بنجاح')
                        ->when($isFlagOnly, fn ($n) => $n->warning())
                        ->when(! $isFlagOnly, fn ($n) => $n->success())
                        ->send();
                }),
        ];
    }

    protected function setPointTransation(Memorization $memorization, $data, $pageLog = null)
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

        // Determine if it is a repetition
        $isRepetition = false;
        $repetitionPercent = 100;
        if ($data['type'] === 'memorization' && (($memorization['memorization_repetition'] ?? 0) > 1)) {
            $isRepetition = true;
            $rePercentSetting = Setting::where('key', 're_recitation_percent')->first();
            $repetitionPercent = $rePercentSetting ? (int) $rePercentSetting->value : 50;
        } elseif ($data['type'] === 'revision' && (($memorization['revision_repetition'] ?? 0) > 1)) {
            $isRepetition = true;
            $rePercentSetting = Setting::where('key', 're_revision_percent')->first();
            $repetitionPercent = $rePercentSetting ? (int) $rePercentSetting->value : 50;
        }

        $multiplier = $memorization->student->points_multiplier ?? 1.0;
        $totalPoints = 0;

        $reason = (__($data['type']));
        if ($this->isFullSura($memorization->sura, $data)) {
            $reason .= ' سورة '.$memorization->sura->name;
        } else {
            $reason .= ' من سورة '.$memorization->sura->name;
            $reason .= ' ( صفحة '.$data['from_page'].' -> '.$data['to_page'].' )';
        }
        if ($data['type'] == 'test') {
            $reason .= ' ('.$memorization->last_test_name.') ';
        }

        if ($isRepetition) {
            $reason .= ' (إعادة)';
        }

        if (! $isNoPoints) {
            $pages = (float) ($data['to_page'] - $data['from_page']);
            $basePoints = $pages * $pointsPerPage * $multiplier;
            $gradeScaled = $basePoints * ($gradePercent / 100.0);
            $totalPoints = (int) round($gradeScaled * ($repetitionPercent / 100.0));
        } else {
            $reason .= ' (بدون نقاط)';
        }

        PointTransaction::create([
            'student_id' => $memorization->student_id,
            'teacher_id' => auth()->id() ?? 1,
            'amount' => $totalPoints,
            'reason' => $reason,
            'sura_id' => $memorization->sura_id,
            'page_log_id' => $pageLog?->id,
        ]);
    }

    protected function setPageLogs($data, $memorization): PageLog
    {
        // 1. Instantiate the log with all your needed data
        $pageLog = new PageLog([
            'student_id' => $memorization->student_id,
            'sura_id' => $memorization->sura_id,
            'type' => $data['type'] == 'memorization' ? 'recitation' : $data['type'],
            'from_page' => $data['from_page'],
            'to_page' => $data['to_page'],
            'count' => $data['to_page'] - $data['from_page'],
            'date' => Date::now()->format('Y-m-d'),
        ]);

        // 2. Save it quietly so the background PointTransaction listener stays asleep
        $pageLog->saveQuietly();

        return $pageLog;
    }

    protected function isFullSura($sura, $data)
    {
        if ($data['from_page'] == $sura->from_page &&
         $data['to_page'] == $data['from_page'] + $sura->pages_count) {
            return true;
        }

        return false;
    }
}
