<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\PageLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class TeacherChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    public function getHeading(): string
    {
        return 'معدل الإنجاز (عدد الصفحات)';
    }

    protected function getFilters(): ?array
    {
        return [
            'week' => 'آخر 7 أيام',
            'month' => 'آخر 30 يوم',
        ];
    }

    protected function getData(): array
    {
        $teacherId = auth()->user()->role !== 'admin' ? auth()->id() : null;
        $activeFilter = $this->filter ?? 'week';
        $daysCount = $activeFilter === 'week' ? 7 : 30;

        $startDate = Carbon::today()->subDays($daysCount - 1);

        // جلب كافة البيانات في استعلام واحد مجمع للأداء العالي
        $logs = PageLog::query()
            ->select(
                DB::raw('DATE(date) as log_date'),
                'type',
                DB::raw('SUM(count) as total_pages')
            )
            ->whereDate('date', '>=', $startDate)
            ->when($teacherId, function ($query) use ($teacherId) {
                $query->whereHas('student', fn($q) => $q->where('teacher_id', $teacherId));
            })
            ->groupBy('log_date', 'type')
            ->get();

        $recitations = [];
        $revisions = [];
        $labels = [];

        // معالجة البيانات لملء الأيام التي ليس بها إنجاز بصفر
        for ($i = $daysCount - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->toDateString();
            $label = $activeFilter === 'week' 
                ? Carbon::parse($date)->translatedFormat('l') 
                : Carbon::parse($date)->format('d/m');
            
            $labels[] = $label;

            // استخراج القيمة من المجموعة أو وضع صفر إذا لم توجد
            $recitations[] = (float) $logs->where('log_date', $date)->where('type', 'recitation')->first()?->total_pages ?? 0;
            $revisions[] = (float) $logs->where('log_date', $date)->where('type', 'revision')->first()?->total_pages ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'إجمالي صفحات التسميع',
                    'data' => $recitations,
                    'borderColor' => '#36A2EB',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'fill' => 'start',
                    'tension' => 0.4, // لجعل الخط منحنياً بشكل جميل
                ],
                [
                    'label' => 'إجمالي صفحات المراجعة',
                    'data' => $revisions,
                    'borderColor' => '#4BC0C0',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}