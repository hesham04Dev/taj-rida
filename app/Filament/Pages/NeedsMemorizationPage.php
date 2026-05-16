<?php

namespace App\Filament\Pages;

use App\Models\Memorization;
use App\Models\Student;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;

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
            Action::make('downloadPng')
                ->label('تحميل كصورة')
                ->color('primary')
                // ->icon('heroicon-o-download')
                ->action('downloadPng'),
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

    public function downloadPng()
    {
        // 1. Render your Blade HTML view to a string
        $html = View::make('exports.sura-report', [
            'groups' => NeedsMemorizationPage::groupedNeeds(),
            'date' => date('Y-m-d'),
        ])->render();

        // 2. Define your available accounts pool
        $accounts = [
            config('services.hcti.account_1'),
            config('services.hcti.account_2'),
        ];

        $imageUrl = null;

        // 3. Loop through your accounts
        foreach ($accounts as $index => $credentials) {
            // Skip if credentials are missing
            if (empty($credentials['id']) || empty($credentials['key'])) {
                continue;
            }

            $response = Http::withBasicAuth($credentials['id'], $credentials['key'])
                ->post('https://hcti.io/v1/image', [
                    'html' => $html,
                    'width' => 850,
                ]);

            // If successful, grab the URL and break out of the loop
            if ($response->successful()) {
                $imageUrl = $response->json('url');
                break;
            }

            // Check if the failure is due to running out of credits (422 Unprocessable or 429 Too Many Requests)
            if ($response->status() == 422 || $response->status() == 429) {
                // Log::warning("HCTI Account " . ($index + 1) . " has reached its free limit. Trying fallback account...");
                continue; // Go to the next account in the loop
            }

            // If it's another error entirely (like invalid HTML), stop and show the error
            abort(500, 'HCTI API Error: '.$response->body());
        }

        // 4. If both accounts fail, return an error message
        if (! $imageUrl) {
            abort(429, 'All available free HCTI API accounts have reached their limits for this month.');
        }

        // 5. Download the image binary and stream it back to your user
        $imageData = file_get_contents($imageUrl);

        return response()->streamDownload(function () use ($imageData) {
            echo $imageData;
        }, 'student_report.png', [
            'Content-Type' => 'image/png',
        ]);
    }
}
