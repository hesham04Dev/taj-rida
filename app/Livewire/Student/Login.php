<?php

namespace App\Livewire\Student;

use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $access_code = '';

    /** @var array<string, array<string>> */
    protected array $rules = [
        'access_code' => ['required', 'string'],
    ];

    public function submit(): void
    {
        $this->validate();

        $student = Student::withoutGlobalScopes()
            ->where('access_code', $this->access_code)
            ->first();

        if (! $student) {
            $this->addError('access_code', 'رمز الدخول غير صحيح.');

            return;
        }

        Auth::guard('student')->login($student);

        $this->redirect(route('student.dashboard'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.student.login')
            ->layout('layouts.student-auth');
    }
}
