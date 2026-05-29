<?php

namespace App\Livewire\Parent;

use App\Models\Attendance;
use App\Models\Memorization;
use App\Models\PointTransaction;
use App\Models\Student;
use App\Models\StudentNote;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class ChildProgress extends Component
{
    public Student $student;

    /** @var Collection<int, Memorization> */
    public Collection $tasks;

    public int $totalPoints = 0;

    public int $remainingPoints = 0;

    public int $givenPoints = 0;

    /** @var Collection<int, Memorization> */
    public Collection $memorizations;

    /** @var Collection<int, Attendance> */
    public Collection $attendanceRecords;

    public int $presentCount = 0;

    public int $absentCount = 0;

    /** @var Collection<int, StudentNote> */
    public Collection $notes;

    public function mount(Student $student): void
    {
        $this->student = $student;
        $studentId = $student->id;

        // Tasks
        $this->tasks = Memorization::with('sura')
            ->where('student_id', $studentId)
            ->where(function ($q): void {
                $q->where('is_need_rememorisation', true)
                    ->orWhere('is_need_revision', true);
            })
            ->get();

        // Points
        $this->totalPoints = (int) PointTransaction::where('student_id', $studentId)->sum('amount');
        $this->givenPoints = (int) $student->given_points;
        $this->remainingPoints = max(0, $this->totalPoints - $this->givenPoints);

        // Grades
        $this->memorizations = Memorization::with('sura')
            ->where('student_id', $studentId)
            ->where(function ($q): void {
                $q->whereNotNull('memorization_degree')
                    ->orWhereNotNull('revision_degree');
            })
            ->orderByDesc('updated_at')
            ->get();

        // Attendance
        $this->attendanceRecords = Attendance::where('student_id', $studentId)
            ->orderByDesc('date')
            ->limit(30)
            ->get();
        $this->presentCount = $this->attendanceRecords->where('is_present', true)->count();
        $this->absentCount = $this->attendanceRecords->where('is_present', false)->count();

        // Notes
        $this->notes = StudentNote::where('student_id', $studentId)
            ->orderByDesc('date')
            ->limit(5)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.parent.child-progress');
    }
}
