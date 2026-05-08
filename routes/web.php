<?php

use App\Http\Controllers\SuraReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/app');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::get('/sura-print-report', [SuraReportController::class, 'print'])
    ->name('sura.print.report');

require __DIR__.'/settings.php';
