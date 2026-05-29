<?php

use App\Livewire\Student\AttendanceLog;
use App\Livewire\Student\Dashboard;
use App\Livewire\Student\GradesCard;
use App\Livewire\Student\MemorizationTask;
use App\Livewire\Student\PointsCard;
use App\Livewire\Student\TeacherNotes;
use App\Models\Attendance;
use App\Models\Memorization;
use App\Models\PointTransaction;
use App\Models\Student;
use App\Models\StudentNote;
use App\Models\Sura;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

// ── Setup helper ──────────────────────────────────────────────────────────────

function loginStudent(): Student
{
    $teacher = User::factory()->create(['role' => 'teacher']);
    $student = Student::factory()->create([
        'teacher_id' => $teacher->id,
        'access_code' => 'DASH123',
    ]);
    Auth::guard('student')->login($student);

    return $student;
}

// ── Dashboard shell ───────────────────────────────────────────────────────────

it('loads the dashboard for an authenticated student', function (): void {
    $student = loginStudent();

    get(route('student.dashboard'))
        ->assertOk()
        ->assertSee($student->name)
        ->assertSeeLivewire(Dashboard::class);
});

// ── MemorizationTask ──────────────────────────────────────────────────────────

it('shows memorization tasks for the student', function (): void {
    $student = loginStudent();
    $sura = Sura::first() ?? Sura::factory()->create();

    Memorization::factory()->create([
        'student_id' => $student->id,
        'sura_id' => $sura->id,
        'is_need_rememorisation' => true,
        'is_need_revision' => false,
    ]);

    Livewire::test(MemorizationTask::class)
        ->assertSee($sura->name);
});

it('shows empty state when no memorization tasks exist', function (): void {
    loginStudent();

    Livewire::test(MemorizationTask::class)
        ->assertSee('لا توجد سور تحتاج مراجعة الآن');
});

// ── PointsCard ────────────────────────────────────────────────────────────────

it('shows the correct total points for the student', function (): void {
    $student = loginStudent();
    $teacher = User::find($student->teacher_id);

    PointTransaction::factory()->create([
        'student_id' => $student->id,
        'teacher_id' => $teacher->id,
        'amount' => 150,
    ]);

    PointTransaction::factory()->create([
        'student_id' => $student->id,
        'teacher_id' => $teacher->id,
        'amount' => 50,
    ]);

    Livewire::test(PointsCard::class)
        ->assertSet('totalPoints', 200);
});

// ── AttendanceLog ─────────────────────────────────────────────────────────────

it('shows attendance records for the student', function (): void {
    $student = loginStudent();

    Attendance::factory()->create([
        'student_id' => $student->id,
        'is_present' => true,
        'date' => now()->subDays(1),
    ]);

    Livewire::test(AttendanceLog::class)
        ->assertSet('presentCount', 1)
        ->assertSet('absentCount', 0);
});

// ── TeacherNotes ──────────────────────────────────────────────────────────────

it('shows the latest teacher notes for the student', function (): void {
    $student = loginStudent();

    StudentNote::factory()->create([
        'student_id' => $student->id,
        'description' => 'أداء ممتاز هذا الأسبوع',
        'date' => now(),
    ]);

    Livewire::test(TeacherNotes::class)
        ->assertSee('أداء ممتاز هذا الأسبوع');
});

// ── GradesCard ────────────────────────────────────────────────────────────────

it('shows memorization and revision grades for the student', function (): void {
    $student = loginStudent();
    $sura = Sura::first() ?? Sura::factory()->create();

    Memorization::factory()->create([
        'student_id' => $student->id,
        'sura_id' => $sura->id,
        'memorization_degree' => 'ممتاز',
        'revision_degree' => 'جيد',
    ]);

    Livewire::test(GradesCard::class)
        ->assertSee('ممتاز')
        ->assertSee('جيد');
});
