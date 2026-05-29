<?php

namespace App\Livewire\Parent;

use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    /** @var Collection<int, Student> */
    public Collection $children;

    public ?int $selectedStudentId = null;

    public function mount(): void
    {
        $parent = Auth::guard('guardian')->user();

        // Fetch all students associated with this parent's phone number
        $this->children = $parent->students;

        if ($this->children->isNotEmpty()) {
            $this->selectedStudentId = $this->children->first()->id;
        }
    }

    public function selectStudent(int $studentId): void
    {
        // Ensure the parent is authorized to view this student
        $parent = Auth::guard('guardian')->user();
        $authorizedIds = $parent->studentGuardians()->pluck('student_id')->toArray();

        if (in_array($studentId, $authorizedIds)) {
            $this->selectedStudentId = $studentId;
        }
    }

    public function render(): View
    {
        $selectedStudent = $this->selectedStudentId
            ? Student::withoutGlobalScopes()->find($this->selectedStudentId)
            : null;

        return view('livewire.parent.dashboard', [
            'selectedStudent' => $selectedStudent,
        ])->layout('layouts.parent');
    }
}
