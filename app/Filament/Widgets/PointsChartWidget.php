<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\PointTransaction;
use Carbon\Carbon;

class PointsChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    public function getHeading(): string
    {
        return 'معدل النقاط المكتسبة والمخصومة';
    }

    protected function getFilters(): ?array
    {
        return [
            'week' => 'هذا الأسبوع',
            'month' => 'هذا الشهر',
        ];
    }

    protected function getData(): array
    {
        $teacherId = auth()->user()->role !== 'admin' ? auth()->id() : null;
        $activeFilter = $this->filter ?? 'week';

        $earned = [];
        $deducted = [];
        $labels = [];

        $daysCount = $activeFilter === 'week' ? 6 : 29;

        for ($i = $daysCount; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $labels[] = $activeFilter === 'week' ? $day->translatedFormat('l') : $day->format('d/m');
            
            $qEarned = PointTransaction::whereDate('created_at', $day)->where('amount', '>', 0);
            $qDeducted = PointTransaction::whereDate('created_at', $day)->where('amount', '<', 0);

            if ($teacherId) {
                $qEarned->whereHas('student', fn($q) => $q->where('teacher_id', $teacherId));
                $qDeducted->whereHas('student', fn($q) => $q->where('teacher_id', $teacherId));
            }

            $earned[] = (int) $qEarned->sum('amount');
            $deducted[] = (int) abs($qDeducted->sum('amount')); // Using abs() so to plot as positive values on the chart
        }

        return [
            'datasets' => [
                [
                    'label' => 'نقاط مكتسبة',
                    'data' => $earned,
                    'borderColor' => '#10B981', // Tailwind Emerald 500
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'fill' => 'start',
                    'tension' => 0.4
                ],
                [
                    'label' => 'نقاط مخصومة',
                    'data' => $deducted,
                    'borderColor' => '#EF4444', // Tailwind Red 500
                    'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                    'fill' => 'start',
                    'tension' => 0.4
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
