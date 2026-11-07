<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class LeaderboardWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    // Makes the widget take the full width of the dashboard
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'لوحة الشرف';

    public function table(Table $table): Table
    {
        // Last seven days rather than strict calendar-week boundaries
        $endOfWeek = now()->endOfDay();
        $startOfWeek = now()->subDays(7)->startOfDay();

        $startDate = $startOfWeek->toDateString();
        $endDate = $endOfWeek->toDateString();

        return $table
            ->query(
                Student::query()
                    ->select('students.*')
                    // Weekly points — already stored as final values, no multiplier needed
                    ->withSum(['pointTransactions as weekly_points' => function ($query) use ($startOfWeek, $endOfWeek) {
                        $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                    }], 'amount')
                    // Weekly memorization: sum weighted_pages (= SUM of each child's points_multiplier).
                    // Falls back to raw count for legacy rows that pre-date the weighted_pages column.
                    ->addSelect(DB::raw(
                        "(SELECT COALESCE(SUM(COALESCE(pl.weighted_pages, pl.`count`)), 0)
                          FROM page_logs pl
                          WHERE pl.student_id = students.id
                            AND pl.type = 'memorization'
                            AND pl.date BETWEEN '{$startDate}' AND '{$endDate}') as weekly_memorization"
                    ))
                    // Weekly revision: same logic
                    ->addSelect(DB::raw(
                        "(SELECT COALESCE(SUM(COALESCE(pl.weighted_pages, pl.`count`)), 0)
                          FROM page_logs pl
                          WHERE pl.student_id = students.id
                            AND pl.type = 'revision'
                            AND pl.date BETWEEN '{$startDate}' AND '{$endDate}') as weekly_revision"
                    ))
            )
            // Sets the default sorting to highest points
            ->defaultSort('weekly_points', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('weekly_points')
                    ->label('النقاط')
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-m-star')
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('weekly_points', $direction))
                    ->formatStateUsing(fn ($state) => $state ?? 0),

                Tables\Columns\TextColumn::make('weekly_memorization')
                    ->label('الحفظ (صفحات)')
                    ->badge()
                    ->color('success')
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('weekly_memorization', $direction))
                    ->formatStateUsing(fn ($state) => round($state ?? 0, 1)),

                Tables\Columns\TextColumn::make('weekly_revision')
                    ->label('المراجعة (صفحات)')
                    ->badge()
                    ->color('info')
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('weekly_revision', $direction))
                    ->formatStateUsing(fn ($state) => round($state ?? 0, 1)),
            ])
            ->paginated([5, 10, 25]);
    }
}
