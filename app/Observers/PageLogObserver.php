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

    protected function handlePoints(PageLog $pageLog)
    {
        $settingKey = $pageLog->type === 'recitation' ? 'recitation_points_per_page' : 'revision_points_per_page';
        $setting = Setting::where('key', $settingKey)->first();
        $pointsPerLog = $setting ? (int) $setting->value : 0;
        
        $multiplier = $pageLog->student->points_multiplier ?? 1.0;
        
        $totalPoints = (int) ($pageLog->count * $pointsPerLog * $multiplier);

        if ($totalPoints > 0) {
            PointTransaction::create([
                'student_id' => $pageLog->student_id,
                'teacher_id' => auth()->id() ?? 1,
                'amount' => $totalPoints,
                'reason' => 'تسجيل ' . ($pageLog->type === 'recitation' ? 'تسميع' : 'مراجعة') . ' (' . $pageLog->count . ' صفحة)'
            ]);
        }
    }
}
