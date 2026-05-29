<?php

namespace App\Livewire\Student;

use App\Models\Memorization;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MemorizationTask extends Component
{
    /** @var Collection<int, Memorization> */
    public Collection $tasks;

    public function mount(): void
    {
        $studentId = Auth::guard('student')->id();

        $this->tasks = Memorization::with('sura')
            ->where('student_id', $studentId)
            ->where(function ($q): void {
                $q->where('is_need_rememorisation', true)
                    ->orWhere('is_need_revision', true);
            })
            ->get();
    }

    public function render(): View
    {
        return view('livewire.student.memorization-task');
    }
}
