<?php

namespace App\Livewire\Parent;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $phone = '';

    public string $password = '';

    /** @var array<string, array<string>> */
    protected array $rules = [
        'phone' => ['required', 'string'],
        'password' => ['required', 'string'],
    ];

    public function submit(): void
    {
        $this->validate();

        if (Auth::guard('guardian')->attempt(['phone' => $this->phone, 'password' => $this->password])) {
            $this->redirect(route('parent.dashboard'), navigate: true);

            return;
        }

        $this->addError('phone', 'رقم الهاتف أو كلمة المرور غير صحيحة.');
    }

    public function render(): View
    {
        return view('livewire.parent.login')
            ->layout('layouts.parent-auth');
    }
}
