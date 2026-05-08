<?php

use App\Models\PointTransaction;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->teacher = User::factory()->create(['role' => 'teacher']);
    $this->student = Student::factory()->create([
        'teacher_id' => $this->teacher->id,
        'given_points' => 0,
    ]);
});

it('defaults given_points to zero', function () {
    expect($this->student->given_points)->toBe(0);
});

it('computes total_points from point transactions', function () {
    PointTransaction::create([
        'student_id' => $this->student->id,
        'teacher_id' => $this->teacher->id,
        'amount' => 60,
        'reason' => 'test',
    ]);

    // Fresh load to avoid caching
    $student = Student::withoutGlobalScopes()->find($this->student->id);

    expect($student->total_points)->toBe(60);
});

it('computes remaining_points correctly', function () {
    PointTransaction::create([
        'student_id' => $this->student->id,
        'teacher_id' => $this->teacher->id,
        'amount' => 105,
        'reason' => 'test',
    ]);
    $this->student->update(['given_points' => 50]);

    $student = Student::withoutGlobalScopes()->find($this->student->id);

    expect($student->remaining_points)->toBe(55);
});

it('suggests the largest card denomination that fits within remaining balance', function () {
    // Remaining = 55 → largest card ≤ 55 is 50
    PointTransaction::create([
        'student_id' => $this->student->id,
        'teacher_id' => $this->teacher->id,
        'amount' => 105,
        'reason' => 'test',
    ]);
    $this->student->update(['given_points' => 50]);

    $student = Student::withoutGlobalScopes()->find($this->student->id);

    expect($student->suggested_card_value)->toBe(50);
});

it('suggests 100 when remaining is exactly 100', function () {
    PointTransaction::create([
        'student_id' => $this->student->id,
        'teacher_id' => $this->teacher->id,
        'amount' => 100,
        'reason' => 'test',
    ]);

    $student = Student::withoutGlobalScopes()->find($this->student->id);

    expect($student->suggested_card_value)->toBe(100);
});

it('suggests the smallest denomination when remaining is less than minimum card', function () {
    PointTransaction::create([
        'student_id' => $this->student->id,
        'teacher_id' => $this->teacher->id,
        'amount' => 10,
        'reason' => 'test',
    ]);

    $student = Student::withoutGlobalScopes()->find($this->student->id);

    // No card fits (10 < 15), falls back to smallest (15)
    expect($student->suggested_card_value)->toBe(15);
});

it('increments given_points correctly', function () {
    $this->student->increment('given_points', 50);

    expect($this->student->fresh()->given_points)->toBe(50);
});

it('has the correct card denominations constant', function () {
    expect(Student::CARD_DENOMINATIONS)->toBe([100, 50, 25, 15]);
});
