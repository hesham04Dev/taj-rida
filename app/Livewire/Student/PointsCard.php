<?php

namespace App\Livewire\Student;

use App\Models\PointTransaction;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PointsCard extends Component
{
    public int $totalPoints = 0;

    public int $remainingPoints = 0;

    public int $givenPoints = 0;

    public function mount(): void
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();

        $this->totalPoints = (int) PointTransaction::where('student_id', $student->id)->sum('amount');
        $this->givenPoints = (int) $student->given_points;
        $this->remainingPoints = max(0, $this->totalPoints - $this->givenPoints);
    }

    public function render(): View
    {
        return view('livewire.student.points-card');
    }
}
