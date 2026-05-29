<?php

use App\Http\Controllers\SuraReportController;
use App\Http\Middleware\AuthenticateStudent;
use App\Livewire\Student\Dashboard;
use App\Livewire\Student\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/app');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::get('/sura-print-report', [SuraReportController::class, 'print'])
    ->name('sura.print.report');

// Student Portal
Route::prefix('student')->name('student.')->group(function () {
    Route::get('login', Login::class)->name('login');
    Route::post('logout', function () {
        Auth::guard('student')->logout();

        return redirect()->route('student.login');
    })->name('logout');

    Route::middleware(AuthenticateStudent::class)->group(function () {
        Route::get('dashboard', Dashboard::class)->name('dashboard');
    });
});

require __DIR__.'/settings.php';
