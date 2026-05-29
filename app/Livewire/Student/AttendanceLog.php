<?php

namespace App\Livewire\Student;

use App\Models\Attendance;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AttendanceLog extends Component
{
    /** @var Collection<int, Attendance> */
    public Collection $records;

    public int $presentCount = 0;

    public int $absentCount = 0;

    public function mount(): void
    {
        $studentId = Auth::guard('student')->id();

        $this->records = Attendance::where('student_id', $studentId)
            ->orderByDesc('date')
            ->limit(30)
            ->get();

        $this->presentCount = $this->records->where('is_present', true)->count();
        $this->absentCount = $this->records->where('is_present', false)->count();
    }

    public function render(): View
    {
        return view('livewire.student.attendance-log');
    }
}
