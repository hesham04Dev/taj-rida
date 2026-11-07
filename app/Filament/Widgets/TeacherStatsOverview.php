<?php

namespace App\Filament\Widgets;

use App\Models\PageLog;
use App\Models\Student;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class TeacherStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // تحديد ما إذا كان المستخدم أستاذاً وليس مديراً
        $isTeacher = auth()->user()?->role !== 'admin';

        // تحديد بداية ونهاية الشهر الحالي
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 1. حساب إجمالي صفحات التسميع (Memorization) لهذا الشهر بالصفحات الموزونة
        $monthlyMemorizations = (float) PageLog::query()
            ->where('type', 'memorization')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->when($isTeacher, function ($query) {
                $query->whereHas('student');
            })
            ->sum(DB::raw('COALESCE(weighted_pages, `count`)'));

        // 2. حساب إجمالي صفحات المراجعة (Revision) لهذا الشهر بالصفحات الموزونة
        $monthlyRevisions = (float) PageLog::query()
            ->where('type', 'revision')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->when($isTeacher, function ($query) {
                $query->whereHas('student');
            })
            ->sum(DB::raw('COALESCE(weighted_pages, `count`)'));

        // 3. عدد الطلاب الإجمالي المرتبطين بهذا الأستاذ (وفق الحلقة المحددة) أو الكل للمدير
        $totalStudents = Student::query()->count();

        return [
            Stat::make('صفحات التسميع (الشهر)', number_format($monthlyMemorizations, 1))
                ->description('إجمالي صفحات الحفظ المنجزة')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // يمكنك ربط هذه المصفوفة ببيانات حقيقية لاحقاً

            Stat::make('صفحات المراجعة (الشهر)', number_format($monthlyRevisions, 1))
                ->description('إجمالي صفحات المراجعة المنجزة')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),

            Stat::make('عدد الطلاب', $totalStudents)
                ->description('إجمالي الطلاب المسجلين')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),
        ];
    }
}
