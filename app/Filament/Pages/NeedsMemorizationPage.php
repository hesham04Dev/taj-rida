<?php

namespace App\Filament\Pages;

use App\Models\Curriculum;
use App\Models\Memorization;
use App\Models\Student;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;

class NeedsMemorizationPage extends Page implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

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
                ->action('downloadPng'),
        ];
    }

    /**
     * Returns all students with flagged suras, including memorization IDs for interactivity.
     *
     * @return array<array{
     *     student: Student,
     *     memorization: array<array{id: int, name: string, children: array<string>}>,
     *     revision:     array<array{id: int, name: string, children: array<string>}>
     * }>
     */
    public static function groupedNeeds(): array
    {
        $query = Memorization::with(['student', 'curriculum'])
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

        return $query->get()
            ->groupBy('student_id')
            ->map(function ($items) {
                return [
                    'student' => $items[0]->student,
                    'memorization' => $items->filter(fn ($m) => $m->is_need_rememorisation)
                        ->map(fn ($m) => [
                            'id' => $m->id,
                            'name' => $m->curriculum->name,
                            'children' => $m->needs_rememorisation_children,
                        ])->values()->all(),
                    'revision' => $items->filter(fn ($m) => $m->is_need_revision)
                        ->map(fn ($m) => [
                            'id' => $m->id,
                            'name' => $m->curriculum->name,
                            'children' => $m->needs_revision_children,
                        ])->values()->all(),
                ];
            })
            ->values()
            ->all();
    }

    public function getGroupedNeedsProperty()
    {
        return $this->groupedNeeds();
    }

    /**
     * Remove the revision flag (or specific children) from a memorization record.
     */
    public function removeRevisionAction(): Action
    {
        return Action::make('removeRevision')
            ->requiresConfirmation()
            ->modalHeading('حذف المراجعة')
            ->modalDescription('هل أنت متأكد أنك تريد إزالة تحديد المراجعة لهذه السورة؟')
            ->modalSubmitActionLabel('نعم، احذف')
            ->color('danger')
            ->icon('heroicon-o-trash')
            ->action(function (array $arguments) {
                $memorization = Memorization::find($arguments['id'] ?? null);
                if (! $memorization) {
                    return;
                }

                $memorization->is_need_revision = false;
                $memorization->needs_revision_children = [];
                $memorization->save();

                Notification::make()
                    ->title('تم إزالة تحديد المراجعة')
                    ->success()
                    ->send();
            });
    }

    /**
     * Remove the rememorisation flag from a memorization record.
     */
    public function removeMemorizationAction(): Action
    {
        return Action::make('removeMemorization')
            ->requiresConfirmation()
            ->modalHeading('حذف إعادة الحفظ')
            ->modalDescription('هل أنت متأكد أنك تريد إزالة تحديد إعادة الحفظ لهذه السورة؟')
            ->modalSubmitActionLabel('نعم، احذف')
            ->color('danger')
            ->icon('heroicon-o-trash')
            ->action(function (array $arguments) {
                $memorization = Memorization::find($arguments['id'] ?? null);
                if (! $memorization) {
                    return;
                }

                $memorization->is_need_rememorisation = false;
                $memorization->needs_rememorisation_children = [];
                $memorization->save();

                Notification::make()
                    ->title('تم إزالة تحديد إعادة الحفظ')
                    ->success()
                    ->send();
            });
    }

    /**
     * Add a revision flag for a student's specific juz and pages.
     */
    public function addRevisionAction(): Action
    {
        return Action::make('addRevision')
            ->label('إضافة مراجعة')
            ->icon('heroicon-o-plus')
            ->color('primary')
            ->schema([
                Select::make('student_id')
                    ->label('الطالب')
                    ->options(function () {
                        $query = Student::query();
                        if (auth()->check() && auth()->user()->role !== 'admin') {
                            $query->where('teacher_id', auth()->id());
                        }

                        return $query->orderBy('name')->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->live(),

                Select::make('curriculum_id')
                    ->label('الجزء / السورة')
                    ->options(fn () => Curriculum::orderBy('number')->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->live(),

                CheckboxList::make('children')
                    ->label('الصفحات / السور')
                    ->options(function (Get $get): array {
                        $curriculumId = $get('curriculum_id');
                        if (! $curriculumId) {
                            return [];
                        }
                        $curriculum = Curriculum::find($curriculumId);

                        return collect($curriculum?->children ?? [])
                            ->pluck('label', 'label')
                            ->toArray();
                    })
                    ->columns(3)
                    ->gridDirection('row')
                    ->hidden(fn (Get $get) => ! $get('curriculum_id')),
            ])
            ->action(function (array $data) {
                $studentId = $data['student_id'];
                $curriculumId = $data['curriculum_id'];
                $selectedChildren = $data['children'] ?? [];

                $curriculum = Curriculum::find($curriculumId);
                if (! $curriculum) {
                    return;
                }

                $memorization = Memorization::firstOrNew([
                    'student_id' => $studentId,
                    'curriculum_id' => $curriculumId,
                ]);

                // If no specific children chosen, flag all
                if (empty($selectedChildren)) {
                    $selectedChildren = collect($curriculum->children ?? [])->pluck('label')->toArray();
                }

                $memorization->is_need_revision = true;
                $memorization->needs_revision_children = array_values(array_unique(
                    array_merge($memorization->needs_revision_children ?? [], $selectedChildren)
                ));
                $memorization->save();

                Notification::make()
                    ->title('تم تحديد المراجعة بنجاح')
                    ->success()
                    ->send();
            });
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
                    'viewport_width' => 1000,
                    'viewport_height' => 1500,
                    'full_screen' => true,
                ]);

            // If successful, grab the URL and break out of the loop
            if ($response->successful()) {
                $imageUrl = $response->json('url');
                break;
            }

            // Check if the failure is due to running out of credits (422 Unprocessable or 429 Too Many Requests)
            if ($response->status() == 422 || $response->status() == 429) {
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
