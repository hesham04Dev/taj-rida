<?php

namespace App\Filament\Pages;

use App\Models\Student;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;

class PointsCards extends Page
{
    protected string $view = 'filament.pages.points-cards';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    public static function getNavigationLabel(): string
    {
        return 'بطاقات النقاط';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'سوق النقاط';
    }

    public function getTitle(): string
    {
        return 'بطاقات النقاط والدورة';
    }

    /**
     * Students with their total points, scoped by teacher for non-admins.
     *
     * @return Collection<int, Student>
     */
    public function getStudentsProperty()
    {
        $query = Student::with('pointTransactions');

        if (auth()->user()->role !== 'admin') {
            $query->where('teacher_id', auth()->id());
        }

        return $query
            ->get()
            ->map(function (Student $student) {
                $student->total_pts = (int) $student->pointTransactions->sum('amount');

                return $student;
            })
            ->sortByDesc('total_pts')
            ->values();
    }

    /**
     * URL for the printable cards view, scoped the same way.
     */
    public function getPrintUrl(): string
    {
        $isAdmin = auth()->user()->role === 'admin';

        return route('points-cards.print', [
            'teacher_id' => $isAdmin ? null : auth()->id(),
        ]);
    }
}
