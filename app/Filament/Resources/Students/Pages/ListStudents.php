<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\Student;
use App\Models\Sura;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('export_data')
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
                }),
        ];
    }
}
