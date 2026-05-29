<?php

namespace App\Livewire\Student;

use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public Student $student;

    public function mount(): void
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();
        $this->student = $student;
    }

    public function logout(): void
    {
        Auth::guard('student')->logout();

        $this->redirect(route('student.login'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.student.dashboard')
            ->layout('layouts.student');
    }
}
