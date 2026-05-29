<?php

namespace App\Livewire\Student;

use App\Models\StudentNote;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TeacherNotes extends Component
{
    /** @var Collection<int, StudentNote> */
    public Collection $notes;

    public function mount(): void
    {
        $studentId = Auth::guard('student')->id();

        $this->notes = StudentNote::where('student_id', $studentId)
            ->orderByDesc('date')
            ->limit(5)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.student.teacher-notes');
    }
}
