<?php

namespace App\Observers;

use App\Models\PageLog;
use App\Models\PointTransaction;
use App\Models\Setting;

class PageLogObserver
{
    public function created(PageLog $pageLog): void
    {
        $this->handlePoints($pageLog);
    }

    public function updated(PageLog $pageLog): void
    {
        if ($pageLog->isDirty('count') || $pageLog->isDirty('type')) {
            // Delete previous point transactions for this specific page log date to prevent duplication
            PointTransaction::where('student_id', $pageLog->student_id)
                ->where(function ($q) {
                    $q->where('reason', 'like', '%تسميع%')
                        ->orWhere('reason', 'like', '%مراجعة%');
                })
                ->whereDate('created_at', $pageLog->created_at ?? now())
                ->delete();

            $this->handlePoints($pageLog);
        }
    }

    protected function handlePoints(PageLog $pageLog): void
    {
        $student = $pageLog->student()->first();
        if (! $student) {
            return;
        }

        $settingKey = $pageLog->type === 'recitation' ? 'recitation_points_per_page' : 'revision_points_per_page';
        $setting = Setting::where('key', $settingKey)->first();
        $pointsPerPage = $setting ? (float) $setting->value : ($pageLog->type === 'recitation' ? 10.0 : 5.0);

        $multiplier = (float) ($student->points_multiplier ?? 1.0);
        $amount = (int) round($pointsPerPage * $pageLog->count * $multiplier);

        if ($amount !== 0) {
            PointTransaction::create([
                'student_id' => $pageLog->student_id,
                'teacher_id' => auth()->id() ?? $student->teacher_id ?? 1,
                'amount' => $amount,
                'reason' => $pageLog->type === 'recitation' ? 'تسميع' : 'مراجعة',
                'sura_id' => $pageLog->sura_id,
                'page_log_id' => $pageLog->id,
            ]);
        }
    }

    public function deleted(PageLog $pageLog): void
    {
        PointTransaction::where('page_log_id', $pageLog->id)->delete();
    }
}
