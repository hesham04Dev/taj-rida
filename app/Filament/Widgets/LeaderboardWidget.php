<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class LeaderboardWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    
    // Makes the widget take the full width of the dashboard
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'لوحة الشرف';

    public function table(Table $table): Table
    {
        // Define the start and end of the current week once for consistency
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        // Check if the user is not admin to filter their students only
        $teacherId = auth()->user()->role !== 'admin' ? auth()->id() : null;

        return $table
            ->query(
                Student::query()
                    ->when($teacherId, fn($q) => $q->where('teacher_id', $teacherId))
                    // Calculate virtual columns using withSum
                    ->withSum(['pointTransactions as weekly_points' => function ($query) use ($startOfWeek, $endOfWeek) {
                        $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                    }], 'amount')
                    ->withSum(['pageLogs as weekly_recitation' => function ($query) use ($startOfWeek, $endOfWeek) {
                        $query->where('type', 'recitation')
                              ->whereBetween('date', [$startOfWeek, $endOfWeek]);
                    }], 'count')
                    ->withSum(['pageLogs as weekly_revision' => function ($query) use ($startOfWeek, $endOfWeek) {
                        $query->where('type', 'revision')
                              ->whereBetween('date', [$startOfWeek, $endOfWeek]);
                    }], 'count')
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
                    // Custom sort logic for virtual column
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('weekly_points', $direction);
                    })
                    ->formatStateUsing(fn ($state) => $state ?? 0),

                Tables\Columns\TextColumn::make('weekly_recitation')
                    ->label('الحفظ (صفحات)')
                    ->badge()
                    ->color('success')
                    // Custom sort logic for virtual column
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('weekly_recitation', $direction);
                    })
                    ->formatStateUsing(fn ($state) => $state ?? 0),

                Tables\Columns\TextColumn::make('weekly_revision')
                    ->label('المراجعة (صفحات)')
                    ->badge()
                    ->color('info')
                    // Custom sort logic for virtual column
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('weekly_revision', $direction);
                    })
                    ->formatStateUsing(fn ($state) => $state ?? 0),
            ])
            ->paginated([5, 10, 25]); // Added pagination for better performance
    }
}