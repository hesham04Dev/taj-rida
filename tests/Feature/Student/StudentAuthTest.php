<?php

use App\Livewire\Student\Login;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

// ── Helpers ──────────────────────────────────────────────────────────────────

function createTeacher(): User
{
    return User::factory()->create(['role' => 'teacher']);
}

function createStudentWithCode(string $code = 'TEST123'): Student
{
    $teacher = createTeacher();

    return Student::factory()->create([
        'teacher_id' => $teacher->id,
        'access_code' => $code,
    ]);
}

// ── Login page ────────────────────────────────────────────────────────────────

it('shows the student login page', function (): void {
    get(route('student.login'))->assertOk();
});

it('renders the Login Livewire component on login page', function (): void {
    get(route('student.login'))
        ->assertSeeLivewire(Login::class);
});

// ── Unauthenticated access ────────────────────────────────────────────────────

it('redirects unauthenticated students away from the dashboard', function (): void {
    get(route('student.dashboard'))
        ->assertRedirect(route('student.login'));
});

// ── Login with valid code ─────────────────────────────────────────────────────

it('logs in a student with a valid access code', function (): void {
    $student = createStudentWithCode('ABC999');

    Livewire::test(Login::class)
        ->set('access_code', 'ABC999')
        ->call('submit')
        ->assertRedirect(route('student.dashboard'));

    expect(Auth::guard('student')->id())
        ->toBe($student->id);
});

// ── Login with invalid code ───────────────────────────────────────────────────

it('rejects an invalid access code', function (): void {
    createStudentWithCode('REAL_CODE');

    Livewire::test(Login::class)
        ->set('access_code', 'WRONG_CODE')
        ->call('submit')
        ->assertHasErrors(['access_code']);
});

it('shows a validation error when access code is empty', function (): void {
    Livewire::test(Login::class)
        ->set('access_code', '')
        ->call('submit')
        ->assertHasErrors(['access_code' => 'required']);
});

// ── Logout ────────────────────────────────────────────────────────────────────

it('logs out a student and redirects to login', function (): void {
    $student = createStudentWithCode('LOGOUT_CODE');

    Auth::guard('student')->login($student);

    post(route('student.logout'))
        ->assertRedirect(route('student.login'));

    expect(Auth::guard('student')->check())->toBeFalse();
});
