<?php

namespace App\Livewire\Student;

use App\Models\Memorization;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GradesCard extends Component
{
    /** @var Collection<int, Memorization> */
    public Collection $memorizations;

    public function mount(): void
    {
        $studentId = Auth::guard('student')->id();

        $this->memorizations = Memorization::with('sura')
            ->where('student_id', $studentId)
            ->whereNotNull('memorization_degree')
            ->orWhere(function ($q) use ($studentId): void {
                $q->where('student_id', $studentId)
                    ->whereNotNull('revision_degree');
            })
            ->orderByDesc('updated_at')
            ->get();
    }

    public function render(): View
    {
        return view('livewire.student.grades-card');
    }
}
