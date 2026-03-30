<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\PageLog;
use App\Models\Student;
use Carbon\Carbon;

class TeacherStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // تحديد هوية الأستاذ إذا لم يكن مديراً
        $teacherId = auth()->user()->role !== 'admin' ? auth()->id() : null;

        // تحديد بداية ونهاية الشهر الحالي
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 1. حساب إجمالي صفحات التسميع (Recitation) لهذا الشهر
        $monthlyRecitations = PageLog::query()
            ->where('type', 'recitation')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->when($teacherId, function ($query) use ($teacherId) {
                $query->whereHas('student', fn($q) => $q->where('teacher_id', $teacherId));
            })
            ->sum('count'); // نستخدم sum لجمع الصفحات وليس count

        // 2. حساب إجمالي صفحات المراجعة (Revision) لهذا الشهر
        $monthlyRevisions = PageLog::query()
            ->where('type', 'revision')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->when($teacherId, function ($query) use ($teacherId) {
                $query->whereHas('student', fn($q) => $q->where('teacher_id', $teacherId));
            })
            ->sum('count');

        // 3. عدد الطلاب الإجمالي المرتبطين بهذا الأستاذ أو الكل للمدير
        $totalStudents = Student::query()
            ->when($teacherId, fn($q) => $q->where('teacher_id', $teacherId))
            ->count();

        return [
            Stat::make('صفحات التسميع (الشهر)', number_format($monthlyRecitations, 1))
                ->description('إجمالي صفحات الحفظ المنجزة')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // يمكنك ربط هذه المصفوفة ببيانات حقيقية لاحقاً

            Stat::make('صفحات المراجعة (الشهر)', number_format($monthlyRevisions, 1))
                ->description('إجمالي صفحات المراجعة المنجزة')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),

            Stat::make('عدد الطلاب', $totalStudents)
                ->description("إجمالي الطلاب المسجلين")
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),
        ];
    }
}