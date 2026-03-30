<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Models\PointTransaction;
use App\Models\Setting;

class AttendanceObserver
{
    public function created(Attendance $attendance): void
    {
        $this->handlePoints($attendance);
    }
    
    public function updated(Attendance $attendance): void
    {
        if ($attendance->isDirty('is_present')) {
             // Remove any point transactions generated today for attendance to prevent zero-sum / accumulation bugs
             \App\Models\PointTransaction::where('student_id', $attendance->student_id)
                  ->whereIn('reason', ['حضور', 'غياب'])
                  ->whereDate('created_at', \Carbon\Carbon::today())
                  ->delete();

             $this->handlePoints($attendance);
        }
    }

    protected function handlePoints(Attendance $attendance)
    {
        $settingKey = $attendance->is_present ? 'attendance_points' : 'absence_penalty';
        $setting = Setting::where('key', $settingKey)->first();
        $points = $setting ? (int) $setting->value : ($attendance->is_present ? 5 : -5);

        if ($points !== 0) {
            PointTransaction::create([
                'student_id' => $attendance->student_id,
                'teacher_id' => auth()->id() ?? 1, 
                'amount' => $points,
                'reason' => $attendance->is_present ? 'حضور' : 'غياب'
            ]);
        }
    }
}
