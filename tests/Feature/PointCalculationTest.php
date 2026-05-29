<?php

use App\Models\PageLog;
use App\Models\PointTransaction;
use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('calculates points correctly for recitation based on settings', function () {
    Setting::updateOrCreate(['key' => 'recitation_points_per_page'], ['value' => '10']);

    $teacher = User::factory()->create(['role' => 'teacher']);
    $student = Student::factory()->create([
        'teacher_id' => $teacher->id,
        'points_multiplier' => 1.5,
    ]);

    $this->actingAs($teacher);

    $pageLog = PageLog::create([
        'student_id' => $student->id,
        'type' => 'recitation',
        'from_page' => '1',
        'to_page' => 3.0,
        'count' => 2.0,
        'date' => now(),
    ]);

    $transaction = PointTransaction::where('student_id', $student->id)->orderBy('id', 'desc')->first();

    expect($transaction)->not->toBeNull()
        ->and($transaction->amount)->toBe(30)
        ->and($transaction->reason)->toContain('تسميع');
});
