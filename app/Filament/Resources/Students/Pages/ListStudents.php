<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Sura;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    /**
     * Auto-initialise the teacher's selected classroom on first page load.
     */
    public function mount(): void
    {
        parent::mount();

        $user = auth()->user();

        if ($user && $user->role === 'teacher' && ! $user->selected_classroom_id) {
            $default = $user->defaultClassroom();

            if ($default) {
                $user->update(['selected_classroom_id' => $default->id]);
            }
        }
    }

    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        $actions = [];

        // Classroom selector for teachers
        if ($user && $user->role === 'teacher') {
            $actions[] = Action::make('select_classroom')
                ->label(fn () => auth()->user()->selectedClassroom?->name ?? 'اختر الحلقة')
                ->icon('heroicon-o-academic-cap')
                ->color('gray')
                ->modalHeading('اختيار الحلقة')
                ->modalDescription('اختر الحلقة التي تريد عرض طلابها. يبقى هذا الاختيار محفوظاً حتى تغيّره.')
                ->modalWidth('sm')
                ->fillForm(fn () => ['classroom_id' => auth()->user()->selected_classroom_id])
                ->form([
                    Select::make('classroom_id')
                        ->label('الحلقة')
                        ->options(function () {
                            return auth()->user()
                                ->classrooms()
                                ->pluck('classrooms.name', 'classrooms.id');
                        })
                        ->required()
                        ->searchable(),
                ])
                ->action(function (array $data) {
                    auth()->user()->update(['selected_classroom_id' => $data['classroom_id']]);

                    Notification::make()
                        ->title('تم تغيير الحلقة بنجاح')
                        ->success()
                        ->send();

                    // Refresh the page so the table re-renders with the new scope
                    $this->redirect(static::getUrl());
                });
        }

        $actions[] = CreateAction::make();

        $actions[] = Action::make('export_data')
            ->label('تصدير البيانات')
            ->icon('heroicon-o-document-arrow-down')
            ->color('gray')
            ->action(function () {
                $suras = Sura::orderBy('id')->get();
                $students = Student::with(['attendances', 'memorizations'])->get();

                $headers = [
                    'اسم الطالب',
                    'عدد الحضور',
                    'عدد الغياب',
                    'إجمالي الصفحات المحفوظة',
                ];

                foreach ($suras as $sura) {
                    $headers[] = $sura->name;
                }

                return response()->streamDownload(function () use ($students, $suras, $headers) {
                    $handle = fopen('php://output', 'w');

                    // UTF-8 BOM
                    fwrite($handle, "\xEF\xBB\xBF");

                    fputcsv($handle, $headers);

                    foreach ($students as $student) {
                        $presentCount = $student->attendances->where('is_present', true)->count();
                        $absentCount = $student->attendances->where('is_present', false)->count();
                        $totalMemorized = $student->memorizations->sum('memorized_pages');

                        $row = [
                            $student->name,
                            $presentCount,
                            $absentCount,
                            $totalMemorized,
                        ];

                        foreach ($suras as $sura) {
                            $memorization = $student->memorizations->firstWhere('sura_id', $sura->id);
                            if ($memorization && $sura->pages_count > 0) {
                                $percentage = ($memorization->memorized_pages / $sura->pages_count) * 100;
                                $percentage = min(100, $percentage);
                                $row[] = round($percentage, 1).'%';
                            } else {
                                $row[] = '0%';
                            }
                        }

                        fputcsv($handle, $row);
                    }

                    fclose($handle);
                }, 'students_export_'.now()->format('Y-m-d').'.csv');
            });

        return $actions;
    }
}
