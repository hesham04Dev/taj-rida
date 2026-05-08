<?php

namespace App\Filament\Pages;

use App\Models\Memorization;
use App\Models\Student;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class NeedsMemorizationPage extends Page
{
    protected string $view = 'filament.pages.needs-memorization';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationCircle;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'بحاجة متابعة';
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Memorization::where('is_need_rememorisation', true)->count()
            + Memorization::where('is_need_revision', true)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public function getTitle(): string
    {
        return 'السور بحاجة متابعة';
    }

    /**
     * Returns all students who have at least one flagged sura,
     * with their memorization-flagged and revision-flagged suras separated.
     *
     * @return array<array{
     *     student: Student,
     *     memorization: array<array{name: string, need_from_page: int|null, need_to_page: int|null, is_full_sura: bool}>,
     *     revision:     array<array{name: string, need_from_page: int|null, need_to_page: int|null, is_full_sura: bool}>
     * }>
     */

    // In your Filament Page class
    protected function getHeaderActions(): array
    {
        return [
            Action::make('printReport')
                ->label('طباعة التقرير')
                ->color('success')
                ->icon('heroicon-o-printer')
                ->url(fn () => route('sura.print.report'), shouldOpenInNewTab: true),
        ];
    }

    public static function groupedNeeds(): array
    {
        $query = Memorization::with(['student', 'sura'])
            ->where(function ($q) {
                $q->where('is_need_rememorisation', true)
                    ->orWhere('is_need_revision', true);
            })
            ->orderBy('student_id');

        // Teachers only see their own students.
        if (auth()->check() && auth()->user()->role !== 'admin') {
            $query->whereHas('student', function ($q) {
                $q->where('teacher_id', auth()->id());
            });
        }

        $x = $query->get()
            ->groupBy('student_id')
            ->map(function ($items) {
                $buildChip = fn ($m) => [
                    'name' => $m->sura->name,
                    'need_from_page' => $m->need_from_page,
                    'need_to_page' => $m->need_to_page,
                    // Full sura = pages match entire sura, or no page range stored.
                    // 'is_full_sura' => ! $m->need_from_page
                    //     || ($m->need_from_page == $m->sura->from_page
                    //         && $m->need_to_page == $m->sura->to_page),
                ];

                return [
                    'student' => $items[0]->student,
                    'memorization' => $items->filter(fn ($m) => $m->is_need_rememorisation)
                        ->map($buildChip)->values()->all(),
                    'revision' => $items->filter(fn ($m) => $m->is_need_revision)
                        ->map($buildChip)->values()->all(),
                ];
            })
            ->values()
            ->all();
        // dd($x);

        return $x;
    }

    public function getGroupedNeedsProperty()
    {
        return $this->groupedNeeds();
    }
}
